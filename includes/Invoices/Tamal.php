<?php

namespace Pelecardwc\Invoices;

use Pelecardwc\Traits\Singleton;
use WC_Order;

class Tamal extends Base {

	use Singleton;

	public static $provider = 'tamal';

	protected static $item_scheme = [
		'Description',
		'Price',
		'Quantity',
	];

	public function get_checkout_args( array $args, WC_Order $order ): array {
		$type_invoice =$this->get_option( 'tamal_doc_type' );
		 if ( sizeof( $order->get_refunds() ) > 0 ) {
		 	switch ( $type_invoice ) {
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
			    	$type_invoice = $this->get_option( 'tamal_doc_type' );
			        break;
			}
		 } 
		$args['TamalInvoice'] = $this->apply_checkout_filters( [
			'InvoiceUserName' => $this->get_option( 'tamal_user' ),
			'InvoicePassword' => $this->get_option( 'tamal_password' ),
			'EsekNum' => $this->get_option( 'tamal_esek_num' ),
			'TypeCode' => $type_invoice ,
			'PrintLanguage' => $this->get_option( 'tamal_lang' ),
			'ClientNumber' => 200000,
			'ClientName' => $order->get_formatted_billing_full_name(),
			'ClientAddress' => $order->get_billing_address_1(),
			'ClientCity' => $order->get_billing_city(),
			'EmailAddress' => $order->get_billing_email(),
			'NikuyBamakorSum' => 0,
			'MaamRate' => 'mursh' === $this->get_option( 'tamal_osek' ) ? 999 : 0,
			'DocDetail' => sprintf( __( 'Order #%s', 'wc-pelecard-gateway' ), $order->get_order_number() ),
			'ToSign' => 1,
			'DocRemark' => $this->get_option( 'tamal_doc_remark' ),
			'ProductsList' => $this->get_formatted_items( $order ),
			'DiscountAmount' => '',
		], $order );

		return $args;
	}

	public function get_admin_fields( array $fields ): array {
		$fields = array_merge( $fields, [
			'tamal_title' => [
				'title' => __( 'Tamal (optional)', 'wc-pelecard-gateway' ),
				'type' => 'title',
			],
			'tamal' => [
				'title' => __( 'Enable/Disable', 'wc-pelecard-gateway' ),
				'type' => 'checkbox',
				'description' => '',
				'label' => __( 'Enable Tamal', 'wc-pelecard-gateway' ),
				'default' => 'no',
				'desc_tip' => false,
			],
			'tamal_user' => [
				'title' => __( 'Username', 'wc-pelecard-gateway' ),
				'type' => 'text',
				'description' => '',
				'default' => '',
				'desc_tip' => false,
			],
			'tamal_password' => [
				'title' => __( 'Password', 'wc-pelecard-gateway' ),
				'type' => 'password',
				'description' => '',
				'default' => '',
				'desc_tip' => false,
			],
			'tamal_esek_num' => [
				'title' => __( 'Esek number', 'wc-pelecard-gateway' ),
				'type' => 'text',
				'description' => '',
				'default' => '',
				'desc_tip' => false,
			],
			'tamal_osek' => [
				'title' => __( 'Osek Type', 'wc-pelecard-gateway' ),
				'type' => 'select',
				'class' => 'wc-enhanced-select',
				'description' => '',
				'default' => 0,
				'desc_tip' => false,
				'options' => [
					'mursh' => __( 'Osek Mursh', 'wc-pelecard-gateway' ),
					'patur' => __( 'Osek Patur', 'wc-pelecard-gateway' ),
					'amuta' => __( 'Amuta', 'wc-pelecard-gateway' ),
				],
			],
			'tamal_doc_type' => [
				'title' => __( 'Document Type', 'wc-pelecard-gateway' ),
				'type' => 'select',
				'class' => 'wc-enhanced-select',
				'description' => '',
				'default' => 320,
				'desc_tip' => false,
				'options' => [
					'100' => __( 'Hazmana', 'wc-pelecard-gateway' ),
					'300' => __( 'Heshbonit Iska', 'wc-pelecard-gateway' ),
					'305' => __( 'Heshbonit Mas', 'wc-pelecard-gateway' ),
					'320' => __( 'Heshbonit Mas Kabala', 'wc-pelecard-gateway' ),
					'330' => __( 'Heshbonit Mas Zikui', 'wc-pelecard-gateway' ),
					'400' => __( 'Kabala', 'wc-pelecard-gateway' ),
					'405' => __( 'Kabala Al Trumot', 'wc-pelecard-gateway' ),
					'10100' => __( 'Hazaat Mehir', 'wc-pelecard-gateway' ),
					'10301' => __( 'Heshbon Iska', 'wc-pelecard-gateway' ),
				],
			],
			'tamal_lang' => [
				'title' => __( 'Document Language', 'wc-pelecard-gateway' ),
				'type' => 'select',
				'class' => 'wc-enhanced-select',
				'description' => '',
				'default' => 0,
				'desc_tip' => false,
				'options' => [
					'0' => __( 'Hebrew', 'wc-pelecard-gateway' ),
					'1' => __( 'English', 'wc-pelecard-gateway' ),
				],
			],
			'tamal_doc_remark' => [
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
