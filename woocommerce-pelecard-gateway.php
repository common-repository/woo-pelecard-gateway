<?php
/**
 * Plugin Name: Pelecard Gateway
 * Plugin URI: 
 * Description: Extends WooCommerce with Pelecard payment gateway.
 * Version: 1.4.30
 * Author: Pelecard
 * Author URI: https://www.pelecard.com/
 * Text Domain: wc-pelecard-gateway
 * Requires at least: 5.5
 * Requires PHP: 7.0
 *
 * WC requires at least: 3.0
 * WC tested up to: 6.6.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'wppc_FILE' ) ) {
	define( 'wppc_FILE', __FILE__ );
}

add_action( 'plugins_loaded', 'wppc_load_plugin_textdomain' );

if ( ! version_compare( PHP_VERSION, '7.0', '>=' ) ) {
	add_action( 'admin_notices', 'wppc_fail_php_version' );
	
} else {

	$need = false; // do we need Woo?
	$network = false; // is plugin activated at network level? 
	           
	 if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
	   require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	 }
	       
	 // Multisite && this plugin is network activated - Woo must be network activated 
	 if ( is_multisite() && is_plugin_active_for_network( plugin_basename(__FILE__) ) ) {
	    $need = is_plugin_active_for_network('woocommerce/woocommerce.php') ? false : true; 
	    $network = true; 
	 // This plugin runs on a single site || is locally activated 
	 } else {
	   $need =  is_plugin_active( 'woocommerce/woocommerce.php') ? false : true;     
	 }
	       
	 if ($need === true) {
	    add_action( 'admin_notices',  'wppc_fail_woocommers' );
	    deactivate_plugins( plugin_basename( __FILE__ ) , false, $network );
	 } else {
	    // Good to go!
	    require_once __DIR__ . '/vendor/autoload.php';
		\Pelecardwc\Plugin::instance();
	 }
}

function wppc_load_plugin_textdomain() {
	load_plugin_textdomain( 'wc-pelecard-gateway' );
}

function wppc_fail_php_version() {
	/* translators: %s: PHP version */
	$message = sprintf( esc_html__( 'Woo Pelecard Gateway requires PHP version %s+, plugin is currently NOT RUNNING.', 'wc-pelecard-gateway' ), '7.0' );
	$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
	echo wp_kses_post( $html_message );
}

function wppc_fail_woocommers() {
	/* translators: %s: PHP version */
	$message = sprintf( esc_html__( 'Woo Pelecard Gateway requires WooCommerce at least %s+, plugin is currently NOT RUNNING.', 'wc-pelecard-gateway' ), '3.0' );
	$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
	echo wp_kses_post( $html_message );
}