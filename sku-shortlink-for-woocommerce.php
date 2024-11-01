<?php
/**
 * Plugin Name:       SKU Shortlink For WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/sku-shortlink-for-woowommerce/
 * Description:       Add Product SKU value in woocommerce products url
 * Version:           1.4
 * Author:            Varun Sridharan
 * Author URI:        http://varunsridharan.in
 * Text Domain:       sku-shortlink-for-woocommerce
 * Domain Path:       /i18n/
 * WC requires at least: 3.0.0
 * WC tested up to: 4.2
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/varunsridharan/sku-shortlink-for-woocommerce/
 */

defined( 'ABSPATH' ) || exit;

defined( 'SKU_SF_WC_FILE' ) || define( 'SKU_SF_WC_FILE', __FILE__ );
defined( 'SKU_SF_WC_VERSION' ) || define( 'SKU_SF_WC_VERSION', '1.4' );
defined( 'SKU_SF_WC_NAME' ) || define( 'SKU_SF_WC_NAME', __( 'SKU Shortlink For WooCommerce', 'sku-shortlink-for-woocommerce' ) );

register_activation_hook( __FILE__, 'sku_sf_wc_activation' );

if ( ! function_exists( 'sku_sf_wc_activation' ) ) {
	/**
	 * Runs On Plugin Activation.
	 */
	function sku_sf_wc_activation() {
		$is_old = get_option( 'sku_sf_wc_url_type', false );
		if ( ! empty( $is_old ) ) {
			$url_type                 = get_option( 'sku_sf_wc_url_type', false );
			$custom_link              = get_option( 'sku_sf_wc_custom_link', false );
			$modify_product_url       = get_option( 'sku_sf_wc_modify_product_url', false );
			$admin_modify_product_url = get_option( 'sku_sf_wc_admin_modify_product_url', false );
			$product_url_format       = get_option( 'sku_sf_wc_product_url_format', false );

			if ( ! empty( $url_type ) && 'custom' !== $url_type ) {
				$url_type = 'preset' . $url_type;
			}

			$new_options = array(
				'modify_product_url'  => ( $modify_product_url ),
				'modify_admin_url'    => ( $admin_modify_product_url ),
				'product_url_format'  => $product_url_format,
				'sku_url_type'        => $url_type,
				'sku_url_type_custom' => $custom_link,
			);
			update_option( '_sku_sf_wc', $new_options );

			delete_option( 'sku_sf_wc_url_type' );
			delete_option( 'sku_sf_wc_custom_link' );
			delete_option( 'sku_sf_wc_modify_product_url' );
			delete_option( 'sku_sf_wc_admin_modify_product_url' );
			delete_option( 'sku_sf_wc_product_url_format' );
			flush_rewrite_rules();
		}
	}
}

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

if ( function_exists( 'vsp_maybe_load' ) ) {
	vsp_maybe_load( 'sku_shortlink_for_wc_init', __DIR__ . '/vendor/varunsridharan' );
}

if ( function_exists( 'wponion_load' ) ) {
	wponion_load( __DIR__ . '/vendor/wponion/wponion' );
}

if ( ! function_exists( 'sku_shortlink_for_wc_init' ) ) {
	/**
	 * Inits SKU Shortlinks Plugin Once VSP_Framework Inits.
	 *
	 * @return bool|\SKU_SF_WC
	 */
	function sku_shortlink_for_wc_init() {
		if ( ! vsp_add_wc_required_notice( SKU_SF_WC_NAME ) ) {
			if ( ! vsp_is_ajax() || ! vsp_is_cron() ) {
				require_once __DIR__ . '/includes/functions.php';
				require_once __DIR__ . '/bootstrap.php';
				return sku_sf_wc();
			}
		}
		return false;
	}
}

if ( ! function_exists( 'sku_sf_wc' ) ) {
	/**
	 * Returns Plugin's Instance.
	 *
	 * @return \SKU_SF_WC
	 */
	function sku_sf_wc() {
		return SKU_SF_WC::instance();
	}
}
