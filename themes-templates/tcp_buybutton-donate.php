<?php
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

/**
 * Template for Donation button
 *
 * @since TheCartPress Price by Customer 1.1
 */

$action = get_permalink( tcp_get_current_id( get_option( 'tcp_checkout_page_id', '' ), 'page' ) );


/**** Start editing to customise your buy buttons! */ ?>

<div class="tcp_buy_button_area tcp_buy_button_simple tcp_buy_button_<?php echo get_post_type(); ?> tcp_buy_button_donation cf <?php echo implode( ' ' , apply_filters( 'tcp_buy_button_get_product_classes', array(), $post_id ) ); ?>">

<form method="post" id="tcp_frm_<?php echo $post_id; ?>" action="<?php echo $action; ?>">

<?php do_action( 'tcp_buy_button_top', $post_id ); ?>

<div class="tcp_buy_button">

	<?php if ( ! tcp_hide_buy_button( $post_id ) ) : ?>

		<div class="tcp-add-to-cart">

			<?php tcp_the_add_to_cart_unit_field( $post_id, 1, true ); ?>

			<?php tcp_the_add_to_cart_button( $post_id, sprintf( __( 'Donate %s', 'tcp-pbc' ), tcp_get_the_price_label( $post_id ) ) ); ?>

		</div><!-- .tcp-add-to-cart -->

	<?php endif; ?>

	</div><!-- .tcp_co_second_col -->
		   
</div><!-- .tcp_buy_button .tcp_buy_button_cityoffer -->

<?php do_action( 'tcp_buy_button_bottom', $post_id ); ?>

</form>

</div><!-- tcp_buy_button_area -->
