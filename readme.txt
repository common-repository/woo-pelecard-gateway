=== Woo Pelecard Gateway ===
Contributors: issirius
Tags: e-commerce, payments, gateway, checkout, pelecard, invoices, woo commerce, subscriptions
Requires at least: 5.5
Tested up to: 6.6
Stable tag: 1.4.30
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Extends WooCommerce with Pelecard payment gateway.

== Description ==

**Pelecard payment gateway for WooCommerce.**

= About Pelecard =
[Pelecard](https://www.pelecard.com) has been providing clearing solutions for over 30 years, and provides a secure and advanced solution for organizations large and small, including websites.
Pelecard is one of the largest software houses in Israel, combining a professional, fast and reliable development and service department.
Placard operates behind the scenes, which allows the business to work automatically, and perform ongoing and fast-paced business activities.
Pelecard achieves this by investing in the development of easy-to-manage and implement solutions, strong information security, tight interfaces to all management software, and the exploitation of new technologies.

= About the plugin =

The plugin allows you to use Pelecard payment gateway with the WooCommerce plugin.

= Features =
* Accept all major credit cards
* Responsive payment form
* Invoices & Receipts
* Subscriptions
* On demand Development

== Installation ==

= Installation =
1. In your WordPress Dashboard go to "Plugins" -> "Add Plugin".
2. Search for "Woo Pelecard Gateway".
3. Install the plugin by pressing the "Install" button.
4. Activate the plugin by pressing the "Activate" button.
5. Open the settings page for WooCommerce and click the "Payments" tab.
6. Click on "Pelecard" in the payment-methods list.
7. Configure your Pelecard Gateway settings.

= Minimum Requirements =
* WordPress version 5.3 or greater.
* PHP version 7.0 or greater.
* MySQL version 5.6 or greater.
* WooCommerce version 3.0 or greater.

== Screenshots ==

1. Easy configuration.
2. Payment gateway selection.
3. Responsive, IFrame-based form.

== Frequently Asked Questions ==

= What is the cost for the gateway plugin? =
This plugin is a FREE download.

== Changelog ==

= 1.4.30 = 

*fix - if transction details was saved several time in order update only 
the last one

*fix - cannot make partial refund from order

= 1.4.29 = 

*Fixed several transactions issue

= 1.4.29 = 

*Changed logs functions. Hide personal user data: username/password

= 1.4.28 = 

*Fixed shipping refund logic
*Fixed client's logo logic

= 1.4.27 = 

*Improved subscription logic

= 1.4.22 = 

*Bug fix in subscription.
 if J2 configured in plugin settings icount / ezcount 
 invoice was not created in renewal payment.

= 1.4.21 = 

*Added support for MultiSite

= 1.4.20 = 

*Convert information errors 041,042 that 
 received in J2 to 000.

= 1.4.19 = 
*Added Emv errors in order notes.
 ShvaResultEmv parameter

= 1.4.18 =
* Adding support for refund in icount with document 'kabala'
* System improvements regarding WooCommerce Subscriptions

= 1.4.9 =
* Added the 'wpg/transaction/order_id' filter hook.
* Added the auto_balance parameter to EZCount integration.

= 1.4.8 =
* Add 3D-Secure params to J4 after J5 requests.
* Save total-payments for later use when doing J5 transactions.

= 1.4.7 =
* Bypass validation for J2 transactions.

= 1.4.6 =
* Disable payments transaction for subscription orders.

= 1.4.5 =
* Bypass validation for 3DS failed transactions.

= 1.4.4 =
* Fixed supported CC field.

= 1.4.3 =
* EZCount: add managed option for the `send_copy` parameter.

= 1.4.2 =
* Fixed J5 transactions logic.

= 1.4.1 =
* Fixed WPML/WCML integration.

= 1.4 =
* Added WooCommerce Subscriptions support.
* Added refund capabilities.
* Support J5 transactions.
* Added WPML/WCML support.
* Fixed checkout flow with IPN fallback.

= 1.3 =
* Added [iCount](https://www.icount.co.il/) support.
* Added [EZcount](https://www.ezcount.co.il/) support.

= 1.2.2 =
* Fixed syntax bug for PHP versions prior to 5.4.
* Fixed incorrect data sent to Tamal.

= 1.2.1 =
* Added filter hooks.
* Fixed HiddenPelecardLogo field logic.

= 1.2.0 =
* Restructured plugin.
* Added support for WC 3.x.
* Added Tokenization support.

= 1.1.12 =
* Added order discount for Tamal.

= 1.1.11 =
* Fixed Tamal default parameters.

= 1.1.10 =
* Fixed Tamal 'MaamRate' for Receipts.

= 1.1.9.4 =
* WordPress 4.7 compatible.
* Removed deprecated function(s).

= 1.1.9.3 =
* Added the 'wc_pelecard_gateway_request_args' filter hook.

= 1.1.9.2 =
* Added full transaction history
* Added gateway icon support (filter).
* Added advanced error logging.

= 1.1.9.1 =
* Fixed gateway response check.
* Fixed bug in constructor.

= 1.1.9 =
* Added the ability to customize min & max payments by cart's total.

= 1.1.8 =
* Added filter hooks.

= 1.1.7 =
* Fixed JS loading.

= 1.1.6 =
* Added Tamal document types.

= 1.1.5 =
* Added shipping to Tamal Invoices.

= 1.1.4 =
* Fixed major front-end bug.

= 1.1.3 =
* Added WordPress 4.5 & WooCommerce 2.5.5 compatibility

= 1.1.2 =
* Updated admin js.

= 1.1.1 =
* Update translation strings.
* Add translators comments.

= 1.1.0 =
* Added [Tamal API](https://www.accountbook.co.il/) for creating invoices.
* Improved tab-based admin menu.

= 1.0.5 =
* i18n: Remove po/mo files from the plugin.
* i18n: Use [translate.wordpress.org](https://translate.wordpress.org/) to translate the plugin.

= 1.0.4 =
* Updated plugin translation files.

= 1.0.3 =
* Added advanced gateway options.

= 1.0.2 =
* Improved data validations.

= 1.0.1 =
* Fixed XSS Vulnerability.

= 1.0.0 =
* First Release.

== Upgrade Notice ==

= 1.1.4 =
* Fixed major front-end bug.

= 1.1.3 =
* Added WordPress 4.5 & WooCommerce 2.5.5 compatibility

= 1.0.2 =
Improved data validations.

= 1.0.1 =
Fixed XSS Vulnerability.