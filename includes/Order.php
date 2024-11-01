<?php

namespace Pelecardwc;

use Pelecardwc\Traits\Singleton;
use WC_Order;

class Order {

	use Singleton;

	private function __construct() {
		add_action( 'admin_init', [ $this, 'register_hooks' ] );
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ], 10, 2 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ], 20 );
		add_action( 'woocommerce_order_item_add_action_buttons', [ $this, 'render_charge_button' ] );
	}

	public function add_meta_boxes( $post_type, $post ) {
		if ( 'shop_order' !== $post_type ) {
			return;
		}

		$order = wc_get_order( $post );
		$transactions = $this->get_transactions( $order );

		if ( empty( $transactions ) ) {
			return;
		}

		add_meta_box(
			'wppc-transactions',
			__( 'Transactions', 'wc-pelecard-gateway' ),
			[ $this, 'render_transactions_metabox' ],
			$post_type,
			'advanced',
			'default',
			[ 'transactions' => $transactions ]
		);
	}

	// public function get_transactions( WC_Order $order ) {
	// 	$transactions = [];

	// 	foreach ( $order->get_meta( Transaction::META_KEY, false ) as $meta ) {
	// 		$transactions[] = ( new Transaction() )->set_json_data( $meta->get_data()['value'] );
	// 	}

	// 	return $transactions;
	// }

	// public function get_transactions( WC_Order $order ) {
	//     $transactions = [];
	//     $get_transaction = [];

	//     $all_meta = $order->get_meta( Transaction::META_KEY, false );

	//     foreach ( $all_meta as $meta ) {
	//     	Log::info('GetTransaction transactions');
	    	
	//         $transaction_data = $meta->get_data()['value'];
	//         $transaction = ( new Transaction() )->set_json_data( $transaction_data );
	//         Log::debug($transaction);
	//         // Check if is't GetTransaction
	//         if ( isset($transaction_data['is_get_transaction_response']) && $transaction_data['is_get_transaction_response'] ) {
	//             $get_transaction = [ $transaction ]; // Leave only last
	//         } else {
	//             $transactions[] = $transaction;
	//         }
	//     }

	//     // Log::info('GetTransaction transactions');
	//     // Log::debug($get_transaction);
	//     // Log::info('Other transactions');
	//     // Log::debug($transactions);

	//     // Merge all transactions with GetTransaction
	//     $transactions = array_merge($transactions, $get_transaction);

	//     return $transactions;
	// }

	// public function get_transactions( WC_Order $order ) {
	//     $transactions = [];
	//     $get_transaction = [];

	//     $all_meta = $order->get_meta( Transaction::META_KEY, false );

	//     foreach ( $all_meta as $meta ) {
	//         $transaction_data = $meta->get_data()['value'];
	//         $transaction = ( new Transaction() )->set_json_data( $transaction_data );

	//         // Extract transaction metadata
	//         $meta_data = $transaction->get_meta_data();
	//         $is_get_transaction = false;

	//         foreach ($meta_data as $meta_item) {
	//             if (is_object($meta_item) && get_class($meta_item) == 'WC_Meta_Data') {
	//                 $meta_item_data = $meta_item->get_data();
	//                 if (isset($meta_item_data['key']) && $meta_item_data['key'] === 'is_get_transaction_response' && isset($meta_item_data['value']) && $meta_item_data['value'] === '1') {
	//                     $is_get_transaction = true;
	//                     break;
	//                 }
	//             }
	//         }

	//         // Check if this is a GetTransaction
	//         if ( $is_get_transaction ) {
	//             $get_transaction = [ $transaction ]; // Reset all previous and keep only this one
	//         } else {
	//             $transactions[] = $transaction;
	//         }
	//     }

	//     Log::info('GetTransaction transactions');
	//     Log::debug($get_transaction);
	//     Log::info('Other transactions');
	// 	Log::debug($transactions);

	//     // Merge all transactions, keeping only the last one for GetTransaction
	//     $transactions = array_merge($transactions, $get_transaction);

	//     return $transactions;
	// }

	public function get_transactions( WC_Order $order ) {
	    $transactions = [];
	    $get_transaction = [];

	    $all_meta = $order->get_meta( Transaction::META_KEY, false );

	    foreach ( $all_meta as $meta ) {
	        $transaction_data = $meta->get_data()['value'];
	        $transaction = ( new Transaction() )->set_json_data( $transaction_data );

	        // Extract transaction metadata
	        $meta_data = $transaction->get_meta_data();
	        $is_get_transaction = false;

	        foreach ($meta_data as $meta_item) {
	            if (is_object($meta_item) && get_class($meta_item) == 'WC_Meta_Data') {
	                $meta_item_data = $meta_item->get_data();
	                if (isset($meta_item_data['key']) && $meta_item_data['key'] === 'is_get_transaction_response' && isset($meta_item_data['value']) && $meta_item_data['value'] === '1') {
	                    $is_get_transaction = true;
	                    break;
	                }
	            }
	        }

	        // Check if this is a GetTransaction
	        if ( $is_get_transaction ) {
	            $get_transaction = [ $transaction ]; // Reset all previous and keep only this one
	        } else {
	            $transactions[] = $transaction;
	        }
	    }

	    Log::info('GetTransaction transactions '.count($get_transaction));
	    // Log::debug($get_transaction);
	    Log::info('Other transactions '.count($transactions));
		// Log::debug($transactions);

	    // Merge all transactions, keeping only the last one for GetTransaction
	    $transactions = array_merge($transactions, $get_transaction);

	    return $transactions;
	}



	public function render_transactions_metabox( \WP_Post $post, array $metabox ) {
		wc_get_template(
			'order/wppc-transactions.php',
			[
				'transactions' => $metabox['args']['transactions'],
			],
			null,
			Plugin::get_templates_path()
		);
	}

	public function render_charge_button( WC_Order $order ) {
		$is_chargeable = Gateway::is_order_chargeable( $order );
		if ( ! $is_chargeable || ! current_user_can( 'edit_shop_orders' ) ) {
			return;
		}

		echo sprintf( '<button type="button" class="button btn--wppc-charge">%1$s</button>', esc_html__( 'Charge', 'wc-pelecard-gateway' ) );
	}

	public function enqueue_admin_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script(
			'wppc-order',
			Plugin::get_directory_url() . '/assets/js/admin/order' . $suffix . '.js',
			[ 'jquery', 'jquery-ui-accordion' ],
			Plugin::$version
		);

		wp_localize_script(
			'wppc-order',
			'wppc_i18n',
			[
				'do_charge' => __( 'Are you sure you wish to process this charge? This action cannot be undone.', 'wc-pelecard-gateway' ),
			]
		);
	}

	public function register_hooks() {
		add_action( 'wp_ajax_wppc_charge_order', [ Gateway::instance(), 'charge_by_order' ] );
	}

	public static function get_3ds_params( WC_Order $order ): array {
		$params = array_filter( [
			$order->get_meta( '_wppc_3ds_eci' ),
			$order->get_meta( '_wppc_3ds_xid' ),
			$order->get_meta( '_wppc_3ds_cavv' ),
		] );

		return 3 === count( $params ) ? $params : [];
	}
}
