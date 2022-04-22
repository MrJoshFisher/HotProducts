<?php
/**
 * Plugin Name: Hot Products
 * Plugin URL: #
 * Description: Adds a counter to products
 * Version: 1.0
 * Author: FisherINC
 * Author URI: #
 * Text Domain: hot-products
 * Requires at least: 5.7
 * Tested up to: 5.9
 * Requires PHP: 7.3
 *
 * WC requires at least: 5.8
 * WC tested up to: 6.3
 *
 */

/**
 * hotProductsCountHead
 * Check if Woocommerce is active
 * Check if is on product page
 * Get the current date (d/m/y)
 * Get the count and data from the product meta,
 * If current date and meta date is the same increment counter by 1
 * else update date in meta and reset counter.
 * @var [type]
 */
if (!function_exists('hotProductsCountHead')) {
	add_action('wp_head', 'hotProductsCountHead', 10);
	function hotProductsCountHead(){
			if (class_exists( 'WooCommerce' )) {
				if(is_product()) {
					global $product;
					$currentDate = date('d/m/y');
					$currentCount = get_post_meta($product->get_id(), 'hotproductscount',true);
					if(isset($currentCount[0]) && isset($currentCount[1])) {
						$date = $currentCount[0];
						$count = $currentCount[1];
					} else {
						$date = $currentDate;
						$count = 0;
					}

					if($currentDate == $date){
						$newCount = $count + 1;
						$countData = array(
							$date,
							$newCount
						);
					} else {
						$newCount = 0;
						$countData = array(
							$currentDate,
							$newCount
						);
					}
					update_post_meta($product->get_id(), 'hotproductscount', $countData, $currentCount);
				}
			}
	}
}

/**
 * Add text to product page.
 * Text states: This Product Has Been Viewed X Times Today!
 * @var [type]
 */
if (!function_exists('hotProductsCountProduct')) {
	add_action('woocommerce_single_product_summary', 'hotProductsCountProduct', 8);
	function hotProductsCountProduct(){
			if (class_exists( 'WooCommerce' )) {
				if(is_product()) {
					global $product;
					if(intval(get_post_meta($product->get_id(), 'hotproductscount', true)[1]) > 0) {
						echo '<p class="hotProducts" style="margin:10px 0 0;background: #f8f8f8;padding: 5px;display: block;"><img style="width: 18px;float: left;margin-right: 5px;margin-top: 2px;" src="/wp-content/plugins/hotproducts/assets/img/flame.png"/> This Product Has Been Viewed <b>'.intval(get_post_meta($product->get_id(), 'hotproductscount', true)[1]).' Times Today!</b></p>';
					}
				}
			}
	}
}
