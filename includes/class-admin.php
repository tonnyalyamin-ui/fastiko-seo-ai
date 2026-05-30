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

			<form method="post">
				<?php wp_nonce_field('fastiko_scan'); ?>

				<input
					type="submit"
					name="fastiko_run_scan"
					class="button button-primary"
					value="Run Scan">
			</form>

		</div>
		<?php

		if (
			isset($_POST['fastiko_run_scan']) &&
			check_admin_referer('fastiko_scan')
		) {
			Fastiko_SEO_AI_Scanner::instance()->scan_site();

			echo '<div class="notice notice-success"><p>Scan completed.</p></div>';
		}
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
}
