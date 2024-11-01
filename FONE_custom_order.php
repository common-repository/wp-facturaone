<?php
//add_action( 'woocommerce_thankyou', 'FONE_verfactura_page', 20 );
add_action( 'woocommerce_view_order', 'FONE_verfactura_page', 20 );
function FONE_verfactura_page( $order_id ){  
	$value = get_post_meta( $order_id, '_facturaURL', true );
	if ($value!=''){ 
		echo '
			<h2>Factura Disponible</h2>
			<a href="'.$value.'" target="_blank">Haga click aqui para ver la factura</a>
		';
	} 
}

add_filter( 'woocommerce_account_menu_items', function($items) {
    $items['orders'] = __('Pedidos y Facturas', 'textdomain'); // Changing label for orders
    return $items;
}, 99, 1 );

//https://rudrastyh.com/woocommerce/columns.html
add_filter( 'manage_edit-shop_order_columns', 'FONE_edit_shop_order_columns' );
function FONE_edit_shop_order_columns( $columns ){
	$columns['facturaurl'] = 'Factura';
	$columns['vat_number'] = 'CIF/NIF';
    return $columns;
}
add_action( 'manage_shop_order_posts_custom_column' , 'FONE_order_items_column_cnt' );
function FONE_order_items_column_cnt( $colname ) {
	global $the_order; // the global order object
 	if( $colname == 'facturaurl' ) {
		global $post;
		$order = $post->ID;
		$value = get_post_meta( $order, '_facturaURL', true );
		if ($value!=''){echo '<a href="'.$value.'" target="_blank">Ver Factura</a>'; }
	}
	if( $colname == 'vat_number' ) {
		global $post;
		$order = $post->ID;
		$value = get_post_meta( $order, '_vat_number', true );
        if ( $value == '' ) {
			$user_id = get_post_meta( $order, '_customer_user', true ); // Obtén el user ID
			if ( $user_id ) {
				$value = get_user_meta( $user_id, 'vat_number', true );
			}
		}
		if ($value!=''){echo $value; }
	}
}
add_filter( 'woocommerce_account_orders_columns', 'add_account_orders_column', 10, 1 );
function add_account_orders_column( $columns ){
    $columns['custom-column'] = __( 'Factura', 'woocommerce' );
    return $columns;
}
add_action( 'woocommerce_my_account_my_orders_column_custom-column', 'add_account_orders_column_rows' );
function add_account_orders_column_rows( $order ) {
    // Example with a custom field
    if ( $value = $order->get_meta( '_facturaURL' ) ) {
		echo '<a href="'.esc_html( $value ).'" class="woocommerce-button button view" target="_blank">Ver Factura</a>';
    }
}



//https://stackoverflow.com/questions/45833977/woocommerce-admin-order-edit-save-post
add_action( 'woocommerce_admin_order_data_after_order_details', 'FONE_custom_code_after_order_details', 10, 1 );
function FONE_custom_code_after_order_details ( $order ) {
    $value = get_post_meta( $order->get_id(), '_facturaURL', true );
    if($value!=''){
    ?>
		<p class="form-field form-field-wide wc-customer-user">
			<label for="customer_user">Factura</label>
			<span class="select2 select2-container select2-container--default" dir="ltr">
                <a href="<?php echo esc_html( $value ); ?>" class="woocommerce-button button view" target="_blank">Ver Factura</a>
			</span>
		</p>
    <?php
    }
}



// Set a minimum dollar amount per order
if (get_option('FacturaONE_pedidominimo')>0){
	add_action( 'woocommerce_check_cart_items', 'FONE_spyr_set_min_total' );
	add_action( 'woocommerce_proceed_to_checkout', 'FONE_disable_checkout_button', 1 );
}
function FONE_spyr_set_min_total() {
    if( is_cart() || is_checkout() ) {
        global $woocommerce;
        $minimum_cart_total = get_option('FacturaONE_pedidominimo');
        $total = WC()->cart->subtotal;
        if( $total <= $minimum_cart_total  ) {
            // Display our error message
            wc_add_notice( sprintf( '<strong>Se requiere un pedido mínimo de %s para poder realizar el pedido.</strong>'
                .'<br />El valor del pedido actual es: %s',
                wc_price( $minimum_cart_total ),
                wc_price( $total ) ),
            'error' );
        }
    }
}
function FONE_disable_checkout_button() { 
    $minimum = get_option('FacturaONE_pedidominimo');
    $total = WC()->cart->cart_contents_total;
    if( $total < $minimum ){
        remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
        //echo '<a style="pointer-events: none !important;" href="#" class="checkout-button button alt wc-forward">Finalizar Compra</a>';
    }  
}