<?php

namespace Pelecardwc\Invoices;

use Pelecardwc\Traits\Singleton;
use WC_Order;

class ICount extends Base {

	use Singleton;

	public static $provider = 'icount';

	protected static $item_scheme = [
		'description',
		'unitprice_incvat',
		'quantity',
	];

	public function get_checkout_args( array $args, WC_Order $order ): array {
		$docType = $this->get_option( 'icount_doc_type' );
		if ( sizeof( $order->get_refunds() ) > 0 ) {
			if ($docType == 'invrec') {
				$docType = 'refund';
			}
		}
		$args['ICountInvoice'] = $this->apply_checkout_filters( [
			'docType' => $docType,
			'cid' => $this->get_option( 'icount_cid' ),
			'user' => $this->get_option( 'icount_user' ),
			'pass' => $this->get_option( 'icount_password' ),
			'client_name' => $order->get_formatted_billing_full_name(),
			'email_to' => $order->get_billing_email(),
			'send_email' => 1,
			'email_lang' => $this->get_option( 'icount_email_lang' ),
			'doc_title' => sprintf( __( 'Order #%s', 'wc-pelecard-gateway' ), $order->get_order_number() ),
			'hwc' => $this->get_option( 'icount_hwc' ),
			'items' => $this->get_formatted_items( $order ),
		], $order );

		return $args;
	}
	public function get_admin_fields( array $fields ): array {
		$fields = array_merge( $fields, [
			'icount_title' => [
				'title' => __( 'ICount (optional)', 'wc-pelecard-gateway' ),
				'type' => 'title',
			],
			'icount' => [
				'title' => __( 'Enable/Disable', 'wc-pelecard-gateway' ),
				'type' => 'checkbox',
				'description' => '',
				'label' => __( 'Enable iCount', 'wc-pelecard-gateway' ),
				'default' => 'no',
				'desc_tip' => false,
			],
			'icount_cid' => [
				'title' => __( 'Company ID', 'wc-pelecard-gateway' ),
				'type' => 'text',
				'description' => '',
				'default' => '',
				'desc_tip' => false,
			],
			'icount_user' => [
				'title' => __( 'Username', 'wc-pelecard-gateway' ),
				'type' => 'text',
				'description' => '',
				'default' => '',
				'desc_tip' => false,
			],
			'icount_password' => [
				'title' => __( 'Password', 'wc-pelecard-gateway' ),
				'type' => 'password',
				'description' => '',
				'default' => '',
				'desc_tip' => false,
			],
			'icount_doc_type' => [
				'title' => __( 'Document Type', 'wc-pelecard-gateway' ),
				'type' => 'select',
				'class' => 'wc-enhanced-select',
				'description' => '',
				'default' => 'invrec',
				'desc_tip' => false,
				'options' => [
					'invrec' => __( 'Heshbonit Mas Kabala', 'wc-pelecard-gateway' ),
					'receipt' => __( 'Kabala', 'wc-pelecard-gateway' ),
					'trec' => __( 'Truma', 'wc-pelecard-gateway' ),
					'invoice' => __( 'Heshbonit Mas', 'wc-pelecard-gateway' ),
				],
			],
			'icount_email_lang' => [
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
			'icount_hwc' => [
				'title' => __( 'Document Remark', 'wc-pelecard-gateway' ),
				'type' => 'text',
				'description' => '',
				'default' => '',
				'desc_tip' => false,
			],
		] );

		return $fields;
	}
}
