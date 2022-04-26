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

 /*
 Hot Products is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 2 of the License, or
 any later version.

Hot Products is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Hot Products. If not, see http://www.gnu.org/licenses/gpl-3.0.html.
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
    function hotProductsCountHead()
    {
        if (class_exists('WooCommerce')) {
            if (is_product()) {
                global $product;
                $currentDate = date('d/m/y');
                $currentCount = get_post_meta($product->get_id(), 'hotproductscount', true);
                if (isset($currentCount[0]) && isset($currentCount[1])) {
                    $date = $currentCount[0];
                    $count = $currentCount[1];
                } else {
                    $date = $currentDate;
                    $count = 0;
                }

                if ($currentDate == $date) {
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
    function hotProductsCountProduct()
    {
        if (class_exists('WooCommerce')) {
            if (is_product()) {
                global $product;

                $enable_hotproducts		= esc_attr(get_option('enable_hotproducts'));
                //$message_placement		= esc_attr(get_option('message_placement'));
                $show_flame		= esc_attr(get_option('show_flame'));
                $text_background_colour		= esc_attr(get_option('text_background_colour'));
                $text_colour		= esc_attr(get_option('text_colour'));
                if ($enable_hotproducts) {
                    if (intval(get_post_meta($product->get_id(), 'hotproductscount', true)[1]) > 0) {
                        echo '<p class="hotProducts" style="margin:10px 0 0;background: '.(($text_background_colour) ? $text_background_colour : '#f8f8f8').';color:'.(($text_colour) ? $text_colour : '#000').';padding: 5px;display: block;">';
                        if ($show_flame) {
                            echo '<img style="width: 18px;float: left;margin-right: 5px;margin-top: 2px;" src="/wp-content/plugins/hotproducts/assets/img/flame.png"/>';
                        }
                        echo wp_sprintf('This Product Has Been Viewed <b>%s Times Today!</b>', intval(get_post_meta($product->get_id(), 'hotproductscount', true)[1]));
                        echo '</p>';
                    }
                }
            }
        }
    }
}

/**
 * Register HotProducts Settings Page
 * @return [type] [description]
 */
if (!function_exists('hotproducts_register_options_page')) {
    function hotproducts_register_options_page()
    {
        add_options_page('Hot Products', 'Hot Products', 'manage_options', 'hotproducts', 'hotproducts_options_page');
        register_setting('hotproducts-sgroup', 'enable_hotproducts');
        //register_setting('hotproducts-sgroup', 'message_placement');
        register_setting('hotproducts-sgroup', 'show_flame');
        register_setting('hotproducts-sgroup', 'text_background_colour');
        register_setting('hotproducts-sgroup', 'text_colour');
    }
    add_action('admin_menu', 'hotproducts_register_options_page');
}

/**
 * HotProducts Settings Page
 * @return [type] [description]
 */
if (!function_exists('hotproducts_options_page')) {
    function hotproducts_options_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient privileges to access this page.'));
        }
        $enable_hotproducts		= esc_attr(get_option('enable_hotproducts'));
        //$message_placement		= esc_attr(get_option('message_placement'));
        $show_flame		= esc_attr(get_option('show_flame'));
        $text_background_colour		= esc_attr(get_option('text_background_colour'));
        $text_colour		= esc_attr(get_option('text_colour')); ?>
	<div>
	    <h1>Hot Products Settings</h1>
			<p>
				Hot products settings page, here you can customise HotProducts to your liking.
			</p>
	    <form method="post" action="options.php">
	        <?php settings_fields('hotproducts-sgroup'); ?>
	        <?php do_settings_sections('hotproducts-sgroup'); ?>
					<h2 class="title">General Settings</h2>
					<table class="form-table" role="presentation">
							<tbody>
									<tr>
											<th scope="row">Enable HotProducts?</th>
											<td>
												<input id="enable_hotproducts" name="enable_hotproducts" type="checkbox" value="true" <?php echo(($enable_hotproducts == 'true') ? 'checked' : ''); ?>/>
											</td>
									</tr>
									<!-- <tr>
											<th scope="row">Message Placement</th>
											<td>
												<select id="message_placement" name="message_placement">
													<option <?php echo(($message_placement == 'above_title') ? 'selected' : ''); ?> value="above_title">Above Title</option>
													<option <?php echo(($message_placement == 'above_price') ? 'selected' : ''); ?> value="above_price">Above Price</option>
													<option <?php echo(($message_placement == 'above_basket') ? 'selected' : ''); ?> value="above_basket">Above Add To Basket</option>
													<option <?php echo(($message_placement == 'above_meta') ? 'selected' : ''); ?> value="above_meta">Above Meta</option>
												</select>
											</td>
									</tr> -->
							</tbody>
					</table>
					<h2 class="title">Visual Settings</h2>
					<table class="form-table" role="presentation">
					    <tbody>
					        <tr>
					            <th scope="row">Show Flame Image?</th>
					            <td><input id="show_flame" name="show_flame" type="checkbox" value="true" <?php echo(($show_flame == 'true') ? 'checked' : ''); ?> />
											<p class="description">To remove the flame image uncheck the checkbox.</p></td>
					        </tr>
									<tr>
					            <th scope="row">Background Colour</th>
					            <td><input id="text_background_colour" name="text_background_colour" value="<?php echo $text_background_colour; ?>" type="color"/></td>
					        </tr>
									<tr>
					            <th scope="row">Text Colour</th>
					            <td><input id="text_colour" name="text_colour" value="<?php echo $text_colour; ?>" type="color"/></td>
					        </tr>
					    </tbody>
					</table>
					<?php  submit_button(); ?>
	    </form>
	</div>
	<?php
    }
}

/**
 * Add HotProduct Coloumn To Products Admin Table
 * @var [type]
 */
if (!function_exists('hotproducts_admin_products_hotproductcount_column')) {
    add_filter('manage_edit-product_columns', 'hotproducts_admin_products_hotproductcount_column', 9999);
    function hotproducts_admin_products_hotproductcount_column($columns)
    {
        $columns['hotproducts'] = 'HP Count';
        return $columns;
    }
}

/**
 * Add HotProduct Coloumn Content
 * @var [type]
 */
if (!function_exists('hotproducts_admin_products_hotproductcount_column_content')) {
    add_action('manage_product_posts_custom_column', 'hotproducts_admin_products_hotproductcount_column_content', 10, 2);
    function hotproducts_admin_products_hotproductcount_column_content($column, $product_id)
    {
        if ($column == 'hotproducts') {
            $product = wc_get_product($product_id);
            if (isset(get_post_meta($product_id, 'hotproductscount', true)[1])) {
                echo get_post_meta($product_id, 'hotproductscount', true)[1];
            } else {
                echo '0';
            }
        }
    }
}
