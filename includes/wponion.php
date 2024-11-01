<?php

use VSP\Core\Abstracts\Plugin_Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Class SKU_SF_WC_WPONION
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 */
class SKU_SF_WC_WPONION extends Plugin_Settings {
	/**
	 * Adds Settings Page.
	 */
	public function fields() {
		$builder = $this->builder->container( 'general', __( 'General', 'sku-shortlink-for-woocommerce' ), 'wpoic-settings' );

		$this->builder->container( 'docs', __( 'Documentation', 'sku-shortlink-for-woocommerce' ), 'wpoic-book' )
			->container_class( 'wpo-text-success' )
			->href( 'https://wordpress.org/plugins/sku-shortlink-for-woocommerce/' )
			->attribute( 'target', '_blank' );

		$this->builder->container( 'sysinfo', __( 'System Info', 'sku-shortlink-for-woocommerce' ), ' wpoic-info ' )
			->callback( 'wponion_sysinfo' )
			->set_var( 'developer', 'varunsridharan23@gmail.com' );
		$builder->subheading( 'URL Format' );

		/* @uses \sku_sf_wc_presets() */
		$builder->select( 'sku_url_type', __( 'SKU Url Type', 'sku-shortlink-for-woocommerce' ) )
			->options( 'sku_sf_wc_presets', false )
			->select_framework( 'select2' );

		/* translators: */
		$builder->text( 'sku_url_type_custom', __( 'Custom URL Format', 'sku-shortlink-for-woocommerce' ) )
			->desc_field( [
				__( 'Use `%sku%` to replace the value with sku code', 'sku-shortlink-for-woocommerce' ),
				__( 'Use `%any%` to replace any type of values like `Category, Slug, ID` and more ***(Only Dynamic URL)***', 'sku-shortlink-for-woocommerce' ),
			] )
			->sanitize( false )
			->style( 'min-width:400px' )
			->dependency( 'sku_url_type', '==', 'custom' );

		$builder->wp_notice( '<p style="font-weight: bold;">' . __( 'If Product SKU Link Not Working. Then Try Updating Permalink ', 'sku-shortlink-for-woocommerce' ) . '</p>' )
			->notice_type( 'warning' )
			->alt( true )
			->large( true );

		$builder->subheading( 'Basic' );

		$builder->switcher( 'modify_product_url', __( 'Modify Product URL', 'sku-shortlink-for-woocommerce' ) )
			->switch_style( 'style-14' )
			->off( __( 'NO', 'sku-shortlink-for-woocommerce' ) )
			->on( __( 'Yes', 'sku-shortlink-for-woocommerce' ) )
			->desc_field( 'if checked all the product urls will be regenerated with the product sku. ' );

		$builder->switcher( 'addtocart_url', __( 'Modify Product AddToCart', 'sku-shortlink-for-woocommerce' ) )
			->switch_style( 'style-14' )
			->off( __( 'NO', 'sku-shortlink-for-woocommerce' ) )
			->on( __( 'Yes', 'sku-shortlink-for-woocommerce' ) )
			->desc_field( __( 'IF Enabled Then Product\'s Add-To-Cart Url Will Have Products SKU', 'sku-shortlink-for-woocommerce' ) )
			->dependency( 'modify_product_url', 'checked', 'false' );

		$builder->switcher( 'modify_admin_url', __( 'Change Product Links in Admin ', 'sku-shortlink-for-woocommerce' ) )
			->switch_style( 'style-14' )
			->off( __( 'NO', 'sku-shortlink-for-woocommerce' ) )
			->on( __( 'Yes', 'sku-shortlink-for-woocommerce' ) )
			->desc_field( 'if checked all the product urls in admin side will be changed. ' )
			->dependency( 'modify_product_url', 'checked', 'false' );

		$builder->text( 'product_url_format', __( 'Product URL Format ', 'sku-shortlink-for-woocommerce' ) )
			->desc_field( array(
				__( 'Use `%sku%` For Product SKU.', 'sku-shortlink-for-woocommerce' ),
				__( 'Use `%postname%` For Product Slug.', 'sku-shortlink-for-woocommerce' ),
				__( 'Use `%id%` For Product ID.', 'sku-shortlink-for-woocommerce' ),
				__( 'Use `%category%` For Product Category.', 'sku-shortlink-for-woocommerce' ),
			) )
			->sanitize( false );

		$builder->switcher( 'random_category', __( 'Select Random Category', 'sku-shortlink-for-woocommerce' ) )
			->desc_field( __( 'If enabled and if a product has more than 1 category then category slug in the url will be picked at random', 'sku-shortlink-for-woocommerce' ) );
	}
}
