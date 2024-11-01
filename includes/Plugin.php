<?php

namespace Pelecardwc;

use DirectoryIterator;
use Pelecardwc\Traits\Singleton;
use ReflectionClass;
use ReflectionException;
include 'Api.php';
/**
 * Class Plugin
 */
class Plugin {

	use Singleton;

	public static $version = '1.0.0';

	private function __construct() {
		add_action( 'plugins_loaded', [ $this, 'register_hooks' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_front_scripts' ], 20 );
		//add_action( 'plugins_loaded', [ $this, 'check_for_update_data'] );
		add_action( 'admin_init', [ $this, 'check_for_update_data'] );
		add_action( 'wp_ajax_update_data', [ $this, 'update_data' ]);

		add_filter( 'woocommerce_payment_gateways', [ $this, 'add_payment_gateway' ], 10, 1 );

		Order::instance();
		Legacy::instance();

		$this->load( 'Invoices' );
	}

	public function update_data($update) {
		global $wpdb;
		$table = $wpdb->prefix . 'options';
		$id = Gateway::instance()->id;
		$option_name = $id.'_data-update';
		$settings_name = 'woocommerce_'.$id.'_settings';
		$old_settings_name = 'woocommerce_pelecard_settings';

		$table_payment_items = $wpdb->prefix . 'postmeta';
        $table_payment_token = $wpdb->prefix . 'woocommerce_payment_tokens';
		if ($update === 'true' || $update === true) {
			$query = $wpdb->get_row("SELECT * FROM $table WHERE `option_name` = '$old_settings_name'");
			//$query_payment_items = $wpdb->get_row("SELECT * FROM $table WHERE `meta_key` = 'pelecard'");
			$update = $wpdb->update($table, ['option_value' => $query->option_value], ['option_name' => $settings_name]);
			// if ($update) {
				$update_status = $wpdb->update($table, ['option_value' => 1], ['option_name' => $option_name]);
				$wpdb->update($table_payment_items, ['meta_value' => 'wc-pelecard'], ['meta_value' => 'pelecard']);
          		$wpdb->update($table_payment_token, ['gateway_id' => 'wc-pelecard'], ['gateway_id' => 'pelecard']);
          		$wpdb->update($table_payment_items, ['meta_key' => '_wppc_save_payment_method'], ['meta_key' => '_wpg_save_payment_method']);
          		$wpdb->update($table_payment_items, ['meta_key' => '_wppc_transaction'], ['meta_key' => '_wpg_transaction']);
          		$wpdb->update($table_payment_items, ['meta_key' => '_wppc_is_chargeable'], ['meta_key' => '_wpg_is_chargeable']);
          		$wpdb->update($table_payment_items, ['meta_key' => '_wppc_auth_number'], ['meta_key' => '_wpg_auth_number']);
          		$wpdb->update($table_payment_items, ['meta_key' => '_wppc_authorized'], ['meta_key' => '_wpg_authorized']);
          		$wpdb->update($table_payment_items, ['meta_key' => '_wppc_total_payments'], ['meta_key' => '_wpg_total_payments']);
			// }
			// wp_send_json($update);
		} else {
			$update_status = $wpdb->update($table, ['option_value' => 1], ['option_name' => $option_name]);
			// wp_send_json('not update');
		}

		

	}

	public function check_for_update_data() {
		$id = Gateway::instance()->id;
		$option_name = $id.'_data-update';
		global $wpdb;
		$table = $wpdb->prefix.'options';
		$query = $wpdb->get_row("SELECT * FROM $table WHERE `option_name` = '$option_name'");
		if (empty($query)) {
			$row_data = array(
				'option_name' => $option_name,
				'option_value' => 0,
				'autoload' => 'yes'
			);
			$query = $wpdb->insert($table, $row_data);
		}
		$old_settings_name = 'woocommerce_pelecard_settings';
		$query_settings = $wpdb->get_row("SELECT * FROM $table WHERE `option_name` = '$old_settings_name'");
		if (!$query->option_value && !empty($query_settings)) {
			$this->update_data(true);
			// add_action( 'admin_notices', array($this , 'update_data_alert') );
		}
	}

	// public function update_data_alert() {
	// 	$html = '<div class="notice notice-warning is-dismissible">
	// 		<p><b>Important</b>: you have some data from your previous Pelecard plugin.<br/>If you want to save data into new plugin, please submit.</p>
	// 		<button type="submit" class="button-primary woocommerce-save-button" id="pelecard_update_data">Submit</button>
	// 		<button type="button" class="button woocommerce-save-button" id="pelecard_dismiss">Cancel</button>
	// 		</div>'; 
	// 	$html .= "<script type='text/javascript'>
	// 		let $ = jQuery;
	// 		$('document').ready(function() {
	// 			$('#pelecard_update_data').click(function() {
	// 				event.preventDefault()
	// 				update_data(true)
	// 			})
	// 			$('#pelecard_dismiss').click(function() {
	// 				event.preventDefault()
	// 				update_data(false)
	// 			})
	// 		})

	// 		function update_data(update) {
	// 			let data = {
	// 				action: 'update_data',
	// 				update: update
	// 			}
	// 			$.ajax({
	// 				type: 'POST',
	// 				url: ajaxurl,
	// 				data: data,
	// 				success: function(resp) {
	// 					document.location.reload()
	// 				}
	// 			})
	// 		}
	// 		</script>
	// 	";
	// 	print_r($html);
	// }

	public function load( string $directory ) {
		$iterator = new DirectoryIterator( __DIR__ . DIRECTORY_SEPARATOR . $directory );

		foreach ( $iterator as $file ) {
			if ( ! $file->isFile() ) {
				continue;
			}

			if ( 'php' !== ( $extension = $file->getExtension() ) ) {
				continue;
			}

			$basename = $file->getBasename( '.' . $extension );
			$class = __NAMESPACE__ . '\\' . $directory . '\\' . $basename;

			if ( ! $this->class_has_method( $class, 'instance' ) ) {
				continue;
			}

			$class::instance();
		}
	}

	public function class_has_method( string $class, string $method ): bool {
		try {
			return ( new ReflectionClass( $class ) )->hasMethod( $method );
		} catch ( ReflectionException $e ) {
			return false;
		}
	}

	public static function get_templates_path(): string {
		return self::get_directory_path() . '/templates/';
	}

	public static function get_directory_path(): string {
		return untrailingslashit( plugin_dir_path( wppc_FILE ) );
	}

	public static function get_directory_url(): string {
		return untrailingslashit( plugin_dir_url( wppc_FILE ) );
	}

	public static function get_migrations_path( string $migration ): string {
		return self::get_directory_path() . '/migrations/' . $migration;
	}

	public function register_hooks() {
		add_action( 'wp', [ Gateway::instance(), 'maybe_process_redirect_order' ] );
	}

	public function add_payment_gateway( array $gateways ): array {
		$gateways[] = Gateway::instance();

		return $gateways;
	}

	public function enqueue_front_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script(
			'wppc-checkout',
			self::get_directory_url() . '/assets/js/checkout' . $suffix . '.js',
			[ 'jquery' ],
			self::$version
		);
	}
}