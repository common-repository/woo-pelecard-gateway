<?php

namespace Pelecardwc\Invoices;

use Pelecardwc\Gateway;
use WC_Order;

/**
 * Class Base
 */
abstract class Base {

	public static $provider;

	protected function __construct() {
		add_action( 'plugins_loaded', [ $this, 'register_hooks' ] );
		add_filter( 'wppc/settings/admin_fields', [ $this, 'get_admin_fields' ], 20 );
	}

	public function register_hooks() {
		if ( $this->is_enabled() ) {
			add_filter( 'wppc/checkout/rest_args', [ $this, 'maybe_add_invoice_args' ], 10, 3 );
			add_filter( 'wppc/checkout/iframe_args', [ $this, 'maybe_add_invoice_args' ], 10, 3 );
		}
	}

	public function is_enabled(): bool {
		return 'yes' === $this->get_option( static::$provider );
	}

	public function get_option( string $key, $empty_value = null ) {
		return Gateway::instance()->get_option( $key, $empty_value );
	}

	public function get_formatted_items( WC_Order $order ): array {
		$items = [];

		list( $name, $total, $quantity ) = static::$item_scheme;

		
		

		$need_to_add_shipping = true;

		if ( sizeof( $order->get_refunds() ) > 0 ){
			$need_to_add_shipping = false;
			$partial_refund = false;

			$order_total = (int) $order->get_data()['total'];
			$order_refund = (int) $order->get_total() * (-1);



			if ( $order_refund < $order_total) {
				$partial_refund = true;
			}

			$refund_title = 'refund partial title';

			if ( $partial_refund ) {

				if (static::$provider == 'icount'){

					LogInvoice::info( sprintf( 'invoice lang (%s)', $this->get_option( 'icount_email_lang'  ) ) );
					switch ( $this->get_option( 'icount_email_lang' ) ) {
					    case 'he':
					        $refund_title = sprintf( __( 'קבלת זיכוי #%s', 'wc-pelecard-gateway' ), $order->get_order_number() );
					        break;
					    case 'en':
					        $refund_title = sprintf( __( 'Refund Receipt #%s', 'wc-pelecard-gateway' ), $order->get_order_number() );
					        break;
					    default:
					       $refund_title = sprintf( __( 'Refund Receipt #%s', 'wc-pelecard-gateway' ), $order->get_order_number() );
					}
				}

				if (static::$provider == 'ezcount'){

					LogInvoice::info( sprintf( 'invoice lang (%s)', $this->get_option( 'ezcount_lang' ) ) );
					switch ( $this->get_option( 'ezcount_lang' ) ) {
					    case 'he':
					        $refund_title = sprintf( __( 'קבלת זיכוי #%s', 'wc-pelecard-gateway' ), $order->get_order_number() );
					        break;
					    case 'en':
					        $refund_title = sprintf( __( 'Refund Receipt #%s', 'wc-pelecard-gateway' ), $order->get_order_number() );
					        break;
					    default:
					       $refund_title = sprintf( __( 'Refund Receipt #%s', 'wc-pelecard-gateway' ), $order->get_order_number() );
					}
				}

				if (static::$provider == 'tamal'){

					LogInvoice::info( sprintf( 'invoice lang (%s)', $this->get_option( 'tamal_lang' ) ) );
					switch ( $this->get_option( 'tamal_lang' ) ) {
					    case '0':
					        $refund_title = sprintf( __( 'קבלת זיכוי #%s', 'wc-pelecard-gateway' ), $order->get_order_number() );
					        break;
					    case '1':
					        $refund_title = sprintf( __( 'Refund Receipt #%s', 'wc-pelecard-gateway' ), $order->get_order_number() );
					        break;
					    default:
					       $refund_title = sprintf( __( 'Refund Receipt #%s', 'wc-pelecard-gateway' ), $order->get_order_number() );
					}
				}
				
				$items[] = [
					$name => $refund_title,
					$total => (int) $order->get_total() * 100 * (-1),
					$quantity => 1,
				];

			} else {

				$need_to_add_shipping = true;

				foreach ( $order->get_items() as $item ) {
					$total_with_tax = (
						(float) $item->get_total() + (float) $item->get_total_tax()
					) / $item->get_quantity();

					$items[] = apply_filters( 'wpg/invoices/' . static::$provider . '_formatted_item', [
						$name => $item->get_name(),
						$total => $total_with_tax * 100 ,
						$quantity => $item->get_quantity(),
					], $item );


				}

				if ( $need_to_add_shipping ) {
					$shipping_total = (float) $order->get_shipping_total();

			        // Check if an item with the same name already exists
			        $exists = false;
			        foreach ($items as $item) {
			            if ($item[$name] === $order->get_shipping_method()) {
			                $exists = true;
			                break;
			            }
			        }



			        // Add the shipping item only if it doesn't exist already
			        if (!$exists && $shipping_total > 0) {
			            $items[] = [
			                $name => $order->get_shipping_method(),
			                $total => $shipping_total * 100 ,
			                $quantity => 1,
			            ];
			        }
				}
			}


		} else {
			foreach ( $order->get_items() as $item ) {
				$total_with_tax = (
					(float) $item->get_total() + (float) $item->get_total_tax()
				) / $item->get_quantity();

				$items[] = apply_filters( 'wpg/invoices/' . static::$provider . '_formatted_item', [
					$name => $item->get_name(),
					$total => $total_with_tax * 100,
					$quantity => $item->get_quantity(),
				], $item );
			}

			$shipping_total = (float) $order->get_shipping_total();

	        // Check if an item with the same name already exists
	        $exists = false;
	        foreach ($items as $item) {
	            if ($item[$name] === $order->get_shipping_method()) {
	                $exists = true;
	                break;
	            }
	        }



	        // Add the shipping item only if it doesn't exist already
	        if (!$exists && $shipping_total > 0) {
	            $items[] = [
	                $name => $order->get_shipping_method(),
	                $total => $shipping_total * 100,
	                $quantity => 1,
	            ];
	        }
		}
 	

		return apply_filters( 'wppc/invoices/' . static::$provider . '_formatted_items', $items, $order );
	}

	public function apply_checkout_filters( array $args, WC_Order $order ): array {
		return apply_filters( 'wppc/invoices/' . static::$provider . '_checkout_args', $args, $order );
	}

	/*public function maybe_add_invoice_args( array $args, WC_Order $order, Gateway $gateway ) {
        if ( 'J4' !== $gateway->get_action_type() ) {
            return $args;
        }
        return $this->get_checkout_args( $args, $order );
    }*/

    public function maybe_add_invoice_args( array $args, WC_Order $order, Gateway $gateway ) {
        switch ( $gateway->get_action_type() ) {
		    case 'J4':
		        return $this->get_checkout_args( $args, $order );
		    case 'J2':
		        //return $this->get_checkout_args( $args, $order );
		        if ( $gateway->check_if_exists_saved_card() ){
		        	return $this->get_checkout_args( $args, $order );
		        } else{
		        	return $args;
		        }
		    case 'J5':
		        return $args;
		}
		return $args;
    }

	abstract public function get_admin_fields( array $fields ): array;

	abstract public function get_checkout_args( array $args, WC_Order $order ): array;
}
