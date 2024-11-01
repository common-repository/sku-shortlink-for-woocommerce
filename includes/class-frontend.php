<?php

defined( 'ABSPATH' ) || exit;

use VSP\Base;

/**
 * Class SKU_SF_WC_Frontend
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 */
final class SKU_SF_WC_Frontend extends Base {
	/**
	 * Inits Class.
	 */
	public function __construct() {
		$this->add_permalink();
		/**
		 * @uses load_if_product
		 * @uses change_product_post_link
		 * @uses modify_addtocart_url
		 */
		add_action( 'parse_request', array( $this, 'load_if_product' ) );
		add_filter( 'post_type_link', array( $this, 'change_product_post_link' ), 10, 2 );
		add_filter( 'woocommerce_product_add_to_cart_url', array( &$this, 'modify_addtocart_url' ), 10, 4 );
	}

	/**
	 * Modify AddToCart URL.
	 *
	 * @param string      $url
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	public function modify_addtocart_url( $url, $product ) {
		if ( sku_sf_wc_option( 'addtocart_url' ) ) {
			if ( in_array( $product->get_type(), array( 'simple' ), true ) ) {
				if ( $product->is_purchasable() && $product->is_in_stock() ) {
					$url = remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $product->get_id(), get_sku_permalink( $product->get_id() ) ) );
				}
			}
		}
		return $url;
	}

	/**
	 * Adds Rewrite Rule.
	 */
	public function add_permalink() {
		$link_type = sku_sf_wc_option( 'sku_url_type' );
		$links     = sku_sf_wc_presets();
		if ( 'custom' === $link_type ) {
			$link = sku_sf_wc_option( 'sku_url_type_custom' );
		} else {
			$link = ( isset( $links[ $link_type ] ) ) ? $links[ $link_type ] : '';
		}

		if ( ! empty( $link ) ) {
			$link = str_replace( array( '%sku%', '%any%' ), array( '{sku}', '{any}' ), $link );
			$link = ltrim( ltrim( untrailingslashit( $link ), '/' ), '^' );
			wponion_endpoint( 'skusfwc' )->add_rewrite_rule( $link );
		}
	}

	/**
	 * Loads Product if url has SKU.
	 *
	 * @param $query
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function load_if_product( $query ) {
		if ( isset( $query->query_vars['skusfwc_sku'] ) && ! vsp_is_admin() ) {
			try {
				$id      = wc_get_product_id_by_sku( $query->query_vars['skusfwc_sku'] );
				$product = ( $id ) ? wc_get_product( $id ) : false;

				if ( $product instanceof WC_Product ) {
					$query->query_vars['page']      = '';
					$query->query_vars['post_type'] = 'product';

					if ( 'variation' === $product->get_type() && $product->get_parent_id() > 0 ) {
						$parent = wc_get_product( $product->get_parent_id() );
						if ( $parent instanceof WC_Product ) {
							$query->query_vars['product'] = $parent->get_slug();
							$query->query_vars['name']    = $parent->get_slug();
						}

						if ( is_array( $product->get_attributes() ) && ! empty( $product->get_attributes() ) ) {
							foreach ( $product->get_attributes() as $key => $val ) {
								$_REQUEST[ 'attribute_' . $key ] = $val;
							}
						}
					} else {
						$query->query_vars['product'] = $product->get_slug();
						$query->query_vars['name']    = $product->get_slug();
					}
				}
			} catch ( Exception $exception ) {

			}
		}
		return $query;
	}

	/**
	 * Changes Products Permalink.
	 *
	 * @param $url
	 * @param $post
	 *
	 * @return string
	 */
	public function change_product_post_link( $url, $post ) {
		if ( false === sku_sf_wc_option( 'modify_product_url' ) ) {
			return $url;
		}

		if ( vsp_is_admin() && false === sku_sf_wc_option( 'modify_admin_url' ) ) {
			return $url;
		}
		$_url = get_sku_permalink( $post->ID );
		return ( false === $_url ) ? $url : $_url;
	}
}
