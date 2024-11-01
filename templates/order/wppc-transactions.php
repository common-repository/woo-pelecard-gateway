<?php
/**
 * Add transactions metabox
 *
 * @var \Pelecard\Transaction[] $transactions
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/wppc-transactions.php
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly
?>

<style>
	#wppc-transactions h3 a {
		color: #0073aa;
		padding-right: .5em;
	}

	#wppc-transactions h3 a:hover {
		text-decoration: underline;
	}
</style>

<table id="wppc-transactions" style="width: 100%;">
	<tbody>
	<?php foreach ( $transactions as $transaction ) : ?>
		<tr>
			<td>
				<h3>
					<?php
					echo esc_attr( $transaction->get_id() );

					$invoice = $transaction->get_meta('InvoiceLink');
					if ( ! empty( $invoice ) ) {
						printf(
							'<a href="%2$s" class="alignright" target="_blank">%1$s</a>',
							__( 'Get Invoice', 'wc-pelecard-gateway' ),
							$invoice
						);
					}
					?>
				</h3>
				<div>
					<table class="wp-list-table widefat fixed striped">
						<thead>
						<tr>
							<th scope="col" class="manage-column"><?php _e( 'Parameter', 'wc-pelecard-gateway' ); ?></th>
							<th scope="col" class="manage-column"><?php _e( 'Value', 'wc-pelecard-gateway' ); ?></th>
						</tr>
						</thead>
						<tbody>
						<?php foreach ( $transaction->get_meta_data() as $meta ) : ?>
							<tr>
								<td><span><?php echo esc_attr( $meta->get_data()['key'] ); ?></span></td>
								<td><span><?php echo esc_attr( $meta->get_data()['value'] ); ?></span></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
