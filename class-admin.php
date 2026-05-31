<?php

if (!defined('ABSPATH')) {
	exit;
}

class Fastiko_SEO_AI_Admin {
	private static ?self $instance = null;

	public static function instance(): self {
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		add_action('admin_menu', [$this, 'register_menu']);
		add_action('wp_ajax_fastiko_scan_batch', [$this, 'ajax_scan_batch']);
		add_action('wp_ajax_fastiko_scan_start', [$this, 'scan_start']);
		add_action('wp_ajax_fastiko_scan_pause', [$this, 'scan_pause']);
		add_action('wp_ajax_fastiko_scan_step',  [$this, 'scan_step']);
		add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);

		add_action('wp_ajax_fastiko_scan_start', [$this, 'ajax_scan_start']);

		add_action('wp_ajax_fastiko_scan_batch', [$this, 'ajax_scan_batch']);
	}


	public function ajax_scan_start(): void {
		check_ajax_referer('fastiko_scan');

		update_option(
			'fastiko_scan_offset',
			0,
			false
		);

		$total = Fastiko_SEO_AI_Scanner::instance()
			->get_total_posts();

		wp_send_json_success([
			'total' => $total
		]);
	}



	public function enqueue_assets(): void {
		wp_enqueue_script(
			'fastiko-scan-controller',
			FASTIKO_SEO_AI_URL . 'scan-controller.js',
			['jquery'],
			FASTIKO_SEO_AI_VERSION,
			true
		);

		wp_localize_script('fastiko-scan-controller', 'fastikoScan', [
			'nonce' => wp_create_nonce('fastiko_scan')
		]);
	}

	public function scan_start(): void {
		check_ajax_referer('fastiko_scan');

		update_option('fastiko_scan_state', [
			'offset' => 0,
			'running' => true
		], false);

		wp_send_json_success(['status' => 'started']);
	}

	public function scan_pause(): void {
		check_ajax_referer('fastiko_scan');

		$state = get_option('fastiko_scan_state', []);

		$state['running'] = false;

		update_option('fastiko_scan_state', $state, false);

		wp_send_json_success(['status' => 'paused']);
	}
	public function scan_step(): void {
		check_ajax_referer('fastiko_scan');

		$result = Fastiko_SEO_AI_Scanner::instance()
			->scan_batch(10);

		wp_send_json_success($result);
	}


	public function ajax_scan_batch(): void {
		check_ajax_referer('fastiko_scan');

		$offset = isset($_POST['offset']) ? (int) $_POST['offset'] : 0;

		$result = Fastiko_SEO_AI_Scanner::instance()
			->scan_batch($offset, 10);

		wp_send_json_success($result);
	}

	public function register_menu(): void {
		add_menu_page(
			'Fastiko SEO AI',
			'Fastiko SEO AI',
			'manage_options',
			'fastiko-seo-ai',
			[$this, 'dashboard'],
			'dashicons-chart-area',
			30
		);

		add_submenu_page(
			'fastiko-seo-ai',
			'Settings',
			'Settings',
			'manage_options',
			'fastiko-seo-settings',
			[$this, 'settings_page']
		);

		add_submenu_page(
			'fastiko-seo-ai',
			'Scan Results',
			'Scan Results',
			'manage_options',
			'fastiko-seo-results',
			[$this, 'results_page']
		);

		add_submenu_page(
			'fastiko-seo-ai',
			'Recommendations',
			'Recommendations',
			'manage_options',
			'fastiko-seo-recommendations',
			[$this, 'recommendations_page']
		);

		add_submenu_page(
			'fastiko-seo-ai',
			'Queue',
			'Queue',
			'manage_options',
			'fastiko-seo-queue',
			[$this, 'queue_page']
		);

		add_submenu_page(
			'fastiko-seo-ai',
			'Reports',
			'Reports',
			'manage_options',
			'fastiko-seo-reports',
			[$this, 'reports_page']
		);

		add_submenu_page(
			'fastiko-seo-ai',
			'Auto Builder',
			'Auto Builder',
			'manage_options',
			'fastiko-seo-auto-builder',
			[$this, 'auto_builder_page']
		);
	}

	public function dashboard(): void {
		global $wpdb;

		$table = $wpdb->prefix . 'fastiko_seo_pages';

		$count = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM {$table}"
		);

?>
		<div class="wrap">

			<h1>Fastiko SEO AI</h1>

			<p>
				Pages scanned:
				<strong><?php echo esc_html($count); ?></strong>
			</p>

		</div>

		<div style="margin-top:20px;max-width:98%;">
			<div style="background:#ffeb3b;height:20px;border-radius:10px;overflow:hidden;">
				<div id="fastiko-bar"
					style="width:0%;height:100%;background:#2271b1;"></div>
			</div>

			<p id="fastiko-status">Idle</p>

			<button class="button button-primary" onclick="startScan()">Start</button>
			<button class="button" onclick="pauseScan()">Pause</button>
		</div>

		<?php


	}

	public function settings_page(): void {
	?>
		<div class="wrap">

			<h1>Fastiko SEO AI Settings</h1>

			<form method="post" action="options.php">

				<?php
				settings_fields('fastiko_seo_ai_settings');
				?>

				<table class="form-table">

					<tr>
						<th>OpenAI API Key</th>
						<td>
							<input
								type="password"
								name="fastiko_openai_key"
								value="<?php echo esc_attr(get_option('fastiko_openai_key')); ?>"
								class="regular-text">
						</td>
					</tr>

					<tr>
						<th>Gemini API Key</th>
						<td>
							<input
								type="password"
								name="fastiko_gemini_key"
								value="<?php echo esc_attr(get_option('fastiko_gemini_key')); ?>"
								class="regular-text">
						</td>
					</tr>

					<tr>
						<th>Default Provider</th>
						<td>

							<select name="fastiko_default_provider">

								<option value="openai"
									<?php selected(
										get_option('fastiko_default_provider'),
										'openai'
									); ?>>

									OpenAI

								</option>

								<option value="gemini"
									<?php selected(
										get_option('fastiko_default_provider'),
										'gemini'
									); ?>>

									Gemini

								</option>

							</select>

						</td>
					</tr>

				</table>

				<?php submit_button(); ?>

			</form>

		</div>
	<?php
	}



	public function results_page(): void {
		global $wpdb;

		$table = $wpdb->prefix . 'fastiko_seo_pages';

		$rows = $wpdb->get_results(
			"SELECT *
         FROM {$table}
         ORDER BY scanned_at DESC
         LIMIT 100"
		);

		echo '<div class="wrap">';
		echo '<h1>Scan Results</h1>';

		echo '<table class="widefat">';

		echo '
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Score</th>
        <th>Scanned</th>
    </tr>';

		foreach ($rows as $row) {

			echo '<tr>';

			echo '<td>' . intval($row->object_id) . '</td>';

			echo '<td>' . esc_html($row->title) . '</td>';

			echo '<td>' . intval($row->seo_score) . '</td>';

			echo '<td>' . esc_html($row->scanned_at) . '</td>';

			echo '</tr>';
		}

		echo '</table>';

		echo '</div>';
	}




	public function recommendations_page(): void {
		$service = Fastiko_SEO_AI_Recommendations_Service::instance();

		$items = $service->get_list(50);

		echo '<div class="wrap">';
		echo '<h1>SEO Recommendations</h1>';

		echo '<table class="widefat striped">';
		echo '<tr>
            <th>ID</th>
            <th>Page</th>
            <th>Title</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>';

		foreach ($items as $item) {

			$post = get_post($item['page_id']);

			echo '<tr>';

			echo '<td>' . esc_html($item['id']) . '</td>';
			echo '<td>' . esc_html($post ? $post->post_title : '-') . '</td>';
			echo '<td>' . esc_html($item['title']) . '</td>';
			echo '<td>' . esc_html($item['status']) . '</td>';

			echo '<td>';

			echo '<a href="?page=fastiko-seo-recommendations&apply=' . intval($item['id']) . '">Apply</a> | ';
			echo '<a href="?page=fastiko-seo-recommendations&ignore=' . intval($item['id']) . '">Ignore</a> | ';
			echo '<a href="?page=fastiko-seo-recommendations&regen=' . intval($item['page_id']) . '">Regenerate</a>';

			echo '</td>';

			echo '</tr>';
		}

		echo '</table>';
		echo '</div>';

		// actions
		if (isset($_GET['apply'])) {
			$service->apply((int) $_GET['apply']);
		}

		if (isset($_GET['ignore'])) {
			$service->ignore((int) $_GET['ignore']);
		}

		if (isset($_GET['regen'])) {
			$service->regenerate((int) $_GET['regen']);
		}
	}


	public function queue_page(): void {
		echo '<div class="wrap">';
		echo '<h1>Queue</h1>';
		echo '<p>No tasks yet.</p>';
		echo '</div>';
	}


	public function reports_page(): void {
		echo '<div class="wrap">';
		echo '<h1>Reports</h1>';
		echo '<p>Reports module coming soon.</p>';
		echo '</div>';
	}



	public function auto_builder_page(): void {
		if (isset($_POST['generate_cities'])) {

			$cities = explode("\n", sanitize_textarea_field($_POST['cities']));

			$builder = Fastiko_SEO_AI_Auto_Page_Builder::instance();

			$created = $builder->generate_bulk($cities);

			echo '<div class="notice notice-success"><p>Created pages: ' . count($created) . '</p></div>';
		}

	?>
		<div class="wrap">
			<h1>Auto SEO Page Builder</h1>

			<form method="post">
				<textarea name="cities" rows="10" style="width:400px"
					placeholder="Melbourne
Sydney
Brisbane"></textarea>

				<br><br>

				<button class="button button-primary" name="generate_cities">
					Generate Pages
				</button>
			</form>
		</div>
<?php
	}
}
