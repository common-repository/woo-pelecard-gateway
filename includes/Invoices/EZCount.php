<?php

namespace Pelecardwc\Invoices;

use Pelecardwc\Traits\Singleton;
use WC_Order;

class EZCount extends Base {

	use Singleton;

	/**
	 * @var string $provider
	 */
	public static $provider = 'ezcount';

	/**
	 * @var string[] $item_scheme
	 */
	protected static $item_scheme = [
		'details',
		'price',
		'amount',
	];

	/**
	 * EZCount constructor.
	 */
	public function __construct() {
		add_filter( 'wppc/invoices/' . self::$provider . '_formatted_item', [ $this, 'add_catalog_number' ], 10, 2 );

		parent::__construct();
	}

	/**
	 * @param array                  $item_data
	 * @param \WC_Order_Item_Product $item
	 *
	 * @return array
	 */
	public function add_catalog_number( array $item_data, \WC_Order_Item_Product $item ): array {
		$product = $item->get_product();
		$item_data['catalog_number'] = $product ? $product->get_sku() : null;

		return $item_data;
	}

	/**
	 * @param array     $args
	 * @param \WC_Order $order
	 *
	 * @return array
	 */
	public function get_checkout_args( array $args, WC_Order $order ): array {
		if ( sizeof( $order->get_refunds() ) > 0 ) {
			switch ( $this->get_option( 'ezcount_doc_type' ) ) {
			    case '300':
			        $type_invoice = '330';
			        break;
			    case '305':
			          $type_invoice = '330';
			        break;
			    case '320':
			         $type_invoice = '330';
			        break;
			    case '330':
			         $type_invoice = '330';
			        break;
			    default:
			    	$type_invoice = $this->get_option( 'ezcount_doc_type' );
			        break;
			}
			$args['EZcountParameters'] = $this->apply_checkout_filters( [
				'type' => $type_invoice,
				'api_key' => $this->get_option( 'ezcount_api_key' ),
				'api_email' => $this->get_option( 'ezcount_api_email' ),
				'developer_email' => $this->get_option( 'ezcount_dev_email' ),
				'description' => '',
				'ua_uuid' => $this->get_option( 'ezcount_uuid' ),
				'lang' => $this->get_option( 'ezcount_lang' ),
				'customer_name' => $order->get_formatted_billing_full_name(),
				'customer_address' => $order->get_billing_address_1(),
				'customer_phone' => $order->get_billing_phone(),
				'customer_email' => $order->get_billing_email(),
				'comment' => sprintf( __( 'Order #%s', 'wc-pelecard-gateway' ), $order->get_order_number() ),
				'email_text' => '',
				'dont_send_email' => false,
				'send_copy' => 'yes' === $this->get_option( 'ezcount_send_copy' ),
				'vat_type' => $this->get_option( 'ezcount_vat_type' ),
				'item' => $this->get_formatted_items( $order ),
				'auto_balance' => true,
				'forceItemsIntoNonItemsDocument' => 400 === (int) $this->get_option( 'ezcount_doc_type' ),
			], $order );
		} else {
			$type_invoice = $this->get_option( 'ezcount_doc_type' );
			$args['EZcountParameters'] = $this->apply_checkout_filters( [
				'type' => $type_invoice,
				'api_key' => $this->get_option( 'ezcount_api_key' ),
				'api_email' => $this->get_option( 'ezcount_api_email' ),
				'developer_email' => $this->get_option( 'ezcount_dev_email' ),
				'transaction_id' => $order->get_id(),
				'description' => '',
				'ua_uuid' => $this->get_option( 'ezcount_uuid' ),
				'lang' => $this->get_option( 'ezcount_lang' ),
				'customer_name' => $order->get_formatted_billing_full_name(),
				'customer_address' => $order->get_billing_address_1(),
				'customer_phone' => $order->get_billing_phone(),
				'customer_email' => $order->get_billing_email(),
				'comment' => sprintf( __( 'Order #%s', 'wc-pelecard-gateway' ), $order->get_order_number() ),
				'email_text' => '',
				'dont_send_email' => false,
				'send_copy' => 'yes' === $this->get_option( 'ezcount_send_copy' ),
				'vat_type' => $this->get_option( 'ezcount_vat_type' ),
				'item' => $this->get_formatted_items( $order ),
				'auto_balance' => true,
				'forceItemsIntoNonItemsDocument' => 400 === (int) $this->get_option( 'ezcount_doc_type' ),
			], $order );
		}
		

		return $args;
	}

	/**
	 * @param array $fields
	 *
	 * @return array
	 */
	public function get_admin_fields( array $fields ): array {
		$fields = array_merge( $fields, [
			'ezcount_title' => [
				'title' => __( 'EZCount (optional)', 'wc-pelecard-gateway' ),
				'type' => 'title',
			],
			'ezcount' => [
				'title' => __( 'Enable/Disable', 'wc-pelecard-gateway' ),
				'type' => 'checkbox',
				'description' => '',
				'label' => __( 'Enable EZcount', 'wc-pelecard-gateway' ),
				'default' => 'no',
				'desc_tip' => false,
			],
			'ezcount_api_key' => [
				'title' => __( 'API Key', 'wc-pelecard-gateway' ),
				'type' => 'text',
				'description' => '',
				'default' => '',
				'desc_tip' => false,
			],
			'ezcount_uuid' => [
				'title' => __( 'Sub Account', 'wc-pelecard-gateway' ),
				'type' => 'text',
				'description' => '',
				'default' => '',
				'desc_tip' => false,
			],
			'ezcount_doc_type' => [
				'title' => __( 'Document Type', 'wc-pelecard-gateway' ),
				'type' => 'select',
				'class' => 'wc-enhanced-select',
				'description' => '',
				'default' => 320,
				'desc_tip' => false,
				'options' => [
					'300' => __( 'Heshbonit Iska', 'wc-pelecard-gateway' ),
					'305' => __( 'Heshbonit Mas', 'wc-pelecard-gateway' ),
					'320' => __( 'Heshbonit Mas Kabala', 'wc-pelecard-gateway' ),
					'330' => __( 'Heshbonit Mas Zikui', 'wc-pelecard-gateway' ),
					'400' => __( 'Kabala', 'wc-pelecard-gateway' ),
					'405' => __( 'Kabala Al Trumot', 'wc-pelecard-gateway' ),
				],
			],
			'ezcount_vat_type' => [
				'title' => __( 'VAT Type', 'wc-pelecard-gateway' ),
				'type' => 'select',
				'class' => 'wc-enhanced-select',
				'description' => '',
				'default' => 'INC',
				'desc_tip' => false,
				'options' => [
					'INC' => __( 'Including VAT', 'wc-pelecard-gateway' ),
					'NON' => __( 'Without VAT', 'wc-pelecard-gateway' ),
				],
			],
			'ezcount_api_email' => [
				'title' => __( 'Business owner email', 'wc-pelecard-gateway' ),
				'type' => 'email',
				'description' => '',
				'default' => '',
				'desc_tip' => false,
			],
			'ezcount_dev_email' => [
				'title' => __( 'Developer Email', 'wc-pelecard-gateway' ),
				'type' => 'email',
				'description' => '',
				'default' => '',
				'desc_tip' => false,
			],
			'ezcount_send_copy' => [
				'title' => __( 'Send copy', 'wc-pelecard-gateway' ),
				'type' => 'checkbox',
				'description' => 'Sends a copy to the email of the business owner',
				'label' => __( 'Enabled', 'wc-pelecard-gateway' ),
				'default' => 'yes',
				'desc_tip' => true,
			],
			'ezcount_lang' => [
				'title' => __( 'Document Language', 'wc-pelecard-gateway' ),
				'type' => 'select',
				'class' => 'wc-enhanced-select',
				'description' => '',
				'default' => 'he',
				'desc_tip' => false,
				'options' => [
					'he' => __( 'Hebrew', 'wc-pelecard-gateway' ),
					'en' => __( 'English', 'wc-pelecard-gateway' ),
				],
			],
		] );

		return $fields;
	}
}
