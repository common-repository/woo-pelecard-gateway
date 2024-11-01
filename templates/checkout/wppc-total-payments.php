<?php
/**
 * Add payments fieldset
 *
 * @var int                  $min_credit
 * @var array                $payments
 * @var \Pelecard\Gateway    $gateway
 * @var \WC_Payment_Token_CC $token
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/wppc-total-payments.php.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly
?>
<fieldset class="form-row woocommerce-TotalPayments" style="display: none;">
	<label class="screen-reader-text" for="wc-pelecard-number-of-payments">
		<?php _e( 'Number of payments', 'wc-pelecard-gateway' ); ?>
	</label>
	<select name="wc-<?php echo esc_attr( $gateway->id ); ?>-total-payments[<?php echo esc_attr( $token->get_id() ); ?>]" id="wc-pelecard-number-of-payments">
		<option value=""><?php _e( 'Number of payments', 'wc-pelecard-gateway' ); ?></option>
		<?php foreach ( $payments as $payment ) : ?>
			<option value="<?php echo esc_attr( $payment ); ?>">
				<?php echo ( $min_credit <= $payment ) ? sprintf( __( '%s (Credit)', 'wc-pelecard-gateway' ), $payment ) : sprintf( __( '%s', 'wc-pelecard-gateway' ), $payment ); ?>
			</option>
		<?php endforeach; ?>
	</select>
</fieldset>
