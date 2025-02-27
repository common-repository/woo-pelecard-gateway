<?php

namespace Pelecardwc;

/**
 * Class Settings
 */
class Settings {

	public static function get_admin_fields(): array {
		$settings = [
			'general_title' => [
				'title' => __( 'General', 'wc-pelecard-gateway' ),
				'type' => 'title',
			],
			'enabled' => [
				'title' => __( 'Enable/Disable', 'wc-pelecard-gateway' ),
				'type' => 'checkbox',
				'label' => __( 'Enable Pelecard', 'wc-pelecard-gateway' ),
				'default' => 'yes',
			],
			'language' => [
				'title' => __( 'IFrame Language', 'wc-pelecard-gateway' ),
				'type' => 'select',
				'class' => 'wc-enhanced-select',
				'description' => '',
				'default' => 'EN',
				'desc_tip' => false,
				'options' => [
					'HE' => __( 'Hebrew', 'wc-pelecard-gateway' ),
					'EN' => __( 'English', 'wc-pelecard-gateway' ),
					'RU' => __( 'Russian', 'wc-pelecard-gateway' ),
				],
			],
			'title' => [
				'title' => __( 'Title', 'wc-pelecard-gateway' ),
				'type' => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'wc-pelecard-gateway' ),
				'default' => __( 'Pay by credit card', 'wc-pelecard-gateway' ),
				'desc_tip' => true,
			],
			'description' => [
				'title' => __( 'Description', 'wc-pelecard-gateway' ),
				'type' => 'textarea',
				'description' => __( 'Payment method description that the customer will see on your checkout.', 'wc-pelecard-gateway' ),
				'default' => __( 'Pay by credit card', 'wc-pelecard-gateway' ),
				'desc_tip' => true,
			],
			'order_button_text' => [
				'title' => __( 'Order Button Text', 'wc-pelecard-gateway' ),
				'type' => 'text',
				'description' => __( 'Set if the place order button should be renamed on selection.', 'wc-pelecard-gateway' ),
				'default' => __( 'Pay by credit card', 'wc-pelecard-gateway' ),
				'desc_tip' => true,
			],
			'icon' => [
				'title' => __( 'Icon', 'wc-pelecard-gateway' ),
				'type' => 'url',
				'description' => __( 'This controls the gateway icon which the user sees during checkout.', 'wc-pelecard-gateway' ),
				'default' => '',
				'desc_tip' => true,
			],
			'saved_cards' => [
				'title' => __( 'Saved Cards', 'wc-pelecard-gateway' ),
				'label' => __( 'Enable Payment via Saved Cards', 'wc-pelecard-gateway' ),
				'type' => 'checkbox',
				'description' => __( 'If enabled, users will be able to pay with a saved card during checkout.', 'wc-pelecard-gateway' ),
				'default' => 'yes',
				'desc_tip' => true,
			],
			'upay' => [
				'title' => __( 'Upay', 'wc-pelecard-gateway' ),
				'label' => __( 'Enable Upay', 'wc-pelecard-gateway' ),
				'type' => 'checkbox',
				'description' => __( 'If enabled, transactions will not be validated.', 'wc-pelecard-gateway' ),
				'default' => 'no',
				'desc_tip' => true,
			],
			'timeout_action' => [
				'title' => __( 'Timeout Action', 'wc-pelecard-gateway' ),
				'type' => 'select',
				'class' => 'wc-enhanced-select',
				'description' => __( 'Where would the user be redirected to, in case the iframe has timed-out.', 'wc-pelecard-gateway' ),
				'default' => 'cancel',
				'desc_tip' => true,
				'options' => [
					'cancel' => __( 'Cancel Order', 'wc-pelecard-gateway' ),
					'checkout' => __( 'Return to Checkout', 'wc-pelecard-gateway' ),
				],
			],
			'terminal_title' => [
				'title' => __( 'Terminal number', 'wc-pelecard-gateway' ),
				'type' => 'title',
			],
			'terminal' => [
				'title' => __( 'Terminal number', 'wc-pelecard-gateway' ),
				'type' => 'text',
				'description' => '',
				'default' => '',
				'desc_tip' => false,
			],
			'username' => [
				'title' => __( 'User', 'wc-pelecard-gateway' ),
				'type' => 'text',
				'description' => '',
				'default' => '',
				'desc_tip' => false,
			],
			'password' => [
				'title' => __( 'Password', 'wc-pelecard-gateway' ),
				'type' => 'password',
				'description' => '',
				'default' => '',
				'desc_tip' => false,
			],
			'action_type' => [
				'title' => __( 'Action Type', 'wc-pelecard-gateway' ),
				'type' => 'select',
				'class' => 'wc-enhanced-select',
				'description' => '',
				'default' => 'J4',
				'desc_tip' => false,
				'options' => [
					'J2' => __( 'J2', 'wc-pelecard-gateway' ),
					'J4' => __( 'J4', 'wc-pelecard-gateway' ),
					'J5' => __( 'J5', 'wc-pelecard-gateway' ),
				],
			],
			'hook' => [
				'title' => __( 'sub terminal', 'wc-pelecard-gateway' ),
				'type' => 'title',
				'description' => __( 'Usually A second terminal is required to perform actions such as tokenizations, refunds, etc.', 'wc-pelecard-gateway' ),
				'default' => '',
				'desc_tip' => false,
			],
			'hook_terminal' => [
				'title' => __( 'Terminal', 'wc-pelecard-gateway' ),
				'type' => 'text',
				'description' => '',
				'default' => '',
				'desc_tip' => false,
			],
			'hook_username' => [
				'title' => __( 'Username', 'wc-pelecard-gateway' ),
				'type' => 'text',
				'description' => '',
				'default' => '',
				'desc_tip' => false,
			],
			'hook_password' => [
				'title' => __( 'Password', 'wc-pelecard-gateway' ),
				'type' => 'password',
				'description' => '',
				'default' => '',
				'desc_tip' => false,
			],
			'payments_title' => [
				'title' => __( 'Payments', 'wc-pelecard-gateway' ),
				'type' => 'title',
			],
			'min_payments' => [
				'title' => __( 'Min Payments', 'wc-pelecard-gateway' ),
				'type' => 'number',
				'description' => __( 'The amount of minimum payments.', 'wc-pelecard-gateway' ),
				'default' => '1',
				'desc_tip' => true,
				'custom_attributes' => [ 'min' => 1, 'required' => 'required' ],
			],
			'max_payments' => [
				'title' => __( 'Max Payments', 'wc-pelecard-gateway' ),
				'type' => 'number',
				'description' => __( 'The amount of maximum payments.', 'wc-pelecard-gateway' ),
				'default' => '12',
				'desc_tip' => true,
				'custom_attributes' => [ 'min' => 1, 'required' => 'required' ],
			],
			'min_credit' => [
				'title' => __( 'Min Payments For Credit', 'wc-pelecard-gateway' ),
				'type' => 'number',
				'description' => __( 'The amount of minimum payments required to define a credit transaction.', 'wc-pelecard-gateway' ),
				'default' => '13',
				'desc_tip' => true,
				'custom_attributes' => [ 'min' => 1, 'required' => 'required' ],
			],
			'payment_range' => [
				'type' => 'payment_range',
			],
			'fields_title' => [
				'title' => __( 'Fields', 'wc-pelecard-gateway' ),
				'type' => 'title',
			],
			'first_payment' => [
				'title' => __( 'First Payment', 'wc-pelecard-gateway' ),
				'type' => 'select',
				'class' => 'wc-enhanced-select',
				'description' => __( 'The initial amount should be less than the amount of the transaction.', 'wc-pelecard-gateway' ),
				'default' => 'auto',
				'desc_tip' => false,
				'options' => [
					'auto' => __( 'Auto', 'wc-pelecard-gateway' ),
					'manual' => __( 'Manual', 'wc-pelecard-gateway' ),
				],
			],
			'card_holder_name' => [
				'title' => __( 'Card Holder Name', 'wc-pelecard-gateway' ),
				'type' => 'select',
				'class' => 'wc-enhanced-select',
				'description' => '',
				'default' => 'hide',
				'desc_tip' => false,
				'options' => [
					'hide' => __( 'Disabled', 'wc-pelecard-gateway' ),
					'must' => __( 'Required', 'wc-pelecard-gateway' ),
					'optional' => __( 'Optional', 'wc-pelecard-gateway' ),
				],
			],
			'customer_id_field' => [
				'title' => __( 'Customer Id Field', 'wc-pelecard-gateway' ),
				'type' => 'select',
				'class' => 'wc-enhanced-select',
				'description' => '',
				'default' => 'hide',
				'desc_tip' => false,
				'options' => [
					'hide' => __( 'Disabled', 'wc-pelecard-gateway' ),
					'must' => __( 'Required', 'wc-pelecard-gateway' ),
					'optional' => __( 'Optional', 'wc-pelecard-gateway' ),
				],
			],
			'cvv2_field' => [
				'title' => __( 'Cvv2 Field', 'wc-pelecard-gateway' ),
				'type' => 'select',
				'class' => 'wc-enhanced-select',
				'description' => '',
				'default' => 'hide',
				'desc_tip' => false,
				'options' => [
					'hide' => __( 'Disabled', 'wc-pelecard-gateway' ),
					'must' => __( 'Required', 'wc-pelecard-gateway' ),
					'optional' => __( 'Optional', 'wc-pelecard-gateway' ),
				],
			],
			'email_field' => [
				'title' => __( 'Email Field', 'wc-pelecard-gateway' ),
				'type' => 'select',
				'class' => 'wc-enhanced-select',
				'description' => '',
				'default' => 'hide',
				'desc_tip' => false,
				'options' => [
					'hide' => __( 'Disabled', 'wc-pelecard-gateway' ),
					'must' => __( 'Required', 'wc-pelecard-gateway' ),
					'optional' => __( 'Optional', 'wc-pelecard-gateway' ),
					'value' => __( 'Required with value', 'wc-pelecard-gateway' ),
				],
			],
			'tel_field' => [
				'title' => __( 'Tel Field', 'wc-pelecard-gateway' ),
				'type' => 'select',
				'class' => 'wc-enhanced-select',
				'description' => '',
				'default' => 'hide',
				'desc_tip' => false,
				'options' => [
					'hide' => __( 'Disabled', 'wc-pelecard-gateway' ),
					'must' => __( 'Required', 'wc-pelecard-gateway' ),
					'optional' => __( 'Optional', 'wc-pelecard-gateway' ),
					'value' => __( 'Required with value', 'wc-pelecard-gateway' ),
				],
			],
			'split_cc_number' => [
				'class' => 'pelecard-tab',
				'title' => __( 'Split CC Number', 'wc-pelecard-gateway' ),
				'type' => 'checkbox',
				'description' => __( 'Card field is divided into 4 groups of 4 numbers.', 'wc-pelecard-gateway' ),
				'label' => __( 'Enabled', 'wc-pelecard-gateway' ),
				'default' => 'no',
				'desc_tip' => true,
			],
			'cancel_button' => [
				'class' => 'pelecard-tab',
				'title' => __( 'Cancel Button', 'wc-pelecard-gateway' ),
				'type' => 'checkbox',
				'description' => __( 'Cancel the order and redirect the buyer back to basket', 'wc-pelecard-gateway' ),
				'label' => __( 'Enabled', 'wc-pelecard-gateway' ),
				'default' => 'no',
				'desc_tip' => true,
			],
			'free_total' => [
				'class' => 'pelecard-tab',
				'title' => __( 'Free Total', 'wc-pelecard-gateway' ),
				'type' => 'checkbox',
				'description' => __( 'Editable field will be displayed next to the original amount field, and the customer can add the desired amount.', 'wc-pelecard-gateway' ),
				'label' => __( 'Enabled', 'wc-pelecard-gateway' ),
				'default' => 'no',
				'desc_tip' => true,
			],
			'confirmation_cb' => [
				'title' => __( 'Confirmation CheckBox', 'wc-pelecard-gateway' ),
				'type' => 'select',
				'class' => 'wc-enhanced-select',
				'description' => '',
				'default' => 'false',
				'desc_tip' => false,
				'options' => [
					'false' => __( 'Disabled', 'wc-pelecard-gateway' ),
					'true' => __( 'Enabled', 'wc-pelecard-gateway' ),
					'checked' => __( 'Checked', 'wc-pelecard-gateway' ),
				],
			],
			'confirmation_text' => [
				'title' => __( 'Confirmation Text', 'wc-pelecard-gateway' ),
				'type' => 'text',
				'description' => __( 'Free text presented to the customer when confirmation box is enabled.', 'wc-pelecard-gateway' ),
				'default' => '',
				'desc_tip' => true,
			],
			'confirmation_url' => [
				'title' => __( 'Confirmation Link', 'wc-pelecard-gateway' ),
				'type' => 'url',
				'placeholder' => 'http://',
				'description' => __( 'HyperLink address of Confirmation Text.', 'wc-pelecard-gateway' ),
				'label' => __( 'Enabled', 'wc-pelecard-gateway' ),
				'default' => '',
				'desc_tip' => true,
			],
			'top_text' => [
				'title' => __( 'Top Text', 'wc-pelecard-gateway' ),
				'type' => 'text',
				'description' => '',
				'default' => '',
				'desc_tip' => true,
			],
			'bottom_text' => [
				'title' => __( 'Bottom Text', 'wc-pelecard-gateway' ),
				'type' => 'text',
				'description' => '',
				'default' => '',
				'desc_tip' => true,
			],
			'supported_cards' => [
				'title' => __( 'Supported Cards', 'wc-pelecard-gateway' ),
				'type' => 'multiselect',
				'class' => 'wc-enhanced-select',
				'description' => __( 'Display logos of the cards supported by the system.', 'wc-pelecard-gateway' ),
				'desc_tip' => false,
				'options' => [
					'Amex' => __( 'American Express', 'wc-pelecard-gateway' ),
					'Diners' => __( 'Diners', 'wc-pelecard-gateway' ),
					'Isra' => __( 'Isracard', 'wc-pelecard-gateway' ),
					'Master' => __( 'Mastercard', 'wc-pelecard-gateway' ),
					'Visa' => __( 'Visa', 'wc-pelecard-gateway' ),
				],
			],
			'set_focus' => [
				'title' => __( 'Focus on field', 'wc-pelecard-gateway' ),
				'type' => 'select',
				'class' => 'wc-enhanced-select',
				'description' => '',
				'desc_tip' => false,
				'options' => [
					'' => __( 'Default (none)', 'wc-pelecard-gateway' ),
					'CC' => __( 'Card Number', 'wc-pelecard-gateway' ),
					'CCH' => __( 'Card Holder Name', 'wc-pelecard-gateway' ),
				],
			],
			'logo_url' => [
				'title' => __( 'Logo URL', 'wc-pelecard-gateway' ),
				'type' => 'url',
				'placeholder' => 'https://',
				'description' => __( 'Link to customers logo file.', 'wc-pelecard-gateway' ),
				'label' => __( 'Enabled', 'wc-pelecard-gateway' ),
				'default' => 'https://gateway21.pelecard.biz/Content/images/Pelecard.png',
				'desc_tip' => true,
			],
			'css_url' => [
				'title' => __( 'Css URL', 'wc-pelecard-gateway' ),
				'type' => 'url',
				'placeholder' => 'https://',
				'description' => __( 'CSS file link for custom design implementation.', 'wc-pelecard-gateway' ),
				'label' => __( 'Enabled', 'wc-pelecard-gateway' ),
				'default' => '',
				'desc_tip' => true,
			],
			/*'hidden_pelecard_logo' => [
				'title' => __( 'Pelecard Logo', 'wc-pelecard-gateway' ),
				'type' => 'checkbox',
				'description' => __( 'Show / hide pelecard logo.', 'wc-pelecard-gateway' ),
				'label' => __( 'Enabled', 'wc-pelecard-gateway' ),
				'default' => 'yes',
				'desc_tip' => true,
			],*/
		];

		return apply_filters( 'wppc/settings/admin_fields', $settings );
	}

	public static function is_settings_page(): bool {
		global $current_tab;

		return 'checkout' === $current_tab && Gateway::instance()->id === ( $_GET['section'] ?? '' );
	}
	
	public static function generate_payment_range_html( string $field, Gateway $gateway ): string {
		ob_start();

		$field_key = $gateway->get_field_key( $field );
		$ranges = array_filter( (array) $gateway->get_option( $field, [] ) );
		?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php _e( 'Custom Payments', 'wc-pelecard-gateway' ); ?>:</th>
			<td class="forminp" id="wppc_payment_range">
				<div class="wc_input_table_wrapper">
					<table class="widefat wc_input_table sortable" cellspacing="0">
						<thead>
						<tr>
							<th class="sort">&nbsp;</th>
							<th><?php _e( 'Min Cart', 'wc-pelecard-gateway' ); ?></th>
							<th><?php _e( 'Max Cart', 'wc-pelecard-gateway' ); ?></th>
							<th><?php _e( 'Min Payments', 'wc-pelecard-gateway' ); ?></th>
							<th><?php _e( 'Max Payments', 'wc-pelecard-gateway' ); ?></th>
						</tr>
						</thead>
						<tbody class="ranges">
						<?php foreach ( $ranges as $i => $range ): ?>
							<tr class="range">
								<td class="sort"></td>
								<td>
									<input
										type="number"
										value="<?php echo esc_attr( $range['min_cart'] ); ?>"
										name="<?php echo esc_attr( $field_key . '[' . $i . '][min_cart]' ); ?>"
										step="0.1"
										min="1"
										required
									/>
								</td>
								<td>
									<input
										type="number"
										value="<?php echo esc_attr( $range['max_cart'] ); ?>"
										name="<?php echo esc_attr( $field_key . '[' . $i . '][max_cart]' ); ?>"
										step="0.1"
										min="1"
										required
									/>
								</td>
								<td>
									<input
										type="number"
										value="<?php echo esc_attr( $range['min_payments'] ); ?>"
										name="<?php echo esc_attr( $field_key . '[' . $i . '][min_payments]' ); ?>"
										step="1"
										min="1"
										required
									/>
								</td>
								<td>
									<input
										type="number"
										value="<?php echo esc_attr( $range['max_payments'] ); ?>"
										name="<?php echo esc_attr( $field_key . '[' . $i . '][max_payments]' ); ?>"
										step="1"
										min="1"
										required
									/>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
						<tfoot>
						<tr>
							<th colspan="7">
								<a href="#" class="add button"><?php _e( '+ Add row', 'wc-pelecard-gateway' ); ?></a>
								<a href="#" class="remove_rows button"><?php _e( 'Remove selected row(s)', 'wc-pelecard-gateway' ); ?></a>
							</th>
						</tr>
						</tfoot>
					</table>
				</div>
				<script type="text/javascript">
					jQuery( function() {
						var $container = jQuery( '#wppc_payment_range' );

						$container.on( 'click', 'a.add', function() {
							var size = $container.find( 'tbody .range' ).length;
							var field = '<?php echo esc_attr( $field_key ); ?>[' + size + ']';

							jQuery( '<tr class="range">\
									<td class="sort"></td>\
									<td><input type="number" name="' + field + '[min_cart]" step="0.1" min="1" required /></td>\
									<td><input type="number" name="' + field + '[max_cart]" step="0.1" min="1" required /></td>\
									<td><input type="number" name="' + field + '[min_payments]" step="1" min="1" required /></td>\
									<td><input type="number" name="' + field + '[max_payments]" step="1" min="1" required /></td>\
								</tr>' ).appendTo( $container.find( 'tbody' ) );

							return false;
						} );
					} );
				</script>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}
}
