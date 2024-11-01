<?php
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'sku_sf_wc_option' ) ) {
	/**
	 * Fetches Values From Settings Which Is Stored In DataBase.
	 *
	 * @param      $key
	 * @param bool $default
	 *
	 * @return bool|mixed
	 */
	function sku_sf_wc_option( $key, $default = false ) {
		return wpo_settings( '_sku_sf_wc', $key, $default );
	}
}

if ( ! function_exists( 'sku_sf_wc_presets' ) ) {
	/**
	 * Provides A Preset Option.
	 *
	 * @return array
	 */
	function sku_sf_wc_presets() {
		$link = get_option( 'woocommerce_permalinks', true );
		$link = trailingslashit( $link['product_base'] );
		return array(
			'preset1' => $link . 'sku/%sku%/',
			'preset2' => $link . '%sku%/',
			'custom'  => __( 'Custom', 'sku-shortlink-for-woocommerce' ),
		);
	}
}

if ( ! function_exists( 'get_sku_permalink' ) ) {
	/**
	 * Generates Product SKU Permalink Based on given product id.
	 *
	 * @param string|int $product_id
	 *
	 * @return bool
	 */
	function get_sku_permalink( $product_id ) {
		if ( ! empty( $product_id ) ) {
			$product = wc_get_product( $product_id );
			if ( $product ) {
				$format = sku_sf_wc_option( 'product_url_format' );
				if ( empty( $format ) ) {
					$links  = sku_sf_wc_presets();
					$format = sku_sf_wc_option( 'sku_url_type' );
					$format = ( 'custom' === $format ) ? sku_sf_wc_option( 'sku_url_type_custom' ) : $links[ $format ];
				}

				if ( empty( $product->get_sku() ) ) {
					return false;
				}

				$options = array(
					'%sku%'      => $product->get_sku(),
					'%postname%' => $product->get_slug(),
					'%id%'       => $product->get_id(),
					'%category%' => $product->get_category_ids(),
				);

				if ( is_array( $options['%category%'] ) && ! empty( $options['%category%'] ) ) {
					if ( ! empty( sku_sf_wc_option( 'random_category' ) ) ) {
						$count                 = ( rand( 1, count( $options['%category%'] ) ) - 1 );
						$options['%category%'] = isset( $options['%category%'][ $count ] ) ? $options['%category%'][ $count ] : $options['%category%'][0];
					} else {
						$options['%category%'] = $options['%category%'][0];
					}
					$options['%category%'] = get_term_by( 'id', $options['%category%'], 'product_cat' );
					$options['%category%'] = $options['%category%']->slug;
				}

				if ( empty( $options['%category%'] ) && is_array( $options['%category%'] ) ) {
					$options['%category%'] = '';
				}

				$format = str_replace( array_keys( $options ), array_values( $options ), untrailingslashit( $format ) );
				return trailingslashit( home_url() ) . trailingslashit( $format );
			}
		}
		return false;
	}
}
