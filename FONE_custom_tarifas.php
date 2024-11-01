<?php
// Variable
add_filter('woocommerce_product_variation_get_regular_price', 'FONE_custom_price', 99, 2 );
add_filter('woocommerce_product_variation_get_price', 'FONE_custom_price' , 99, 2 );
// Variations (of a variable product)
add_filter('woocommerce_variation_prices_price', 'FONE_custom_variation_price', 99, 3 );
add_filter('woocommerce_variation_prices_regular_price', 'FONE_custom_variation_price', 99, 3 );
function FONE_custom_price( $price, $product ) {
    // Delete product cached price  (if needed)
    wc_delete_product_transients($product->get_id());

	$user_id = get_current_user_id(); //se necesita para que carge la info en la siguiente linea
	if($user_id>0){
		$tarifa = get_user_meta($user_id,'tarifa_user',true);
	}else{
		$tarifa = get_option("FacturaONE_catalogo_tarifa_cliente");
	}	
	
	// Get the WC_Product Object from the product ID (optional)
	//$productparent = wc_get_product($product->get_parent_id()); // If needed

	$tarifesp=FONE_get_tarifa($product->get_id(),0);
	if($tarifesp){
		return $tarifesp;
	}else if ($tarifa==1){
		return $product->get_meta('_tarifa1');
	} else if ($tarifa==2){
		return $product->get_meta('_tarifa2');
	} else if ($tarifa==3){
		return $product->get_meta('_tarifa3');
	} else if ($tarifa==4){
		return $product->get_meta('_tarifa4');
	} else if ($tarifa==5){
		return $product->get_meta('_tarifa5');
	} else {
		return $price; //en el caso sin tarifa selecciona precio de base
	}
}
function FONE_custom_variation_price( $price, $variation, $product ) {
    // Delete product cached price  (if needed)
    wc_delete_product_transients($variation->get_id());

	$user_id = get_current_user_id(); //se necesita para que carge la info en la siguiente linea
	//$tarifa = get_usermeta( $user_id, 'tarifa_user' );
	if($user_id>0){
		$tarifa = get_user_meta($user_id,'tarifa_user',true);
	}else{
		$tarifa = get_option("FacturaONE_catalogo_tarifa_cliente");
	}

	// Get the WC_Product Object from the product ID (optional)
	//$productparent = wc_get_product($variation->get_parent_id()); // If needed

	if ($tarifa==1){
		return $variation->get_meta('_tarifa1');
	} else if ($tarifa==2){
		return $variation->get_meta('_tarifa2');
	} else if ($tarifa==3){
		return $variation->get_meta('_tarifa3');
	} else if ($tarifa==4){
		return $variation->get_meta('_tarifa4');
	} else if ($tarifa==5){
		return $variation->get_meta('_tarifa5');
	} else {
		return $price; //en el caso sin tarifa selecciona precio de base
	}
}





//      https://rudrastyh.com/woocommerce/change-product-prices-in-cart.html
add_action( 'woocommerce_before_calculate_totals', 'FONE_misha_recalc_price' );
function FONE_misha_recalc_price( $cart_object ) {
	foreach ( $cart_object->get_cart() as $hash => $value ) {
		if ($value['variation_id']>0){continue;} //si es una variacion lo calcula desde variaciones
		
		$user_id = get_current_user_id(); //se necesita para que carge la info en la siguiente linea
		$tarifa = get_user_meta( get_current_user_id(), 'tarifa_user', true );
		//$tarifa = get_metadata( 'user', $user_id, 'tarifa_user', true );
		$productdata=$value['data'];
		$tarifesp=FONE_get_tarifa(0, $productdata->get_sku());
		if($tarifesp){
			$value['data']->set_price($tarifesp);
		}else if ($tarifa==1){
			$value['data']->set_price( $value['data']->get_meta('_tarifa1') );
		} else if ($tarifa==2){
			$value['data']->set_price( $value['data']->get_meta('_tarifa2') );
		} else if ($tarifa==3){
			$value['data']->set_price( $value['data']->get_meta('_tarifa3') );
		} else if ($tarifa==4){
			$value['data']->set_price( $value['data']->get_meta('_tarifa4') );
		} else if ($tarifa==5){
			$value['data']->set_price( $value['data']->get_meta('_tarifa5') );
		} else {
		}
	}
	
	//https://stackoverflow.com/questions/44950932/set-different-tax-rates-conditionally-based-on-cart-item-prices-in-woocommerce
//	foreach ( $cart_object->get_cart() as $cart_item ) {
        // get product price
//        $price = $cart_item['data']->get_price();
        // Set conditionaly based on price the tax class
//  if ( $price < 2500 )
//            $cart_item['data']->set_tax_class( 'tax-12' ); // below 2500
//        if ( $price >= 2500 )
//            $cart_item['data']->set_tax_class( 'tax-18' ); // Above 2500
//    }
	
}


add_filter( 'woocommerce_product_get_price', 'FONE_double_price', 10, 2 );
function FONE_double_price( $price, $product ){
	if ( is_page( 'cart' ) || is_page( 'checkout' ) || is_cart() || ( is_checkout() && is_ajax()) ) { return $price; } // if composite dont recalcule byjc
    if( is_shop() || is_product_category() || is_product_tag() || is_product() )
		//$tarifa3 = $product->get_meta('_tarifa3');
		//return $product->get_meta('_tarifa3');
        //return $price*3;
		$user_id = get_current_user_id(); //se necesita para que carge la info en la siguiente linea
		$tarifa = get_user_meta( get_current_user_id(), 'tarifa_user', true );
		//$tarifa = get_metadata( 'user', $user_id, 'tarifa_user', true );
		
		$tarifesp=FONE_get_tarifa($product->get_id(),0);
		if($tarifesp){
			return wc_format_decimal( $tarifesp,4 );
		}else if ($tarifa==1){
			return wc_format_decimal( $product->get_meta('_tarifa1'),4 ); //precio
		} else if ($tarifa==2){
			return wc_format_decimal( $product->get_meta('_tarifa2'),4 ); 
		} else if ($tarifa==3){
			return wc_format_decimal( $product->get_meta('_tarifa3'),4 );
		} else if ($tarifa==4){
			return wc_format_decimal( $product->get_meta('_tarifa4'),4 );
		} else if ($tarifa==5){
			return wc_format_decimal( $product->get_meta('_tarifa5'),4 );
		} else {
			return $price;
		}

    return $price;
	
//	$objProduct->set_props(array('code' => '12345', 'discount' => 10, 'discount_tax' => 5));
}
add_filter( 'woocommerce_product_get_sale_price', 'FONE_sale_price', 10, 2 );
function FONE_sale_price( $price, $product ){
    if( is_shop() || is_product_category() || is_product_tag() || is_product() )
		//$tarifa3 = $product->get_meta('_tarifa3');
		//return $product->get_meta('_tarifa3');
        //return $price*3;
		$user_id = get_current_user_id(); //se necesita para que carge la info en la siguiente linea
		$tarifa = get_user_meta( get_current_user_id(), 'tarifa_user', true );
		//$tarifa = get_metadata( 'user', $user_id, 'tarifa_user', true );
		
		$tarifesp=FONE_get_tarifa($product->get_id(),0);
		if($tarifesp){
			return $tarifesp;
		}else if ($tarifa==1){
			return $product->get_meta('_tarifa1'); //oferta
		} else if ($tarifa==2){
			return $product->get_meta('_tarifa2');
		} else if ($tarifa==3){
			return $product->get_meta('_tarifa3');
		} else if ($tarifa==4){
			return $product->get_meta('_tarifa4');
		} else if ($tarifa==5){
			return $product->get_meta('_tarifa5');
		} else {
			return $price;
		}
    return $price;
	
//	$objProduct->set_props(array('code' => '12345', 'discount' => 10, 'discount_tax' => 5));
}




/**
 * Filter the price based on user role
 */
// if composite show tarifa byjc
function FONE_th_reseller_price( $price, $product ) {
	if (class_exists( 'WC_Product_Composite' ) && get_post_meta( $product->get_id(), 'wooco_pricing', true )=='include'){
		$user_id = get_current_user_id(); //se necesita para que carge la info en la siguiente linea
		$tarifa = get_user_meta( get_current_user_id(), 'tarifa_user', true );
		
		$tarifesp=FONE_get_tarifa($product->get_id(),0);
		if($tarifesp){
			return $tarifesp;
		}else if ($tarifa==1){
			return $product->get_meta('_tarifa1');
		} else if ($tarifa==2){
			return $product->get_meta('_tarifa2');
		} else if ($tarifa==3){
			return $product->get_meta('_tarifa3');
		} else if ($tarifa==4){
			return $product->get_meta('_tarifa4');
		} else if ($tarifa==5){
			return $product->get_meta('_tarifa5');
		} else {
			return $price;
		}
		return $price;
	}else{
		return $price;
	}
}
add_filter( 'woocommerce_product_get_price', 'FONE_th_reseller_price', 10, 2 );
//add_filter( 'woocommerce_product_variation_get_price', 'FONE_th_reseller_price', 10, 2 );
add_filter( 'woocommerce_product_get_regular_price', 'FONE_th_reseller_price', 10, 2 );
//add_filter( 'woocommerce_product_get_sale_price', 'FONE_th_reseller_price', 10, 2 );





// -----------------------------------------
// 3. Store custom field value into variation data
add_filter( 'woocommerce_available_variation', 'FONE_bbloomer_add_custom_field_variation_data', 10, 3 );
function FONE_bbloomer_add_custom_field_variation_data( $data, $product, $variation ) {
	//global $wpdb;
	$tarifa = get_user_meta( get_current_user_id(), 'tarifa_user', true );
	//print_r($data);
	
	$tarifesp=FONE_get_tarifa(0,$variation->get_sku());
	if($tarifesp){ 
		$tarifa=$tarifesp;
	}else if ($tarifa==1){
		$tarifa=get_post_meta( $data[ 'variation_id' ], '_tarifa1', true );
	}else if ($tarifa==2){
		$tarifa=get_post_meta( $data[ 'variation_id' ], '_tarifa2', true );
	}else if ($tarifa==3){
		$tarifa=get_post_meta( $data[ 'variation_id' ], '_tarifa3', true );
	}else if ($tarifa==4){
		$tarifa=get_post_meta( $data[ 'variation_id' ], '_tarifa4', true );
	}else if ($tarifa==5){
		$tarifa=get_post_meta( $data[ 'variation_id' ], '_tarifa5', true );
	}else{
		$tarifa=get_post_meta( $data[ 'variation_id' ], '_regular_price', true );
	}
	
	if( get_option('woocommerce_tax_display_shop')=='incl' ) {
		$tarif = wc_price(wc_get_price_including_tax($variation, array('price' => $tarifa)));
	} else {
		$tarif = wc_price($tarifa);
	}
	
	$data['price_html'] = '<p class="price"><span class="woocommerce-Price-amount amount"><bdi>'.$tarif.' <u>Precio del articulo seleccionado</u><span class="woocommerce-Price-currencySymbol"></span></bdi></span></p>';
   return $data;
}






//muestra productos con stock y precio superior a 0
//add_filter( 'woocommerce_product_query_meta_query', 'FONE_shop_only_instock_products', 10, 2 );
function FONE_shop_only_instock_products( $meta_query, $query ) {
    // In frontend only
    if( is_admin() ) return $meta_query;

    $meta_query['relation'] = 'OR';

    $meta_query[] = array(
        'key'     => '_price',
        'value'   => '',
        'type'    => 'numeric',
        'compare' => '!='
    );
    $meta_query[] = array(
        'key'     => '_price',
        'value'   => 0,
        'type'    => 'numeric',
        'compare' => '!='
    );
    return $meta_query;
}


/**
 * si el producto tiene precio 0, no es opcion de compra
 */
add_filter( 'woocommerce_is_purchasable', 'FONE_is_purchasable', 10, 2 );
function FONE_is_purchasable( $purchasable, $product ){
    if( $product->get_price() == 0 && !$product->is_type( 'variable' ) ){
		$purchasable = false;
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );		
		//remove_action('woocommerce_after_shop_loop_item_title','woocommerce_template_loop_price',10);
		
		add_filter( 'woocommerce_variable_sale_price_html', 'FONE_businessbloomer_remove_prices', 10, 2 );
		add_filter( 'woocommerce_variable_price_html', 'FONE_businessbloomer_remove_prices', 10, 2 );
		add_filter( 'woocommerce_grouped_price_html','FONE_businessbloomer_remove_prices',10);
		//add_filter( 'woocommerce_get_price_html', 'FONE_maybe_hide_price', 10, 2 );
		add_filter( 'woocommerce_get_price_html', 'FONE_maybe_hide_price', 100, 2 );
	}
    return $purchasable;
}
function FONE_maybe_hide_price($price_html, $product){
     if($product->get_price()>0){
          return $price_html;
     }
     return '<span class="price"><span class="woocommerce-Price-amount amount"><bdi>Consultar</bdi></span></span>';
 } 


/**
 * Esconde precios usuario no registrado
 */
add_action( 'init', 'FONE_bbloomer_hide_price_add_cart_not_logged_in' );
function FONE_bbloomer_hide_price_add_cart_not_logged_in() {  
	if (is_admin()){return;}
	if (get_option('FacturaONE_preciosusuariosregistrados')==0){return;}
	if ( ! is_user_logged_in() ) {      
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 ); 
		
		add_action( 'woocommerce_single_product_summary', 'FONE_bbloomer_print_login_to_see', 31 );
		add_action( 'woocommerce_after_shop_loop_item', 'FONE_bbloomer_print_login_to_see', 11 );

		add_filter( 'woocommerce_is_purchasable', '__return_false');
		
		add_filter( 'woocommerce_variable_sale_price_html', 'FONE_businessbloomer_remove_prices', 10, 2 );
		add_filter( 'woocommerce_variable_price_html', 'FONE_businessbloomer_remove_prices', 10, 2 );
		add_filter( 'woocommerce_grouped_price_html','FONE_businessbloomer_remove_prices',10);
		add_filter( 'woocommerce_get_price_html', 'FONE_businessbloomer_remove_prices', 10, 2 );
		
		add_action('wp_head','FONE_hide_menu_price');		
	}
}
function FONE_bbloomer_print_login_to_see() {
	echo '<a href="' . get_permalink(wc_get_page_id('myaccount')) . '">' . __('Registrate para ver precios', 'theme_name') . '</a>';
}
function FONE_businessbloomer_remove_prices( $price, $product ) {
	return '';
}
function FONE_hide_menu_price() { 
	$output="<style> .cart-item { display: none !important;  } 
					 .woocommerce-Price-amount { display: none !important;} 
					 .single_add_to_cart_button { display: none !important;} 
					 .quantity.buttons_added { display: none !important;}
			</style>";
    echo $output;
}




//translate
add_filter( 'gettext', 'bbloomer_translate_woocommerce_strings', 999, 3 );
function bbloomer_translate_woocommerce_strings( $translated, $untranslated, $domain ) {
   if (! is_admin() && $domain=='wpc-composite-products' && $translated=='From'){$translated = ''; return $translated;}
//	if ( ! is_admin() && 'woocommerce' === $domain ) {
//      switch ( $translated ) {
//         case 'From':
//            $translated = 'Desde';
//            break;
//         case 'Product Description':
//            $translated = 'Product Specifications';
//            break;
//         // ETC
//      }

//   }   
   return $translated;
}
?>