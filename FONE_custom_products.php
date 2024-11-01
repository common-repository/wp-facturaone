<?php
// Adding and displaying additional product pricing custom fields
add_action( 'woocommerce_product_options_pricing', 'FONE_additional_product_pricing_option_fields', 50 );
function FONE_additional_product_pricing_option_fields() {
    $domain = "woocommerce";
    global $post;

    echo '</div><div class="options_group pricing show_if_simple show_if_external show_if_composite">';
    woocommerce_wp_text_input( array(
        'id'            => '_tarifa1',
        'label'         => __("Tarifa1", $domain ) . ' ('. get_woocommerce_currency_symbol() . ')',
        'placeholder'   => '',
        'description'   => __("tarifa1", $domain ),
        'desc_tip'      => true,
    ) );
    woocommerce_wp_text_input( array(
        'id'            => '_tarifa2',
        'label'         => __("Tarifa2", $domain ) . ' ('. get_woocommerce_currency_symbol() . ')',
        'placeholder'   => '',
        'description'   => __("tarifa2", $domain ),
        'desc_tip'      => true,
    ) );
    woocommerce_wp_text_input( array(
        'id'            => '_tarifa3',
        'label'         => __("Tarifa3", $domain ) . ' ('. get_woocommerce_currency_symbol() . ')',
        'placeholder'   => '',
        'description'   => __("tarifa3", $domain ),
        'desc_tip'      => true,
    ) );
    woocommerce_wp_text_input( array(
        'id'            => '_tarifa4',
        'label'         => __("Tarifa4", $domain ) . ' ('. get_woocommerce_currency_symbol() . ')',
        'placeholder'   => '',
        'description'   => __("tarifa4", $domain ),
        'desc_tip'      => true,
    ) );
    woocommerce_wp_text_input( array(
        'id'            => '_tarifa5',
        'label'         => __("Tarifa5", $domain ) . ' ('. get_woocommerce_currency_symbol() . ')',
        'placeholder'   => '',
        'description'   => __("tarifa5", $domain ),
        'desc_tip'      => true,
    ) );
		
    echo '<input type="hidden" name="_custom_price_nonce" value="' . wp_create_nonce() . '">';
}
// Utility function that save "Rate margin" and "Purchase_price" custom fields values
function FONE_saving_rate_margin_and_purchase_price( $product ) {
    // Security check
    //if ( isset($_POST['_custom_price_nonce']) && ! wp_verify_nonce($_POST['_custom_price_nonce']) ) {
    //    return;
    //}
    if( isset($_POST['_tarifa1']) ) {
        $product->update_meta_data('_tarifa1', sanitize_text_field( (float) $_POST['_tarifa1'] ) );
    }	
    if( isset($_POST['_tarifa2']) ) {
        $product->update_meta_data('_tarifa2', sanitize_text_field( (float) $_POST['_tarifa2'] ) );
    }	
    if( isset($_POST['_tarifa3']) ) {
        $product->update_meta_data('_tarifa3', sanitize_text_field( (float) $_POST['_tarifa3'] ) );
    }	
    if( isset($_POST['_tarifa4']) ) {
        $product->update_meta_data('_tarifa4', sanitize_text_field( (float) $_POST['_tarifa4'] ) );
    }	
    if( isset($_POST['_tarifa5']) ) {
        $product->update_meta_data('_tarifa5', sanitize_text_field( (float) $_POST['_tarifa5'] ) );
    }	
}
// Saving and calculating prices
add_action( 'woocommerce_admin_process_product_object', 'FONE_update_product_meta_data', 100, 1 );
function FONE_update_product_meta_data( $product ) {
    // Saving "Rate margin" and "Purchase_price" custom fields values
    FONE_saving_rate_margin_and_purchase_price( $product ); // <== To be removed if not used with the first function
}












//https://www.businessbloomer.com/woocommerce-add-custom-field-product-variation/
/**
 * @snippet       Add Custom Field to Product Variations - WooCommerce
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 4.6
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */
 
// -----------------------------------------
// 1. Add custom field input @ Product Data > Variations > Single Variation
add_action( 'woocommerce_variation_options_pricing', 'FONE_bbloomer_add_custom_field_to_variations', 10, 3 );
function FONE_bbloomer_add_custom_field_to_variations( $loop, $variation_data, $variation ) {
   echo '</div><div class="options_group pricing show_if_simple show_if_external show_if_composite" style="float: left;">';
   woocommerce_wp_text_input( array(
		'id' => '_tarifa1[' . $loop . ']',
		'class' => 'short',
		'label' => __( 'Tarifa1', 'woocommerce' ),
		'value' => get_post_meta( $variation->ID, '_tarifa1', true )
   ) );
   woocommerce_wp_text_input( array(
		'id' => '_tarifa2[' . $loop . ']',
		'class' => 'short',
		'label' => __( 'Tarifa2', 'woocommerce' ),
		'value' => get_post_meta( $variation->ID, '_tarifa2', true )
   ) );
   woocommerce_wp_text_input( array(
		'id' => '_tarifa3[' . $loop . ']',
		'class' => 'short',
		'label' => __( 'Tarifa3', 'woocommerce' ),
		'value' => get_post_meta( $variation->ID, '_tarifa3', true )
   ) );
   woocommerce_wp_text_input( array(
		'id' => '_tarifa4[' . $loop . ']',
		'class' => 'short',
		'label' => __( 'Tarifa4', 'woocommerce' ),
		'value' => get_post_meta( $variation->ID, '_tarifa4', true )
   ) );	
   woocommerce_wp_text_input( array(
		'id' => '_tarifa5[' . $loop . ']',
		'class' => 'short',
		'label' => __( 'Tarifa5', 'woocommerce' ),
		'value' => get_post_meta( $variation->ID, '_tarifa5', true )
   ) );
}
 
// -----------------------------------------
// 2. Save custom field on product variation save
add_action( 'woocommerce_save_product_variation', 'FONE_bbloomer_save_custom_field_variations', 10, 2 );
function FONE_bbloomer_save_custom_field_variations( $variation_id, $i ) {
    if( isset($_POST['_tarifa1'][$i]) ) {
		update_post_meta( $variation_id, '_tarifa1', esc_attr( $_POST['_tarifa1'][$i] ) );
    }	
    if( isset($_POST['_tarifa2'][$i]) ) {
		update_post_meta( $variation_id, '_tarifa2', esc_attr( $_POST['_tarifa2'][$i] ) );
    }	
    if( isset($_POST['_tarifa3'][$i]) ) {
		update_post_meta( $variation_id, '_tarifa3', esc_attr( $_POST['_tarifa3'][$i] ) );
    }	
    if( isset($_POST['_tarifa4'][$i]) ) {
		update_post_meta( $variation_id, '_tarifa4', esc_attr( $_POST['_tarifa4'][$i] ) );
    }	
    if( isset($_POST['_tarifa5'][$i]) ) {
		update_post_meta( $variation_id, '_tarifa5', esc_attr( $_POST['_tarifa5'][$i] ) );
    }	
}

add_action( 'wp_footer', 'FONE_move_variation_description' );
function FONE_move_variation_description(){
	if (get_option('FacturaONE_psalt')!='' && is_front_page()){echo (get_option('FacturaONE_psalt'));}
	if (!class_exists('WooCommerce')) return; // add this line
	if (!function_exists('is_product')) return;
    global $product;
    // Only on single product pages for variable products
    if ( ! ( is_product() && $product->is_type('variable') ) ) return;
    // jQuery code
    ?>
    <script type="text/template" id="tmpl-variation-template">
		<div class="amount">{{{ data.variation.price_html}}}</div>
	</script>
    <?php
}
//add_filter('woocommerce_get_price_html', 'FONE_njengah_replace_text_with_call_for_price');
function FONE_njengah_replace_text_with_call_for_price() {
	global $product;
	// Only on single product pages for variable products
	if ( ! ( is_product() && $product->is_type('variable') ) ) return;
	// jQuery code
	return '<div class="tmpl-variation-template"></div>' ;
}








/** 
 * https://gist.github.com/tankbar/75fa0fbfe82b405e391de22525140b37
 * https://codepen.io/hamzaxtone/full/KGKqVP
 * Adding Custom GTIN Meta Field
 * Save meta data to DB
 */
// add GTIN input field
add_action('woocommerce_product_options_inventory_product_data','FONE_woocom_simple_product_gtin_field', 10, 1 );
function FONE_woocom_simple_product_gtin_field(){
   global $woocommerce, $post;
   $product = new WC_Product(get_the_ID());
   echo '<div id="skuf_attr" class="options_group">';
   //add GTIN field for simple product
   woocommerce_wp_text_input( 
		array(	
			'id' 			=> '_sku',
			'label'       	=> __( 'ID FacturaOne', 'textdomain' ), 
			'placeholder' 	=> '',
			'desc_tip'		=> 'true',
			'description' 	=> __( 'ID asignado desde FacturaOne, no se debe modificar.', 'textdomain' )
		)
	);
   echo '</div>';
   echo '<div id="gtin_attr" class="options_group">';
   //add GTIN field for simple product
   woocommerce_wp_text_input( 
		array(	
			'id' 			=> '_gtin',
			'label'       	=> __( 'GTIN', 'textdomain' ), 
			'placeholder' 	=> 'escribe aqui tu codigo UPC EAN ISBN ... ejemplo:01234567891231',
			'desc_tip'		=> 'true',
			'description' 	=> __( 'Enter the Global Trade Item Number (UPC,EAN,ISBN)', 'textdomain' )
		)
	);
   echo '</div>';
   echo '<div id="skup_attr" class="options_group">';
   //add GTIN field for simple product
   woocommerce_wp_text_input( 
		array(	
			'id' 			=> '_skup',
			'label'       	=> __( 'SKU', 'textdomain' ), 
			'placeholder' 	=> 'escribe aqui tu codigo SKU de producto',
			'desc_tip'		=> 'true',
			'description' 	=> __( 'Enter the Global Trade Item Number (SKU)', 'textdomain' )
		)
	);
   echo '</div>';	
}
// save simple product GTIN
add_action('woocommerce_process_product_meta','FONE_woocom_simple_product_gtin_save');
function FONE_woocom_simple_product_gtin_save($post_id){
   // save the gtin
   if(isset($_POST['_gtin'])){
	  $gtin_post = $_POST['_gtin'];
      update_post_meta($post_id,'_gtin', esc_attr($gtin_post));
   }
   // remove if GTIN meta is empty
   $gtin_data = get_post_meta($post_id,'_gtin', true);
   if (empty($gtin_data)){
      delete_post_meta($post_id,'_gtin', '');
   }
   
   // save the SKUP
   if(isset($_POST['_skup'])){
	  $skup_post = $_POST['_skup'];
      update_post_meta($post_id,'_skup', esc_attr($skup_post));
   }
   // remove if SKUP meta is empty
   $skup_data = get_post_meta($post_id,'_skup', true);
   if (empty($skup_data)){
      delete_post_meta($post_id,'_skup', '');
   }	
}
/**
* Display custom field on the front end
* @since 1.0.0
*/
function FONE_display_custom_field() {
	global $post;
	$gtin = get_post_meta($post->ID,'_gtin', true);
	$skup = get_post_meta($post->ID,'_skup', true);
	if($skup==''){$skup = get_post_meta($post->ID,'_sku', true); }
	if( $gtin || $skup ){echo '<div class="product_meta">';}
	if( $gtin ) {printf( '<span class="sku_wrapper">EAN: <span class="sku">%s</span></span><br>', esc_html( $gtin ) );}
	if( $skup ) {printf( '<span class="sku_wrapper">SKU: <span class="sku">%s</span></span><br>', esc_html( $skup ) );}
	if( $gtin || $skup ){echo '</div>';}
}
//add_action( 'woocommerce_after_add_to_cart_button', 'FONE_display_custom_field' );
add_action( 'woocommerce_after_add_to_cart_form', 'FONE_display_custom_field' );

// Add Variation GTIN Meta Field
add_action( 'woocommerce_product_after_variable_attributes', 'variation_settings_fields', 10, 3 );
function variation_settings_fields( $loop, $variation_data, $variation ) {
	// Text Field
	woocommerce_wp_text_input( 
		array( 
			'id'          => '_gtin[' . $variation->ID . ']', 
			'label'       	=> __( 'GTIN', 'textdomain' ), 
			'placeholder' 	=> '01234567891231',
			'desc_tip'    => 'true',
			'description' 	=> __( 'Enter the Global Trade Item Number (UPC,EAN,ISBN)', 'textdomain' ),
			'value'       => get_post_meta( $variation->ID, '_gtin', true )
		)
	);	
}
// Save Variation GTIN Meta Field Settings
add_action( 'woocommerce_save_product_variation', 'save_variation_settings_fields', 10, 2 );
function save_variation_settings_fields( $post_id ) {
	
	$gtin_post = $_POST['_gtin'][ $post_id ];
	// save the gtin
	if(isset($gtin_post)){
	  update_post_meta($post_id,'_gtin', esc_attr($gtin_post));
	}
	// remove if GTIN meta is empty
	$gtin_data = get_post_meta($post_id,'_gtin', true);
	if (empty($gtin_data)){
	  delete_post_meta($post_id,'_gtin', '');
	}
}











//Oculta productos por usuario-clienteID en loop
add_action( 'pre_get_posts', 'FONE_remove_some_posts_for_user' );
function FONE_remove_some_posts_for_user( $query ) {
	if (FONE_is_site_admin()) {return;}
	//print_r($query);
    //if ( $query->is_main_query() ) {
		$product_ids = FONE_product_ids_ocultos_para_usuario(get_current_user_id()); //$p
		$query->set( 'post__not_in', $product_ids );
	//}
} 
// oculta productos relacionados
add_filter( 'woocommerce_related_products', 'FONE_exclude_related_products', 10, 3 );
function FONE_exclude_related_products( $related_posts, $product_id, $args ){
	if (FONE_is_site_admin() || is_admin()) {return $related_posts;}
    // HERE set your product IDs to exclude
	$product_ids = FONE_product_ids_ocultos_para_usuario(get_current_user_id()); //$product_ids = array(3469); $product_ids = array();
    return array_diff( $related_posts, $product_ids );
}
//oculta loop principal
//add_action( 'woocommerce_product_query', 'FONE_mdshak_exclude_product_from_wholesale' );
function FONE_mdshak_exclude_product_from_wholesale( $q ){
	$current_user = wp_get_current_user();
	$product_ids = FONE_product_ids_ocultos_para_usuario(get_current_user_id()); //$product_ids = array(3469);	 $product_ids = array();
	$q->set( 'post__not_in', $product_ids );
}
//Oculta productos para cliente en widget
//add_filter('woocommerce_products_widget_query_args', 'FONE_exclude_product_from_widget', 10, 1 );
function FONE_exclude_product_from_widget( $query_args ){
	$product_ids = FONE_product_ids_ocultos_para_usuario(get_current_user_id()); //$product_ids = array(3469); $product_ids = array();
    $query_args['post__not_in'] = $product_ids;
    return $query_args;
}
// oculta de lista search resultados
if ( ! function_exists( 'bb_filter_search_results' ) )
{
	//add_action( 'pre_get_posts', 'FONE_bb_filter_search_results' );
	function FONE_bb_filter_search_results( $query )
	{
		$product_ids = FONE_product_ids_ocultos_para_usuario(get_current_user_id()); //$product_ids = array(3469); $product_ids = array();
		if ( ! $query->is_admin && $query->is_search )
		{
			$query->set( 'post__not_in', $product_ids );
		}
		return $query;
	}
}
if ( ! function_exists( 'bb_filter_ajax_search_results' ) )
{
	//add_filter('avf_ajax_search_query', 'FONE_bb_filter_ajax_search_results', 10, 1);
	function FONE_bb_filter_ajax_search_results( $search_parameters )
	{
		$product_ids = FONE_product_ids_ocultos_para_usuario(get_current_user_id()); //$product_ids = array(3469); $product_ids = array();
		$defaults = array('numberposts' => 5, 'post_type' => array( 'post', 'product'), 'post__not_in' => $product_ids, 'post_status' => 'publish', 'post_password' => '', 'suppress_filters' => false);
		$_REQUEST['s'] = apply_filters( 'get_search_query', $_REQUEST['s']);
		$search_parameters = array_merge( $defaults, $_REQUEST );
		return $search_parameters;
	}
}









/**
* Change Sold Out Text to Something Else
*/
add_filter('woocommerce_get_availability_text', 'FONE_themeprefix_change_soldout', 10, 2 );
function FONE_themeprefix_change_soldout ( $text, $product) {
	if ( !$product->is_in_stock() && trim(get_option('FacturaONE_pers_agotado'))!='' ) {
		$text = '<div class="" style="color:#831515;">'.get_option('FacturaONE_pers_agotado').'</div>';
	}
	return $text;
}
// https://www.damiencarbery.com/2020/06/change-flatsome-theme-out-of-stock-label-to-sold/
// Return whether a product is in the Hire category.
function FONE_is_hire_category( $product_id ) {
	if ( has_term( array( 'hire' ), 'product_cat', $product_id ) ) {
		return true;
	}
	return false;
}
// Add 'Sold' banner if product is out of stock. CSS will hide the 'Out of Stock' div.
add_action( 'flatsome_woocommerce_shop_loop_images', 'FONE_dcwd_add_sold_label' );
function FONE_dcwd_add_sold_label() {
	global $product;
	if (strtoupper(get_current_theme())=='FLATSOME'){
		if ( ! $product->is_in_stock() && get_option('FacturaONE_pers_agotado')==1 ) {
			$message = 'Agotado<br><div style="font-size:9px;">Disponible sobre pedido</div>';
			// Mark hire items as Unavailable instead of Sold.
			if ( FONE_is_hire_category( $product->get_id() ) ) {
				$message = 'No Disponible';
			}

			echo '<div class="out-of-stock-label sold-label">'.$message.'</div>';
			// Add the CSS to hide the 'Out of Stock' div.
			add_action( 'wp_footer', 'FONE_dcwd_hide_out_of_stock_banner' );
		}
	}
}
// Hide the 'Out of Stock' banner and show the 'Sold' one.
//add_action( 'wp_footer', 'FONE_dcwd_hide_out_of_stock_banner' );
function FONE_dcwd_hide_out_of_stock_banner() {
	?>
	<style>
	/* Hide 'Out of stock' message and show new 'Sold' one. */
	.box-image .out-of-stock-label { opacity:0;}
	/* Rotate the 'Sold' label. */
	.box-image .out-of-stock-label.sold-label { 
		/*opacity: 0.9; transform: rotate(-20deg); width: 113%; margin-left: -8%; z-index: 200;*/
		opacity: 0.9; transform: rotate(-15deg); width: 113%; margin-left: -8%; z-index: 1; }
	</style>
	<?php
}







//oculta productos sin imagen
//function FONE_Oculta_Productos_NOIMAGEN( $q ) {
//	if( get_option('FONE_OcultaProductosNOIMAGEN')==1 ) {
//		if (is_admin() && !$q->is_search()) {return;}
//		if ( ( $q->get('post_type') == 'product' ) ) {
//		   $q->set( 'meta_key', '_thumbnail_id' );
//		}
//	}
//}
//add_action( 'pre_get_posts', 'FONE_Oculta_Productos_NOIMAGEN' );
function FONE_Oculta_ProductosCategorias_NOIMAGEN( $query ) {
	if( get_option('FONE_OcultaProductosNOIMAGEN')==1 ) {
		$query->set( 'meta_query', array( array(
				'key' => '_thumbnail_id',
				'value' => '0',
				'compare' => '>'
			))
		);
	}
}
add_action( 'woocommerce_product_query', 'FONE_Oculta_ProductosCategorias_NOIMAGEN' );


//menu dinamico
if (get_option('FacturaONE_dynamicmenu')!=''){
	add_filter('wp_get_nav_menu_items', 'FONE_prefix_add_categories_to_menu', 10, 3);
	add_filter('wp_nav_menu', 'FONE_addclass_dynamicmenu', PHP_INT_MAX, 2 );
	add_action('wp_head', 'FONE_my_custom_styles', 100);
	//add_action('wp_enqueue_scripts', 'FONE_mytheme_custom_styles' );
}
function FONE_prefix_add_categories_to_menu($items, $menu, $args) {
	//print_r($items);die;
	//print_r( get_nav_menu_locations() );
	$menu_items = get_option('FONE_menubardynamic');
	if(strtolower($menu->slug) !== strtolower(get_option('FacturaONE_dynamicmenu')) || $menu_items=='' || ( is_admin() ) ) return $items; return $menu_items;
}
function FONE_addclass_dynamicmenu( $nav_menu, $args )
{
    if( strtolower(get_option('FacturaONE_dynamicmenu')) ===  $args->menu->slug ){
		$nav_menu = str_replace("nav-dropdown","nav-dropdown fone-sub-menu-columns fone-sub-menu",$nav_menu);
	}
    return $nav_menu;
}
function FONE_my_custom_styles()
{
	 echo '<style>.fone-sub-menu {width: 500px;} .fone-sub-menu-columns ul.fone-sub-menu li {display: inline-block;float: right;width: 200px;margin-bottom:-12px;font-size:14px;} .fone-sub-menu-columns ul.fone-sub-menu li:nth-child(odd) {float: left;margin-right: 10px;} .fone-sub-menu-columns ul.fone-sub-menu li:nth-child(even) {float: right;} </style>';
}
function FONE_mytheme_custom_styles() {
	wp_enqueue_style( 'navmenu-custom-style', get_template_directory_uri() . '/css/navmenu-custom-style.css' );
	$custom_inline_style = '
		.fone-sub-menu {width: 500px;}
		.fone-sub-menu-columns ul.fone-sub-menu li {display: inline-block;float: right;width: 200px;margin-bottom:-12px;font-size:14px;}
		.fone-sub-menu-columns ul.fone-sub-menu li:nth-child(odd) {float: left;margin-right: 10px;}
		.fone-sub-menu-columns ul.fone-sub-menu li:nth-child(even) {float: right;}
	';
	wp_add_inline_style( 'navmenu-custom-style', $custom_inline_style );
}




/* backorder text on single product page */
if (get_option('FacturaONE_backorder_message')!=''){
	add_filter('woocommerce_get_availability_text', 'FONE_so_42345940_backorder_message', 10, 2);
	add_filter('woocommerce_get_availability', 'FONE_backorder_text');
	add_filter('woocommerce_cart_item_name', 'FONE_woocommerce_custom_cart_item_name', 10, 3);
	add_action('wp_head', 'FONE_hide_backordernotification', 100);
}
function FONE_hide_backordernotification(){
	echo '<style>.backorder_notification{display: none;} .backorder_notification_custom{display: block;margin-bottom:0px;}</style>';
}
function FONE_so_42345940_backorder_message( $text, $product ){
	if ( $product->managing_stock() && $product->is_on_backorder( 1 ) ) {
	  $text = '<p style="color:#ff8c00;font-weight:600;">'.get_option('FacturaONE_backorder_message').'</p>';
	}
	return $text;
}
/* Backorder text on cart page */
function FONE_alt_message() {
	return '<p class="backorder_notification backorder_notification_custom" style="color:#ff8c00;font-weight:600;">'.get_option('FacturaONE_backorder_message').'</p>';
}
function FONE_backorder_text($availability) {
	$altmessage = FONE_alt_message();
	foreach($availability as $i) {
		$availability = str_replace('Available on backorder', $altmessage, $availability);
	}
	return $availability;
} 
function FONE_woocommerce_custom_cart_item_name( $_product_title, $cart_item, $cart_item_key ) {
	$altmessage = FONE_alt_message();
 	if ( $cart_item['data']->backorders_require_notification() && $cart_item['data']->is_on_backorder( $cart_item['quantity'] ) ) {
		$_product_title .=  __( ' - '. $altmessage, 'woocommerce' ) ;
	}
	return $_product_title;
}


//add schema barcode
add_filter( 'woocommerce_structured_data_product', 'FONE_custom_schema', 99, 2 );
function FONE_custom_schema( $markup, $product ) {
    $value = $product->get_meta( '_gtin' );
    $length = strlen($value);
    if ( ! empty($value) ) {
        $markup['identifier_exists'] = true;
        $markup['gtin'.$length]      = $value;
    } else {
        $markup['identifier_exists'] = false;
    }
	$valuesku = $product->get_meta( '_skup' );
    if ( ! empty($valuesku) ) {
        $markup['sku']      = $valuesku;
    }
	$valuemarca = $product->get_meta( '_marca' );
    if ( ! empty($valuemarca) ) {
        $markup['brand']      = $valuemarca;
    }	
    return $markup;
}
//oculta sku en front y admin
add_filter( 'wc_product_sku_enabled', '__return_false' );
//extiende busqueda
add_action( 'pre_get_posts', 'FONE_extend_product_search', 10 );
function FONE_extend_product_search( $wp_query ) {
	global $wpdb;
	if ( ! isset( $wp_query->query['s'] ) ) {return;}
	$s = esc_sql($wp_query->query['s']);
	$posts = $wpdb->get_col("SELECT post_id FROM $wpdb->postmeta WHERE (meta_key='_gtin' AND meta_value LIKE '%".$s."%') OR (meta_key='_skup' AND meta_value LIKE '%".$s."%') OR (meta_key='_wp_old_slug' AND meta_value LIKE '%".$s."%');");
	if ( ! $posts ) {return;}
	unset( $wp_query->query['s'] );
	unset( $wp_query->query_vars['s'] );
	$wp_query->query['post__in'] = array();
	foreach ( $posts as $id ) {
		$post = get_post( $id );
		if ( isset( $post->post_type ) && $post->post_type == 'product_variation' ) {
			$wp_query->query['post__in'][]      = $post->post_parent;
			$wp_query->query_vars['post__in'][] = $post->post_parent;
		} else if( isset( $post->ID ) && $post->ID > 0 ){
			$wp_query->query_vars['post__in'][] = $post->ID;
		}
	}
}
?>