<?php
/*
Plugin Name: TheCartPress Price by customer
Plugin URI: http://extend.thecartpress.com/ecommerce-plugins/price-by-customers/
Description: Allow to set product prices by customers
Version: 1.0
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

class TCPPriceByCustomer {

	function tcp_product_metabox_custom_fields( $post_id ) {?>
		<tr valign="top" class="tcp_price_by_customer" <?php echo $style;?>>
			<th scope="row"><label for="tcp_price_by_customer"><?php _e( 'Is Price defined by customer', 'tcp-pbc' );?>:</label></th>
			<td><input type="checkbox" name="tcp_price_by_customer" id="tcp_price_by_customer" <?php if ( (bool)get_post_meta( $post_id, 'tcp_price_by_customer', true ) ):?>checked="true" <?php endif;?>  />
			</td>
		</tr><?php
	}

	function tcp_product_metabox_delete_custom_fields( $post_id ) {
		delete_post_meta( $post_id, 'tcp_price_by_customer' );
	}
	
	function tcp_product_metabox_save_custom_fields( $post_id ) {
		update_post_meta( $post_id, 'tcp_price_by_customer', isset( $_POST['tcp_price_by_customer'] ) );
	}

	function tcp_buy_button_options( $html, $post_id, $parent_id = 0 ) {
		$is_price_by_customer = (bool)tcp_get_the_meta( 'tcp_price_by_customer', $post_id );
		if ( $is_price_by_customer ) {
			$price = tcp_get_the_price( $post_id );
			if ( $price == 0 ) $price = '';
			$out = '<label>' . __( 'Type your price', 'tcp-pbc' ) . ': <input type="text" name="tcp_price_by_customer[]" id="tcp_price_by_customer" value="' . tcp_number_format( $price ) . '" title="' . __( 'Suggested price', 'tcp-pbc' ) . '" size="3" maxlength="13"/></label>' . "\n";
		} else
			$out = '<input type="hidden" name="tcp_price_by_customer[]" id="tcp_price_by_customer" value="' . tcp_get_the_price( $post_id ) . '" />' . "\n";
		return $out . $html;
	}

/**
 * @param $args = array{ 'i', 'post_id', 'count', 'unit_price', 'tax', 'unit_weight', 'price_to_show' }
 */
	function tcp_add_item_shopping_cart( $args ) {
		$is_price_by_customer = (bool)tcp_get_the_meta( 'tcp_price_by_customer', $args['post_id'] );
		if ( $is_price_by_customer ) {
			extract( $args );
			if ( $is_price_by_customer && isset( $_REQUEST['tcp_price_by_customer'][$i] ) ) $unit_price = tcp_input_number( $_REQUEST['tcp_price_by_customer'][$i] );
			$price_to_show = tcp_get_the_price_to_show( $post_id, $unit_price );
			$args = compact( 'i', 'post_id', 'count', 'unit_price', 'tax', 'unit_weight', 'price_to_show' );
		}
		return $args;
	}

	function __construct() {
		if ( is_admin() ) {
			add_action( 'tcp_product_metabox_custom_fields', array( $this, 'tcp_product_metabox_custom_fields' ) );
			add_action( 'tcp_product_metabox_save_custom_fields', array( $this, 'tcp_product_metabox_save_custom_fields' ) );
			add_action( 'tcp_product_metabox_delete_custom_fields', array( $this, 'tcp_product_metabox_delete_custom_fields' ) );
		} else {
			add_filter( 'tcp_buy_button_options', array( $this, 'tcp_buy_button_options' ), 20, 3 );
			add_filter( 'tcp_add_item_shopping_cart', array( $this, 'tcp_add_item_shopping_cart' ) );
		}
	}
}

$tcp_pricebycustomer = new TCPPriceByCustomer();
?>
