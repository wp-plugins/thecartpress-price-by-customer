<?php
/*
Plugin Name: TheCartPress Price by customer
Plugin URI: http://extend.thecartpress.com/ecommerce-plugins/price-by-customers/
Description: Allow to set product prices by customers
Version: 1.1
Author: TheCartPress team
Author URI: http://thecartpress.com
License: GPL
Parent: thecartpress
*/

/**
 * This file is part of TheCartPress-PriceByCustomer.
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'TCPPriceByCustomer' ) ) :

class TCPPriceByCustomer {

	private function __construct() {}

	static function init_plugin() {
		add_action( 'tcp_init'		, array( __CLASS__, 'tcp_init' ) );
		add_action( 'tcp_admin_init', array( __CLASS__, 'tcp_admin_init' ) );

		// Allows to add shortcode to text widget
		add_filter( 'widget_text'	, 'do_shortcode' );
	}

	static function tcp_admin_init() {
		add_action( 'tcp_product_metabox_custom_fields'			, array( __CLASS__, 'tcp_product_metabox_custom_fields' ) );
		add_action( 'tcp_product_metabox_save_custom_fields'	, array( __CLASS__, 'tcp_product_metabox_save_custom_fields' ) );
		add_action( 'tcp_product_metabox_delete_custom_fields'	, array( __CLASS__, 'tcp_product_metabox_delete_custom_fields' ) );

	}

	static function tcp_init() {
		add_filter( 'tcp_the_add_to_cart_unit_field', array( __CLASS__, 'tcp_the_add_to_cart_unit_field' ), 20, 2 );
		add_filter( 'tcp_buy_button_unit_text'		, array( __CLASS__, 'tcp_the_add_to_cart_unit_field' ), 20, 2 ); //for 1.1.7
		add_filter( 'tcp_add_item_shopping_cart'	, array( __CLASS__, 'tcp_add_item_shopping_cart' ) );

		// Adds donation template
		add_filter( 'tcp_get_buy_buttons_paths'	, array( __CLASS__, 'tcp_get_buybutton_template' ) );

	}

	static function tcp_product_metabox_custom_fields( $post_id ) { ?>
<tr valign="top" class="tcp_price_by_customer">
	<th scope="row">
		<label for="tcp_price_by_customer"><?php _e( 'Is Price defined by customer', 'tcp-pbc' ); ?>:</label>
	</th>
	<td>
		<input type="checkbox" name="tcp_price_by_customer" id="tcp_price_by_customer" <?php if ( (bool)get_post_meta( $post_id, 'tcp_price_by_customer', true ) ) : ?>checked="true" <?php endif; ?> />
	</td>
</tr><?php
	}

	static function tcp_product_metabox_delete_custom_fields( $post_id ) {
		delete_post_meta( $post_id, 'tcp_price_by_customer' );
	}
	
	static function tcp_product_metabox_save_custom_fields( $post_id ) {
		update_post_meta( $post_id, 'tcp_price_by_customer', isset( $_POST['tcp_price_by_customer'] ) );
	}

	static function tcp_the_add_to_cart_unit_field( $html, $post_id ) {
		$is_price_by_customer = (bool)tcp_get_the_meta( 'tcp_price_by_customer', $post_id );
		if ( $is_price_by_customer ) {
			$price = tcp_get_the_price( $post_id );
			if ( $price == 0 ) {
				$price = '';
			} else {
				$price = tcp_number_format( $price );
			}
			$out = '<label>' . __( 'Type your price', 'tcp-pbc' ) . ': <input type="number" min="0" step="0.01" name="tcp_price_by_customer[]" id="tcp_price_by_customer_' . $post_id . '" value="' . $price . '" title="' . __( 'Suggested price', 'tcp-pbc' ) . '" size="5" maxlength="13"/></label>' . "\n";
		} else {
			$out = '<input type="hidden" name="tcp_price_by_customer[]" id="tcp_price_by_customer_' . $post_id . '" value="" />' . "\n";
		}
		return $html . $out;
	}

	/**
	 * Executed when an item is added to the cart
	 *
	 * @param $args = array{ 'i', 'post_id', 'count', 'unit_price', 'unit_weight' }
	 */
	static function tcp_add_item_shopping_cart( $args ) {
		$is_price_by_customer = (bool)tcp_get_the_meta( 'tcp_price_by_customer', $args['post_id'] );
		if ( $is_price_by_customer ) {
			extract( $args );
			if ( $is_price_by_customer && isset( $_REQUEST['tcp_price_by_customer'][$i] ) ) {
				$unit_price = tcp_input_number( $_REQUEST['tcp_price_by_customer'][$i] );
			}
			//$price_to_show = tcp_get_the_price_to_show( $post_id, $unit_price );
			$args = compact( 'i', 'post_id', 'count', 'unit_price', 'unit_weight' );
		}
		return $args;
	}

	/**
	 * Adds the donation template in the product admin data
	 *
	 * @since 1.1
	 */
	static function tcp_get_buybutton_template( $paths ) {

		$paths[] = array(
			'path'	=> dirname( __FILE__ ) . '/themes-templates/tcp_buybutton-donate.php',
			'label'	=> __( 'Donation button', 'tcp-pbc' ),
		);
		return $paths;
	}
}

TCPPriceByCustomer::init_plugin();

endif; // class_exists check