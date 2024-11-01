<?php

defined( 'ABSPATH' ) || exit;

use VSP\Framework;

if ( ! class_exists( '\SKU_SF_WC' ) ) {
	/**
	 * Class SKU_SF_WC
	 *
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	final class SKU_SF_WC extends Framework {
		/**
		 * SKU_SF_WC constructor.
		 *
		 * @throws \Exception
		 */
		public function __construct() {
			parent::__construct( array(
				'name'          => SKU_SF_WC_NAME,
				'file'          => SKU_SF_WC_FILE,
				'version'       => SKU_SF_WC_VERSION,
				'hook_slug'     => 'sku_sf_wc',
				'db_slug'       => 'sku_sf_wc',
				'slug'          => 'sku-shortlink-for-woocommerce',
				'addons'        => false,
				'logging'       => false,
				'system_tools'  => false,
				'localizer'     => false,
				'autoloader'    => false,
				'settings_page' => array(
					'option_name'    => '_sku_sf_wc',
					'framework_desc' => __( 'This handy plugin might just be what you have searched for. It will create URL Aliases for your WooCommerce products.', 'sku-shortlink-for-woocommerce' ),
					'theme'          => 'wp',
					'ajax'           => true,
					'search'         => false,
					'menu'           => array(
						'page_title' => SKU_SF_WC_NAME,
						'menu_title' => __( 'SKU Shortlink', 'sku-shortlink-for-woocommerce' ),
						'submenu'    => 'woocommerce',
						'menu_slug'  => 'sku-sf-wc',
					),
				),
			) );
		}

		/**
		 * Loads Required Files.
		 */
		public function load_files() {
			$this->load_file( 'includes/class-frontend.php' );
		}

		/**
		 * Inits All Basic Class.
		 */
		public function init_class() {
			$this->_instance( 'SKU_SF_WC_Frontend' );
			if ( vsp_is_admin() ) {
				wponion_plugin_links( $this->file() )
					->action_link_before( 'settings', __( '⚙️ Settings', 'sku-shortlink-for-woocommerce' ), admin_url( 'admin.php?page=sku-sf-wc' ) )
					->action_link_after( 'sysinfo', __( 'ℹ️ System Info', 'sku-shortlink-for-woocommerce' ), admin_url( 'admin.php?page=sku-sf-wc&container-id=sysinfo' ) )
					->row_link( __( '📚 F.A.Q', 'sku-shortlink-for-woocommerce' ), 'https://wordpress.org/plugins/sku-shortlink-for-woocommerce/#faq' )
					->row_link( __( '📦 View On Github', 'sku-shortlink-for-woocommerce' ), 'https://github.com/varunsridharan/sku-shortlink-for-woocommerce/' )
					->row_link( __( '📝 Report An Issue', 'sku-shortlink-for-woocommerce' ), 'https://github.com/varunsridharan/sku-shortlink-for-woocommerce/issues' )
					->row_link( __( '💁🏻 Donate', 'sku-shortlink-for-woocommerce' ), 'https://paypal.me/varunsridharan' );
			}
		}

		/**
		 * Settings Before Init.
		 */
		public function settings_init_before() {
			$this->load_file( '/includes/wponion.php' );
			$this->_instance( 'SKU_SF_WC_WPONION' );
		}
	}
}
