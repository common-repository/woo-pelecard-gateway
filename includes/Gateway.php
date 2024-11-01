<?php

namespace Pelecardwc;

use Pelecardwc\Traits\Singleton;
use WC_Order;
use WC_Payment_Gateway_CC;
use WC_Payment_Token_CC;
use WC_Payment_Tokens;
use WC_Subscription;

/**
 * Class Gateway
 */
class Gateway extends WC_Payment_Gateway_CC {

	use Singleton;
	public $saved_cards;
	public $action_type;
	protected $current_language;
	protected $total_payments = 1;

	private function __construct() {
		$this->id = 'wc-pelecard';  
		$this->icon = $this->get_icon();
		$this->title = $this->get_title();
		$this->has_fields = $this->get_has_fields();
		$this->method_title = $this->get_method_title();
		$this->method_description = $this->get_method_description();
		$this->order_button_text = $this->get_order_button_text();
		$this->saved_cards = $this->get_saved_cards();
		$this->supports = $this->get_supports();

		$this->set_action_type( $this->get_option( 'action_type', 'J4' ) );

		$this->init_form_fields();
		$this->init_settings();
		$this->register_hooks();
		$this->set_language();
	}

	public function get_icon(): string {
		$this->icon = apply_filters( 'wppc/settings/icon', $this->get_option( 'icon' ), $this );

		return parent::get_icon();
	}

	public function get_option( $key, $empty_value = null ) {
		$option = parent::get_option( $key, $empty_value );

		if ( $this->is_wcml_active() ) {
			$option = wpml_translate_single_string_filter(
				$option,
				\WCML_WC_Gateways::STRINGS_CONTEXT,
				$this->id . '_gateway_' . $key,
				$this->current_language
			);
		}

		$option = apply_filters( 'wppc/settings/option_' . $key, $option, $key, $this );

		return apply_filters( 'wppc/settings/option', $option, $key, $this );
	}

	public function get_title(): string {
		return apply_filters( 'wppc/settings/title', $this->get_option( 'title' ), $this );
	}

	private function get_has_fields(): bool {
		return apply_filters( 'wppc/settings/has_fields', true, $this );
	}

	public function get_method_title(): string {
		return apply_filters( 'wppc/settings/method_title', __( 'Pelecard gateway', 'wc-pelecard-gateway' ), $this );
	}

	public function get_method_description(): string {
		return apply_filters( 'wppc/settings/method_description', $this->get_option( 'description' ), $this );
	}

	private function get_order_button_text(): string {
		return apply_filters( 'wppc/settings/order_button_text', $this->get_option( 'order_button_text' ), $this );
	}

	private function get_saved_cards(): bool {
		$this->saved_cards = 'yes' === $this->get_option( 'saved_cards', 'yes' );

		return apply_filters( 'wppc/settings/saved_cards', $this->saved_cards, $this );
	}

	private function get_supports(): array {
		return apply_filters( 'wppc/supports', [
			'products',
			'refunds',
			'tokenization',
			'add_payment_method',
			'subscriptions',
			'subscription_cancellation',
			'subscription_suspension',
			'subscription_reactivation',
			'subscription_amount_changes',
			'subscription_date_changes',
			'subscription_payment_method_change',
			'subscription_payment_method_change_customer',
			'subscription_payment_method_change_admin',
			'multiple_subscriptions',
		], $this );
	}

	public function init_form_fields() {
		$this->form_fields = Settings::get_admin_fields();
	}

	public function register_hooks() {
		add_action( 'woocommerce_receipt_' . $this->id, [ $this, 'receipt_page' ] );
		add_action( 'woocommerce_api_' . $this->id, [ $this, 'check_ipn_response' ] );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
		add_action( 'woocommerce_scheduled_subscription_payment_' . $this->id, [
			$this,
			'scheduled_subscription_payment',
		], 10, 2 );
		add_action( 'woocommerce_subscription_failing_payment_method_updated_' . $this->id, [
			$this,
			'failing_payment_method_update',
		], 10, 2 );
		add_action( 'before_woocommerce_pay', [ $this, 'maybe_display_tokenization' ] );
		add_action( 'woocommerce_review_order_before_payment', [ $this, 'maybe_display_tokenization' ] );
		add_action( 'woocommerce_checkout_update_order_review', [ $this, 'maybe_display_tokenization' ] );
		add_filter( 'woocommerce_credit_card_type_labels', [ $this, 'get_credit_card_type_labels' ] );
		// WooCommerce Multilingual (WPML)
		add_filter( 'wcml_gateway_text_keys_to_translate', [ $this, 'wcml_translated_keys' ] );
		// Upay & J2 transactions
		add_filter( 'wppc/transaction/needs_validation', [ $this, 'skip_transaction_validation' ], 10, 2 );
		// Disable payments transaction for subscription orders
		/*add_filter( 'wppc/checkout/min_payments', [ $this, 'disable_payments_for_subscription' ] );
		add_filter( 'wppc/checkout/max_payments', [ $this, 'disable_payments_for_subscription' ] );*/
		
	}

	public function set_language() {
		global $sitepress;

		$this->current_language = is_callable( [
			$sitepress,
			'get_current_language',
		] ) ? $sitepress->get_current_language() : '';

		if ( 'all' === $this->current_language ) {
			$this->current_language = $sitepress->get_default_language();
		}
	}

	public function is_wcml_active() {
		return function_exists( 'wpml_translate_single_string_filter' ) && class_exists( '\WCML_WC_Gateways' );
	}

	public function generate_payment_range_html( $field ) {
		return Settings::generate_payment_range_html( $field, $this );
	}

	public function validate_payment_range_field( $key, $value ) {
		$value = (array) $value;

		foreach ( $value as $row => $range ) {
			if ( array_filter( $range ) !== $range ) {
				unset( $value[ $row ] );

				continue;
			}

			if ( $range['max_cart'] < $range['min_cart'] || $range['max_payments'] < $range['min_payments'] ) {
				unset( $value[ $row ] );
			}
		}

		return $value;
	}

	public function maybe_display_tokenization() {
		$display_tokenization = $this->get_saved_cards() || is_add_payment_method_page();

		$this->has_fields = apply_filters( 'wppc/settings/display_tokenization', $display_tokenization, $this );
	}
	public function check_if_exists_saved_card() {
		$exists_saved_card = $this->get_saved_cards();
		
		return $exists_saved_card;
	}

	public function skip_transaction_validation( bool $needs_validation, Transaction $transaction ): bool {
		if ( $transaction->is_action_type( $transaction::ACTION_TYPE_J2 ) ) {
			return false;
		}

		if ( 'yes' === $this->get_option( 'upay' ) ) {
			return false;
		}

		return $needs_validation;
	}

	public function disable_payments_for_subscription( int $payments ): int {
		return $this->cart_contains_subscription() ? 1 : $payments;
	}

	public function get_terminal( bool $hook = false ) {
		return ( $hook ? $this->get_option( 'hook_terminal' ) : null )
			?: $this->get_option( 'terminal' );
	}

	public function get_username( bool $hook = false ) {
		return ( $hook ? $this->get_option( 'hook_username' ) : null )
			?: $this->get_option( 'username' );
	}

	public function get_password( bool $hook = false ) {
		return ( $hook ? $this->get_option( 'hook_password' ) : null )
			?: $this->get_option( 'password' );
	}

	public function get_action_type() {
		return $this->action_type;
	}

	public function set_action_type( string $action_type ) {
		$this->action_type = $action_type;

		return $this;
	}

	public function wcml_translated_keys( $text_keys ) {
		if ( isset( $_REQUEST['section'] ) && $this->id === $_REQUEST['section'] ) {
			$form_fields = array_filter( $this->form_fields, function( $setting ) {
				return ! in_array( $setting['type'], [ 'title', 'payment_range' ] );
			} );

			$text_keys = array_keys( $form_fields );
		}

		return $text_keys;
	}

	public function failing_payment_method_update( WC_Subscription $subscription, WC_Order $renewal_order ) {
		$token_ids = $renewal_order->get_payment_tokens();
		$subscription->get_data_store()->update_payment_token_ids( $subscription, $token_ids );
	}

	public function form() {
		if ( ! is_add_payment_method_page() ) {
			return;
		}

		Log::info( 'MY ACCOUNT - ADD PAYMENT METHOD IFRAME' );

		$iframe_url = Api::get_my_account_iframe_url( $this );
		if ( is_wp_error( $iframe_url ) ) {
			wc_print_notice( $iframe_url->get_error_message(), 'error' );

			return;
		}

		wc_get_template( 'wppc-iframe.php', [
			'iframe_url' => $iframe_url,
		], null, Plugin::get_templates_path() );
	}

	public function save_payment_method_checkbox() {
		if ( $this->cart_contains_subscription() ) {
			return false;
		}

		if ( isset( $_GET['change_payment_method'] ) ) {
			return false;
		}

		parent::save_payment_method_checkbox();
	}

	public function cart_contains_subscription(): bool {
		if ( ! method_exists( '\WC_Subscriptions_Cart', 'cart_contains_subscription' ) ) {
			return false;
		}

		return \WC_Subscriptions_Cart::cart_contains_subscription();
	}



	public function get_credit_card_type_labels( array $labels ) {
		$labels = array_merge( $labels, apply_filters( 'wppc/credit_card_type_labels', [
			'maestro' => __( 'Maestro', 'wc-pelecard-gateway' ),
			'isracard' => __( 'Isracard', 'wc-pelecard-gateway' ),
			'leumi card' => __( 'Leumi Card', 'wc-pelecard-gateway' ),
		], $this ) );

		return $labels;
	}

	public function get_supported_cards() {
		$supported_cards = [
			'Amex' => false,
			'Diners' => false,
			'Isra' => false,
			'Master' => false,
			'Visa' => false,
		];

		$display_cards = (array) $this->get_option( 'supported_cards', [] );
		foreach ( array_keys( $supported_cards ) as $card ) {
			$supported_cards[ $card ] = in_array( $card, $display_cards );
		}

		return apply_filters( 'wppc/settings/supported_cards', $supported_cards, $display_cards, $this );
	}

	public function saved_payment_methods() {
		if ( empty( $this->get_tokens() ) ) {
			return;
		}
		parent::saved_payment_methods();
	}

	public function get_saved_payment_method_option_html( $token ) {
		$min_payments = $this->get_minimum_payments();
		$max_payments = $this->get_maximum_payments();

		$payments_template_html = '';

		if ( $this->is_subs_change_payment() ) {
			$subscription = wcs_get_subscription( absint( $_GET['change_payment_method'] ) );
			$subscription_payment_token = $this->get_order_payment_token( $subscription );
			$checked = $subscription_payment_token && $token->get_id() === $subscription_payment_token->get_id();
		} else {
			$checked = $token->is_default();

			if ( $max_payments > $min_payments || 1 > $min_payments ) {
				$payments_template_html = wc_get_template_html(
					'checkout/wppc-total-payments.php',
					[
						'min_credit' => $this->get_minimum_credit_payments(),
						'payments' => $this->get_total_payments_range(),
						'gateway' => $this,
						'token' => $token,
					],
					null,
					Plugin::get_templates_path()
				);
			}
		}

		return sprintf(
			'<li class="woocommerce-SavedPaymentMethods-token">
				<input class="woocommerce-SavedPaymentMethods-tokenInput" %4$s
					id="wc-%1$s-payment-token-%2$s"
					name="wc-%1$s-payment-token"
					style="width:auto;"
					type="radio"
					value="%2$s"
				/>
				<label for="wc-%1$s-payment-token-%2$s">%3$s</label>
				%5$s
			</li>',
			esc_attr( $this->id ),
			esc_attr( $token->get_id() ),
			esc_html( $token->get_display_name() ),
			checked( $checked, true, false ),
			$payments_template_html
		);
	}

	public function get_minimum_payments() {
		$min_payments = $this->get_option( 'min_payments', 1 );

		$total = $this->get_checkout_total();
		if ( $total > 0 ) {
			$custom_payment = $this->get_custom_payment( $total );
			if ( $custom_payment ) {
				$min_payments = $custom_payment['min_payments'];
			}
		}

		return apply_filters( 'wppc/checkout/min_payments', absint( $min_payments ) );
	}

	public function get_checkout_total() {
		$order = wc_get_order( absint( get_query_var( 'order-pay' ) ) );
		if ( $order ) {
			return $order->get_total() + $order->get_total_tax();
		}

		$total = WC()->cart->get_cart_contents_total() + WC()->cart->get_cart_contents_tax();

		return $total ? (float) $total : 0.0;
	}

	public function get_custom_payment( $total ) {
		$custom_payments = $this->get_option( 'payment_range', [] );

		return array_reduce( $custom_payments, function( $carry, $custom_payment ) use ( $total ) {
			return $custom_payment['min_cart'] <= $total && $total <= $custom_payment['max_cart']
				? $custom_payment
				: $carry;
		} );
	}

	public function get_maximum_payments() {
		$max_payments = $this->get_option( 'max_payments', 1 );

		$total = $this->get_checkout_total();
		if ( $total > 0 ) {
			$custom_payment = $this->get_custom_payment( $total );
			if ( $custom_payment ) {
				$max_payments = $custom_payment['max_payments'];
			}
		}

		return apply_filters( 'wppc/checkout/max_payments', absint( $max_payments ) );
	}

	public function is_subs_change_payment(): bool {
		return ( isset( $_GET['pay_for_order'] ) && isset( $_GET['change_payment_method'] ) );
	}

	public function get_order_payment_token( WC_Order $order ) {
		$token_ids = $order->get_payment_tokens();
		if ( empty( $token_ids ) ) {
			return false;
		}

		$token_id = array_pop( $token_ids );
		$token = WC_Payment_Tokens::get( $token_id );

		return $token ?? false;
	}

	public function get_minimum_credit_payments() {
		return absint( $this->get_option( 'min_credit', 1 ) );
	}

	public function get_total_payments_range() {
		return range( $this->get_minimum_payments(), $this->get_maximum_payments() );
	}

	public function get_new_payment_method_option_html() {
		if ( $this->is_subs_change_payment() ) {
			return '';
		}

		return parent::get_new_payment_method_option_html();
	}

	public function process_refund( $order_id, $amount = null, $reason = '' ) {

		global $wpdb;
		$woocommerce_payment_tokens    = $wpdb->prefix . 'woocommerce_payment_tokens';
		$woocommerce_payment_tokenmeta = $wpdb->prefix . 'woocommerce_payment_tokenmeta';
		$postmeta_table = $wpdb->prefix . 'postmeta';



		$order = wc_get_order( $order_id );

		$check_order_token_array = $order->get_meta('_transaction_data');

		

		$order_token = $check_order_token_array['Token'];
		
		$token = $this->get_order_payment_token( $order );
		

		$query_order_token = $wpdb->get_row( "SELECT * FROM ".$woocommerce_payment_tokens." WHERE token = '" . $order_token . "'");

		if ( !isset($query_order_token) || !$token ){
			Log::info( sprintf( 'token does not exists' ) );
			$order_last_four = substr( $check_order_token_array['CreditCardNumber'], -4 );
			$order_year_exp = substr( $check_order_token_array['CreditCardExpDate'], -2 );
			$order_month_exp = substr( $check_order_token_array['CreditCardExpDate'], 0, 2 );
			
			switch ($check_order_token_array['CreditType']) {
			    case '1':
			        $order_cc_type = 'mastercard';
			        break;
			    case '2':
			        $order_cc_type = 'visa';
			        break;
			    case '3':
			        $order_cc_type = 'maestro';
			        break;
			    case '5':
			        $order_cc_type = 'isracard';
			        break;
			}

			$order_user_id =  $order->user_id;

			Log::info( sprintf( 'order token #%d', $order_token ) );
			Log::info( sprintf( '4 last digits #%d', $order_last_four  ) );
			Log::info( sprintf( 'year #%d', $order_year_exp ) );
			Log::info( sprintf( 'month #%d', $order_month_exp ) );
			Log::info( sprintf( 'credit card type #%d', $order_cc_type ) );
			Log::info( sprintf( 'user id #%d', $order_user_id ) );
			

			$token = new \WC_Payment_Token_CC();
			$token->set_gateway_id( 'wc-pelecard' );
			$token->set_token( $order_token );
			$token->set_last4( $order_last_four );
			$token->set_card_type( $check_order_token_array['CreditType'] );
			$token->set_expiry_year( $order_year_exp );
			$token->set_expiry_month( $order_month_exp );
			$token->set_user_id( $order_user_id );

			Log::info( sprintf( 'ORDER #%d: START REFUND (DELETED TOKEN)', $order->get_id() ) );

			$order->set_total( -1 * abs( $amount ) );					

			$result = Api::charge_by_token_missed_order_tokens( $order, $this, $token );

			if ( is_wp_error( $result ) ) {
				throw new \Exception( $result->get_error_message(), $result->get_error_code() );
			}
			$transaction = ( new Transaction() )->set_data( $result );
			Log::info( sprintf(
				'ORDER #%d: REFUND %s (DELETED TOKEN)', $order->get_id(), $transaction->is_success() ? 'SUCCESS' : 'FAILED'
			) );

			if ( ! $transaction->is_success() ) {
				$error_message = sprintf(
					__( 'Transaction failed (%2$s): %1$s', 'wc-pelecard-gateway' ),
					$transaction->get_error_message(),
					$transaction->get_status_code()
				);

				$order->add_order_note( $error_message );
			}

			
			return $transaction->is_success();
			
		}

		

		Log::info( sprintf( 'ORDER #%d: START REFUND', $order->get_id() ) );

		try {

			$token = $this->get_order_payment_token( $order );

			if ( ! $token ) {
				$token = ( new Transaction( $order->get_transaction_id() ) )
					->get_token_object( $this );
			}

			$this->set_action_type( 'J4' );
			//$order->set_subtotal( abs($order->get_total()) + -1 * abs( $amount ) );
			$order->set_total( -1 * abs( $amount ) );

			// Prevent sending auth number in refund transactions
			$order->update_meta_data( '_wppc_auth_number', null );

			$result = Api::charge_by_token( $order, $this, $token );

			if ( is_wp_error( $result ) ) {
				throw new \Exception( $result->get_error_message(), $result->get_error_code() );
			}

			

			$transaction = ( new Transaction() )->set_data( $result );

			if ( ! $transaction->is_success() ) {
				$error_message = sprintf(
					__( 'Transaction failed (%2$s): %1$s', 'wc-pelecard-gateway' ),
					$transaction->get_error_message(),
					$transaction->get_status_code()
				);

				$order->add_order_note( $error_message );
			}
		} catch ( \Throwable $th ) {
			$error_message = sprintf(
				__( 'Refund failed (%2$s): %1$s', 'wc-pelecard-gateway' ),
				$th->getMessage(),
				$th->getCode()
			);

			$order->add_order_note( $error_message );
			$order->save();

			return false;
		}

		Log::info( sprintf(
			'ORDER #%d: REFUND %s', $order->get_id(), $transaction->is_success() ? 'SUCCESS' : 'FAILED'
		) );

		return $transaction->is_success();
	}

	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( $this->has_subscription( $order_id ) && $this->is_subs_change_payment() ) {
			return $this->change_subs_payment_method( $order_id );
		}

		if ( ! $order->needs_payment() ) {
			$order->payment_complete();
		}

		$save_payment_method = isset( $_POST[ 'wc-' . $this->id . '-new-payment-method' ] );
      
		$order->update_meta_data(
			'_wppc_save_payment_method',
			$save_payment_method || $this->has_subscription( $order->get_id() )
		);

		$order->save_meta_data();

		//3ds todo

		if ( $this->is_using_saved_payment_method() ) {
			$token = $this->get_validated_token();
			$this->validate_total_payments( $token );

			if ( $this->has_subscription( $order_id ) ) {
				$this->set_auth_number( $order, $token );
			}

			// Ensure 3D Secure parameters are included
	        $three_d_secure_params = Order::get_3ds_params($order);
	        if (!empty($three_d_secure_params)) {
	            $order->update_meta_data('_3ds_params', $three_d_secure_params);
	        }

			$this->charge_by_token( $order, $token );

			$next = $order->get_checkout_order_received_url();
		} else {
			$next = $order->get_checkout_payment_url( true );
		}

		return [
			'result' => 'success',
			'redirect' => $next,
		];
	}

	public function has_subscription( int $order_id ): bool {
		if ( ! function_exists( 'wcs_order_contains_subscription' ) ) {
			return false;
		}

		return wcs_order_contains_subscription( $order_id ) || wcs_is_subscription( $order_id ) || wcs_order_contains_renewal( $order_id );
	}

	public function change_subs_payment_method( int $order_id ) {
		$subscription = wc_get_order( $order_id );

		try {
			$new_token = $this->get_validated_token();

			$this->update_order_token_ids( $subscription, $new_token );
		} catch ( \Exception $e ) {
			Log::error( $e->getMessage() );

			$notice = __( 'There was an error with your request. Please try again.', 'wc-pelecard-gateway' );
			wc_add_notice( $notice, 'error' );
			wp_redirect( $subscription->get_view_order_url() );
			exit();
		}

		Log::info( sprintf( 'SUBSCRIPTION #%d: TOKEN UPDATED', $subscription->get_id() ) );
		Log::debug( $new_token->get_data() );

		return [
			'result' => 'success',
			'redirect' => $subscription->get_view_order_url(),
		];
	}

	public function get_validated_token() {
		$token_id = $this->get_checkout_payment_token();

		$token = WC_Payment_Tokens::get( $token_id );
		if ( ! $token || $token->get_user_id() !== get_current_user_id() ) {
			throw new \Exception( __( 'Invalid payment method.', 'wc-pelecard-gateway' ) );
		}

		return $token;
	}

	private function get_checkout_payment_token() {
      	$payment_token = $_POST[ 'wc-' . $this->id . '-payment-token' ] ?? 0;
      	//!!!!!!!
		//$payment_token = isset( $_POST[ 'wc-' . $this->id . '-payment-token' ] ) ? absint( wc_clean ( $_POST[ 'wc-' . $this->id . '-payment-token' ] ) ) : 0;
		//$payment_token = $_POST[ 'wc-' . $this->id . '-payment-token' ] ?? 0;

		/*if ( empty( $payment_token ) ):
			return;
		endif;

		return  $payment_token ;*/

		return absint( $payment_token );
	}

	public function update_order_token_ids( WC_Order $order, WC_Payment_Token_CC $token ) {
		$order->get_data_store()->update_payment_token_ids( $order, [ $token->get_id() ] );
	}

	private function is_using_saved_payment_method() {
		$payment_token = $this->get_checkout_payment_token();

		return $payment_token && 'new' !== $payment_token;
	}

	public function charge_by_token( WC_Order $order, WC_Payment_Token_CC $token ) {
		$result = Api::charge_by_token( $order, $this, $token );

		if ( is_wp_error( $result ) ) {
			throw new \Exception( $result->get_error_message() );
		}

		$transaction = ( new Transaction() )
			->set_validate( false )
			->set_data( $result );

		if ( ! $transaction->get_order_id() ) {
			$transaction->set_order_id( $order->get_id() );
		}

		$this->update_order_payment_token( $order, $token );

		return $this->do_payment( $transaction );
	}

	public function update_order_payment_token( WC_Order $order, WC_Payment_Token_CC $token ) {
		$this->update_order_token_ids( $order, $token );

		$subscriptions = $this->get_subscriptions_for_order( $order );
		foreach ( $subscriptions as $subscription ) {
			$this->update_order_token_ids( $subscription, $token );
		}
	}

	public function get_subscriptions_for_order( WC_Order $order ) {
		if ( ! function_exists( 'wcs_get_subscriptions_for_order' ) ) {
			return [];
		}

		return wcs_get_subscriptions_for_order( $order, [ 'order_type' => 'any' ] );
	}

	public function do_payment( Transaction $transaction ): bool {
		if ( $transaction->needs_validation() && ! $transaction->validate() ) {
			Log::info( sprintf( 'INVALID TRANSACTION: %s', $transaction->get_id() ) );

			return false;
		}

		$order = $transaction->get_order();
		if ( ! $order ) {
			Log::info( sprintf( 'TRANSACTION WITHOUT ORDER: %s', $transaction->get_id() ) );

			return false;
		}

		$transaction->save();

		if ( ! $order->needs_payment() ) {
			Log::info( sprintf( 'ORDER #%d: ALREADY PAID', $order->get_id() ) );

			return false;
		}

		if ( ! $transaction->is_success() ) {
			$error_message = sprintf(
				__( 'Transaction failed (%2$s): %1$s', 'wc-pelecard-gateway' ),
				$transaction->get_error_message(),
				$transaction->get_status_code()
			);

			$order->add_order_note( $error_message );

			Log::info( sprintf( 'ORDER #%d: PAYMENT FAILED', $order->get_id() ) );
			Log::debug( $transaction->get_error_message() );

			if ( ! $order->has_status( 'failed' ) ) {
				$order->update_status( 'failed' );
			}

			if ( defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
				throw new \Exception( ucfirst( $transaction->get_error_message() ) );
			}

			return false;
		}

		if ( $this->order_save_payment_method( $order ) ) {
			$this->maybe_save_payment_method( $transaction, $order );
		}

		if ( 'J5' === $this->get_action_type() && ! $this->has_subscription( $order->get_id() ) ) {
			$this->order_mark_as_chargeable( $order );
			$this->order_save_auth_number( $order, $transaction );
			$this->order_save_total_payments( $order, $transaction );

			$three_d_secure_params = $transaction->get_3ds_params();
			if ( ! empty( $three_d_secure_params ) ) {
				$this->order_save_3ds_params( $order, $three_d_secure_params );
			}
		}

		unset( WC()->session->total_payments );

		Log::info( sprintf( 'ORDER #%d: PAYMENT COMPLETE', $order->get_id() ) );

		return $order->payment_complete( $transaction->get_id() );
	}

	public function order_mark_as_chargeable( WC_Order $order ) {
		$order->update_meta_data( '_wppc_is_chargeable', true );
		$order->save_meta_data();
	}

	public function order_save_3ds_params( WC_Order $order, array $params ) {
		$order->update_meta_data( '_wppc_3ds_eci', $params[0] );
		$order->update_meta_data( '_wppc_3ds_xid', $params[1] );
		$order->update_meta_data( '_wppc_3ds_cavv', $params[2] );

		$order->save_meta_data();
	}

	public static function is_order_chargeable( WC_Order $order ) {
		return (bool) $order->get_meta( '_wppc_is_chargeable' );
	}

	public function order_save_payment_method( WC_Order $order ): bool {
		return (bool) $order->get_meta( '_wppc_save_payment_method' );
	}

	public function maybe_save_payment_method( Transaction $transaction, WC_Order $order ) {
		if ( ! $transaction->is_token_valid() ) {
			return false;
		}

		$token = $this->save_token( $transaction );
		$this->update_order_payment_token( $order, $token );

		return $token;
	}

	public function save_token( Transaction $transaction ) {
		$token = $transaction->get_token_object( $this );

		try {
			$token->save();
		} catch ( \Exception $e ) {
			Log::error( $e->getMessage() );
		}

		return $token;
	}

	public function order_save_auth_number( WC_Order $order, Transaction $transaction ) {
		$auth_number = $transaction->get_debit_approve_number();

		$order->update_meta_data( '_wppc_auth_number', $auth_number );
		$order->save_meta_data();

		$subscriptions = $this->get_subscriptions_for_order( $order );
		foreach ( $subscriptions as $subscription ) {
			$subscription->update_meta_data( '_wppc_auth_number', $auth_number );
			$subscription->update_meta_data( '_wppc_authorized', 'yes' );
			$subscription->save_meta_data();
		}
	}

	public function order_save_total_payments( WC_Order $order, Transaction $transaction ) {
		$order->update_meta_data( '_wppc_total_payments', $transaction->get_total_payments() );
		$order->save_meta_data();
	}

	public function set_auth_number( WC_Order $order, WC_Payment_Token_CC $token ) {
		$original_action_type = $this->get_action_type();
		$this->set_action_type( 'J5' );

		Log::info( 'START AUTH PAYMENT' );

		try {
			$fake_order = clone $order;
			$fake_order->set_id( 0 );
			$fake_order->set_total( 1 );

			$result = Api::charge_by_token( $fake_order, $this, $token );

			if ( is_wp_error( $result ) ) {
				throw new \Exception( $result->get_error_message(), $result->get_error_code() );
			}

			$transaction = ( new Transaction() )->set_data( $result );
			if ( ! $transaction->is_success() ) {
                throw new \Exception( ucfirst( $transaction->get_error_message() ) );
            }

			$this->order_save_auth_number( $order, $transaction );

			Log::info( 'AUTH PAYMENT COMPLETE' );
		} catch ( \Exception $e ) {
			Log::error( sprintf( 'AUTH PAYMENT FAILED: %s', $e->getMessage() ) );

			throw $e;
		} finally {
			$this->set_action_type( $original_action_type );
		}

		return $this;
	}

	public function validate_total_payments( WC_Payment_Token_CC $token ) {
      	$total_payments = $_POST[ 'wc-' . $this->id . '-total-payments' ][ $token->get_id() ] ?? 1;
      	//!!!!!
		//$total_payments = isset($_POST[ 'wc-' . $this->id . '-total-payments' ][ $token->get_id() ]) ? absint ( $_POST[ 'wc-' . $this->id . '-total-payments' ][ $token->get_id() ] ) : 1;
		if ( ! $total_payments || ! in_array( $total_payments, $this->get_total_payments_range() ) ) {
			throw new \Exception( __( 'Please select number of payments.', 'wc-pelecard-gateway' ) );
		}

		WC()->session->set( 'total_payments', $total_payments );

		return $this;
	}

	public function get_user_nonce( int $user_id = 0 ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		return wp_hash( wp_nonce_tick() . '|' . $user_id );
	}

	public function charge_by_order() {
		check_ajax_referer( 'order-item', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		/*if ( empty($_POST['order_id']) ) :
			return;
		endif;*/

		$order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
		$order = wc_get_order( $order_id );

		if ( ! self::is_order_chargeable( $order ) ) {
			wp_die( -1 );
		}

		$token = $this->get_order_payment_token( $order );
		if ( ! $token ) {
			$token = ( new Transaction( $order->get_transaction_id() ) )
				->get_token_object( $this );

			$token->set_user_id( 0 );
			$token->save();
			$this->update_order_token_ids( $order, $token );
		}


		$this->set_action_type( 'J4' );
		$this->set_total_payments( self::get_order_total_payments( $order ) );

		try {
			Log::info( sprintf( 'ORDER #%d: START CHARGE', $order->get_id() ) );

			$result = Api::charge_by_token( $order, $this, $token );

			if ( is_wp_error( $result ) ) {
				throw new \Exception( $result->get_error_message() );
			}

			$transaction = ( new Transaction() )
				->set_validate( false )
				->set_data( $result );

			$this->do_payment( $transaction );
		} catch ( \Exception $e ) {
			wp_send_json_error( [ 'error' => $e->getMessage() ] );
		}

		$order->delete_meta_data( '_wppc_is_chargeable' );
		$order->save_meta_data();

		wp_send_json_success();
	}

	public static function get_order_total_payments( \WC_Order $order ): int {
		$total_payments = (int) $order->get_meta( '_wppc_total_payments' );

		return $total_payments ?: 1;
	}

	public function get_total_payments(): int {
		if ( is_admin() || null === WC()->session ) {
			return $this->total_payments;
		}

		return (int) WC()->session->get( 'total_payments' );
	}

	public function set_total_payments( int $total_payments ) {
		$this->total_payments = $total_payments;

		return $this;
	}

	public function receipt_page( int $order_id ) {
		$order = wc_get_order( $order_id );
		
		// use Pelecardwc\CheckoutIframe;
		$iframe_url = Api::get_checkout_iframe_url( $order, $this );
		if ( is_wp_error( $iframe_url ) ) {
			return wc_print_notice( $iframe_url->get_error_message(), 'error' );
		}

		wc_get_template( 'wppc-iframe.php', [
			'iframe_url' => $iframe_url,
		], null, Plugin::get_templates_path() );
	}

	public function check_ipn_response() {
		$http_post_data = json_decode( file_get_contents( 'php://input' ), true );

		$transaction_id = $http_post_data['ResultData']['TransactionId'] ?? null;
		if ( empty( $transaction_id ) ) {
			return;
		}

		$transaction = new Transaction( $transaction_id );

		Log::info( 'PROCESS IPN: START' );
		Log::debug( $transaction );

		try {
			$this->do_payment( $transaction );
			Log::info( 'PROCESS IPN: SUCCESS' );
		} catch ( \Exception $e ) {
			Log::error( 'PROCESS IPN: FAILED' );
			Log::debug( $e->getMessage() );
		}

		status_header( 200 );
		exit();
	}

	public function maybe_process_redirect_order() {
		if ( empty( $_POST['PelecardTransactionId'] ) ) {
			return;
		}

      	$transaction_id = wc_clean( $_POST['PelecardTransactionId'] );
      	//!!!!
		//$transaction_id = isset( $_POST['PelecardTransactionId'] ) ? wc_clean( $_POST['PelecardTransactionId'] ) : 0;
		$transaction = new Transaction( $transaction_id );

		// Timeout
		if ( $transaction->is_timeout() ) {
			$order = $transaction->get_order();
			if ( $order ) {
				$error_message = sprintf(
					__( 'Transaction failed (%2$s): %1$s', 'wc-pelecard-gateway' ),
					$transaction->get_error_message(),
					$transaction->get_status_code()
				);

				$order->add_order_note( $error_message );
			}

			wp_redirect( $transaction->get_timeout_redirect_url() );
			exit();
		}

		$transaction->set_validate( ! $transaction->is_3ds_failure() );

		if ( is_add_payment_method_page() ) {
			return $this->add_payment_method( $transaction );
		}

		if ( is_order_received_page() ) {
			$order = $transaction->get_order();
			if ( ! $order || ! $order->needs_payment() ) {
				return;
			}

			$result = $this->do_payment( $transaction );

			// Remove cart.
			if ( $result && isset( WC()->cart ) ) {
				WC()->cart->empty_cart();
			}

			wp_redirect( $this->get_return_url( $order ) );
			exit();
		}
	}

	public function add_payment_method( Transaction $transaction = null ) {
		if ( empty( $transaction ) ) {
			wc_add_notice( __( 'Please use the payment button inside the form.', 'wc-pelecard-gateway' ), 'error' );

			wp_redirect( wc_get_endpoint_url( 'add-payment-method' ) );
			exit();
		}

		if ( ! $transaction->validate() ) {
			return;
		}

		if ( $transaction->is_success() ) {
			$this->save_token( $transaction );
			wc_add_notice( __( 'Payment method successfully added.', 'wc-pelecard-gateway' ) );
		} else {
			wc_add_notice( __( 'Unable to add payment method to your account.', 'wc-pelecard-gateway' ), 'error' );
		}

		$notices = WC()->session->get( 'wc_notices' );
		Session::instance()
			->set_customer_id( $transaction->get_user_id() )
			->init_current_session_data()
			->set_notices( $notices )
			->save_data();

		wp_redirect( wc_get_endpoint_url( 'payment-methods' ) );
		exit();
	}

	public function scheduled_subscription_payment( float $amount_to_charge, WC_Order $renewal_order ) {
		global $wpdb;

		$woocommerce_payment_tokens    = $wpdb->prefix . 'woocommerce_payment_tokens';
		$woocommerce_payment_tokenmeta = $wpdb->prefix . 'woocommerce_payment_tokenmeta';
		$postmeta_table = $wpdb->prefix . 'postmeta';

		$token = $this->get_order_payment_token( $renewal_order );

		$user_id_to_check = $renewal_order->get_user_id();

		Log::info( sprintf( 'RENEWAL ORDER PARAMETRS' ) );
		Log::debug( $user_id_to_check );
		$this->set_action_type( 'J4' );


		if ( ! $token || 'CC' !== $token->get_type() ) {

			Log::error(
			 	sprintf( 'SCHEDULED PAYMENT FAILED: NO VALID TOKEN FOR RENEWAL ORDER #%d TRYING TO GET VALID TOKEN...', $renewal_order->get_id() )
			);

			$query_order_token_query = $wpdb->get_results( "SELECT * FROM ".$woocommerce_payment_tokens." WHERE user_id = '".$user_id_to_check."' AND `gateway_id` = 'wc-pelecard'" );


			if ( !empty($query_order_token_query) ){
				$query_order_token = end($query_order_token_query);

				$token_id = $query_order_token->token_id;
				$order_token = $query_order_token->token;

				$order_last_four  = $wpdb->get_row( "SELECT * FROM ".$woocommerce_payment_tokenmeta." WHERE payment_token_id = '".$token_id."' AND `meta_key` = 'last4'" )->meta_value;
				$order_year_exp = $wpdb->get_row( "SELECT * FROM ".$woocommerce_payment_tokenmeta." WHERE payment_token_id = '".$token_id."' AND `meta_key` = 'expiry_year'" )->meta_value;
				$order_month_exp = $wpdb->get_row( "SELECT * FROM ".$woocommerce_payment_tokenmeta." WHERE payment_token_id = '".$token_id."' AND `meta_key` = 'expiry_month'" )->meta_value;
				$order_user_id = $user_id_to_check; 
				$order_cc_type = $wpdb->get_row( "SELECT * FROM ".$woocommerce_payment_tokenmeta." WHERE payment_token_id = '".$token_id."' AND `meta_key` = 'card_type'" )->meta_value;

				$token = new \WC_Payment_Token_CC();
				$token->set_gateway_id( 'wc-pelecard' );
				$token->set_token( $order_token );
				$token->set_last4( $order_last_four );
				$token->set_card_type( $order_cc_type );
				$token->set_expiry_year( $order_year_exp );
				$token->set_expiry_month( $order_month_exp );
				$token->set_user_id( $order_user_id );
			} else {
				Log::error(
				 	sprintf( 'SCHEDULED PAYMENT FAILED: INVALID TOKEN FOR ORDER #%d', $renewal_order->get_id() )
				);

				return false;
			}
			
		}

		try {
		    $has_auth_number = $this->has_auth_number( $renewal_order );
            if ( ! $has_auth_number ) {
                $this->set_auth_number( $renewal_order, $token );
            }

			$renewal_order->set_total( $amount_to_charge );
			///!!!!!
			//$this->charge_by_order( $renewal_order, $token );
			$this->charge_by_token( $renewal_order, $token );
		} catch ( \Exception $e ) {
			Log::error( sprintf( 'SCHEDULED PAYMENT FAILED: ORDER #%d', $renewal_order->get_id() ) );

            $renewal_order->update_status( 'failed', $e->getMessage() );
		}
	}

	public function has_auth_number( WC_Order $order ): bool {
		$subscriptions = $this->get_subscriptions_for_order( $order );

		return array_reduce( $subscriptions, function( $carry, $subscription ) {
			return $carry && 'yes' === $subscription->get_meta( '_wppc_authorized' );
		}, true );
	}

	public function get_timeout_url( WC_Order $order ) {
		$checkout_url = wc_get_checkout_url();

		if ( 'cancel' !== $this->get_option( 'timeout_action', 'cancel' ) ) {
			return $checkout_url;
		}

		return $order->get_cancel_order_url_raw( $checkout_url );
	}
}