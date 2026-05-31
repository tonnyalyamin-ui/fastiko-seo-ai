<?php

if (!defined('ABSPATH')) {
	exit;
}

class Fastiko_SEO_AI_Scanner {
	private static ?self $instance = null;

	public static function instance(): self {
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function scan_posts(): void {
		global $wpdb;

		$table = $wpdb->prefix . 'fastiko_seo_pages';

		$posts = get_posts([
			'post_type' => 'any',
			'post_status' => 'publish',
			'posts_per_page' => -1
		]);

		foreach ($posts as $post) {

			$metrics = $this->analyze_post($post);

			$score = Fastiko_SEO_AI_Auditor::instance()
				->calculate_score($post);

			$wpdb->replace(
				$table,
				[
					'object_id' => $post->ID,
					'object_type' => $post->post_type,
					'url' => get_permalink($post),
					'title' => get_the_title($post),
					'seo_score' => $score,
					'word_count' => $metrics['word_count'],
					'h1_count' => $metrics['h1_count'],
					'h2_count' => $metrics['h2_count'],
					'images_count' => $metrics['images_count'],
					'internal_links_count' => $metrics['internal_links_count'],
					'scanned_at' => current_time('mysql')
				]
			);
		}
	}


	public function scan_batch(int $limit = 10): array {
		$state = $this->get_state();

		if (!$state['running']) {
			return [
				'done' => true,
				'paused' => true
			];
		}

		$offset = (int) $state['offset'];

		$posts = get_posts([
			'post_type' => 'any',
			'post_status' => 'publish',
			'posts_per_page' => $limit,
			'offset' => $offset,
		]);

		global $wpdb;

		foreach ($posts as $post) {

			$score = Fastiko_SEO_AI_Auditor::instance()
				->calculate_score($post);

			$wpdb->replace(
				$wpdb->prefix . 'fastiko_seo_pages',
				[
					'object_id' => $post->ID,
					'object_type' => $post->post_type,
					'url' => get_permalink($post),
					'title' => get_the_title($post),
					'seo_score' => $score,
					'scanned_at' => current_time('mysql')
				]
			);
		}

		$new_offset = $offset + count($posts);

		$this->set_state([
			'offset' => $new_offset,
			'running' => true
		]);

		return [
			'processed' => count($posts),
			'offset' => $new_offset,
			'done' => count($posts) < $limit
		];
	}


	private function analyze_post(WP_Post $post): array {
		$content = $post->post_content;

		return [
			'word_count' => str_word_count(
				wp_strip_all_tags($content)
			),

			'h1_count' => preg_match_all(
				'/<h1/i',
				$content
			),

			'h2_count' => preg_match_all(
				'/<h2/i',
				$content
			),

			'images_count' => preg_match_all(
				'/<img/i',
				$content
			),

			'internal_links_count' => preg_match_all(
				'/href=/i',
				$content
			)
		];
	}


	private function get_state(): array {
		return get_option('fastiko_scan_state', [
			'offset' => 0,
			'running' => false
		]);
	}

	private function set_state(array $state): void {
		update_option('fastiko_scan_state', $state, false);
	}


	public function get_total_posts(): int {
		global $wpdb;

		return (int) $wpdb->get_var("
        SELECT COUNT(ID)
        FROM {$wpdb->posts}
        WHERE post_status='publish'
    ");
	}
}
