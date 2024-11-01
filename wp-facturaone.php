<?php
/*
Plugin Name: WP FacturaONE
Plugin URI: 
Description: La forma más fácil de trabajar con tu tienda online desde nuestro ERP FACTURAONE 
Version: 3.39
Author: FacturaOne
Author URI: https://www.FacturaOne.com
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/
require_once('FONE_custom_products.php');
require_once('FONE_custom_tarifas.php');
require_once('FONE_custom_users.php');
require_once('FONE_custom_order.php');
require_once('FONE_progressbar.php');
if (get_option('FacturaONE_widgetsubcategorias')!=''){require_once('FONE_subcategories.php');}
if (is_admin()){FONE_get_version();}

//$current_site_id = get_current_blog_id();
//if ( isset( $current_site_id ) && function_exists( 'switch_to_blog' ) ) {
//	switch_to_blog( $current_site_id );
//}
	
function FONE_url_get_contents($opcion, $checkerror = FALSE) {
	if (get_option('FacturaONE_version')){$ver=base64_encode(get_option('FacturaONE_version'));}else{$ver='0.01';}
	$arraypost = array('q' => $opcion,'e' => base64_encode(get_option('FacturaONE_EMAIL')),'a' => base64_encode(get_option('FacturaONE_APIKEY')),'i'=> base64_encode($_SERVER['REMOTE_ADDR']), 'v' => $ver, 'u' => base64_encode(get_site_url()));
	if (get_option('FacturaONE_conexionSSL')=='1'){
		$wpremotepost = array('method' => 'POST', 'timeout' => 45, 'redirection' => 5, 'httpversion' => '1.0', 'blocking' => true, 'headers' => array(), 'body' => $arraypost, 'cookies' => array() );
	}else{
		$wpremotepost = array('method' => 'POST', 'timeout' => 45, 'redirection' => 5, 'httpversion' => '1.0', 'blocking' => true, 'headers' => array(), 'sslverify' => false, 'body' => $arraypost, 'cookies' => array() );
	}
    $response = wp_safe_remote_post("https://app.ifactura.es/facturaonesync/mysqlphp.php", $wpremotepost);	
	if ( is_wp_error( $response ) && $checkerror==TRUE) {
		$body = 'error '.$response->get_error_message();
	} else {
		$body = wp_remote_retrieve_body($response);
	}
	return $body;
}
function FONE_url_get_contents2($opcion,$id) {
	if (get_option('FacturaONE_version')){$ver=base64_encode(get_option('FacturaONE_version'));}else{$ver='0.01';}
	$arraypost = array('q' => $opcion,'e' => base64_encode(get_option('FacturaONE_EMAIL')),'a' => base64_encode(get_option('FacturaONE_APIKEY')),'i'=> base64_encode($_SERVER['REMOTE_ADDR']), 'v' => $ver, 'u' => base64_encode(get_site_url()),'id' => base64_encode($id));
	if (get_option('FacturaONE_conexionSSL')=='1'){
		$wpremotepost = array('method' => 'POST', 'timeout' => 45, 'redirection' => 5, 'httpversion' => '1.0', 'blocking' => true, 'headers' => array(), 'body' => $arraypost, 'cookies' => array() );
	}else{
		$wpremotepost = array('method' => 'POST', 'timeout' => 45, 'redirection' => 5, 'httpversion' => '1.0', 'blocking' => true, 'headers' => array(), 'sslverify' => false, 'body' => $arraypost, 'cookies' => array() );
	}
    $response = wp_safe_remote_post("https://app.ifactura.es/facturaonesync/mysqlphp.php", $wpremotepost);	
	$body = wp_remote_retrieve_body($response);
	return $body;
}

//variables genereales
add_action('init', 'FONE_variables_generales');
function FONE_variables_generales(){
	if (get_option('FacturaONE_conexionSSL')==''){update_option('FacturaONE_conexionSSL',1);}
	if (get_option('FacturaONE_ivaincluido')==''){update_option('FacturaONE_ivaincluido',1);}
	if (get_option('FacturaONE_preciosusuariosregistrados')==''){update_option('FacturaONE_preciosusuariosregistrados',0);}
	if (get_option('FONE_OcultaProductosNOIMAGEN')==''){update_option('FONE_OcultaProductosNOIMAGEN',0);}
	if (get_option('FacturaONE_ajax_cicle')==''){update_option('FacturaONE_ajax_cicle',100);}
	if (get_option('FacturaONE_laststockupdatetime')==''){update_option('FacturaONE_laststockupdatetime',current_time('timestamp'));}
	if (get_option('FacturaONE_pers_agotado')==''){update_option('FacturaONE_pers_agotado','');}	
	if (get_option('FacturaONE_backorder_message')==''){update_option('FacturaONE_backorder_message','');}	
	if (is_multisite()){
		$site_id=get_current_blog_id();
		if (get_blog_option($site_id,'FacturaONE_multisiteped')==''){ 
			update_blog_option($site_id, 'FacturaONE_multisiteped', $site_id);
		}
	}
}

//carga style
function FONE_add_plugin_stylesheet() 
    {
      wp_enqueue_style( 'FONE_style', plugins_url( 'assets/FONE_main.css', __FILE__ ) );
    }
add_action('admin_print_styles', 'FONE_add_plugin_stylesheet');
//var urlupd = "'.plugins_url('wp-facturaone').'/update_stock.php'.'";

//execute script
//function FONE_scriptmainmenu() {
//}
// Add hook for admin <head></head>
//add_action( 'admin_head', 'FONE_scriptmainmenu' );
//add_action( 'wp_head', 'FONE_scriptmainmenu' );

//añade menu en woocoomerce
add_action('admin_menu', 'FONE_register_my_custom_submenu_page');
function FONE_register_my_custom_submenu_page() {
    add_submenu_page( 'woocommerce', 'FacturaONE', 'FacturaONE', 'manage_options', 'WPFacturaOne', 'FONE_facturaone_pagina_de_opciones' ); 
	//add_options_page('FacturaONE','FacturaONE','read','WPFacturaOne','FONE_facturaone_pagina_de_opciones');
}
//html con actions
function FONE_facturaone_pagina_de_opciones(){
	require_once('FONE_index.php');
}
//añade ajustes dentro lista de plugins
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'FONE_plugin_add_settings_link' );
function FONE_plugin_add_settings_link( $links ) {
    $settings_link = '<a href="admin.php?page=WPFacturaOne">' . __( 'Settings' ) . '</a>';
    array_push( $links, $settings_link );
  	return $links;
}





/**
 * Opens all product documents links in new window/tabs
 */
add_filter( 'wc_product_documents_link_target', 'wc_product_documents_open_link_in_new_window', 10, 4 );
function wc_product_documents_open_link_in_new_window( $target, $product, $section, $document ) {
	return '_blank';
}













function FONE_clientes($email_address, $password, $username, $tarifa, $client_name, $client_address_1, $client_city, $client_zip, $phone, $fone_client_id, $client_country, $client_nif, $sendresetpass=0){
	$client_country='ES'; //llega con España

	$user_id = email_exists( $email_address );
	if( ! $user_id ){
		$user_id = username_exists( $username );
	}
	
	if ( ! $user_id ){
		if ($password==''){$password = bin2hex(random_bytes(16));}
		$user_id = wp_create_user( $username, $password, $email_address );
		$user = new WP_User( $user_id );
		$user->set_role( 'customer' );
		update_user_meta($user_id, 'tarifa_user', $tarifa);
		update_user_meta($user_id, 'fone_client_id', $fone_client_id);
		update_user_meta($user_id, 'billing_company', $client_name);
		update_user_meta($user_id, 'billing_address_1', $client_address_1);
		update_user_meta($user_id, 'billing_city', $client_city);
		update_user_meta($user_id, 'billing_postcode', $client_zip);
		update_user_meta($user_id, 'billing_phone', $phone);
		update_user_meta($user_id, 'billing_country', $client_country);
		update_user_meta($user_id, 'vat_number', $client_nif);
	}else{
		if ($user_id){
			global $wpdb;
			$wpdb->update($wpdb->users, array('user_login' => $username), array('ID' => $user_id));
			wp_update_user( array( 'ID' => $user_id, 'display_name' => $username ) );
			//wp_update_user( array ( 'ID' => $user_id, 'user_login' => $username ) ) ;
			update_user_meta($user_id, 'tarifa_user', $tarifa);
			update_user_meta($user_id, 'fone_client_id', $fone_client_id);
			if(trim($client_name)!=''){update_user_meta($user_id, 'billing_company', $client_name);}
			update_user_meta($user_id, 'vat_number', $client_nif);
			if(!email_exists( $email_address )){
				wp_update_user( array('ID'=> $user_id,'user_email' => esc_attr( $email_address )) );
			}
		}
	}

	if($user_id && $sendresetpass==1){
		$user = new WP_User( intval($user_id) );
		$reset_key = get_password_reset_key( $user );
		$wc_emails = WC()->mailer()->get_emails();
		$wc_emails['WC_Email_Customer_Reset_Password']->trigger( $user->user_login, $reset_key );
	}
}
function FONE_clientes_updatepass($email_address, $password){
	if ( email_exists( $email_address ) ) {
		$user_id = email_exists( $email_address );
		wp_set_password( $password, $user_id );
	}
}
function FONE_clientes_delete($email_address, $client_id){
	if ( email_exists( $email_address ) ) {
		$user_id = email_exists( $email_address );
		if($user_id){
			require_once( ABSPATH . 'wp-admin/includes/user.php' );
			wp_delete_user($user_id);
		}
	}
}
function FONE_cliente_update($client_id, $client_tarifa, $client_nif){
	if ($client_id>0){
		$users = get_users( array( 'fields' => array( 'ID' ) ) );
		foreach($users as $user){
			$fone_client_id = get_user_meta($user->ID, 'fone_client_id', true);
			if ($fone_client_id==$client_id){
				update_user_meta($user->ID, 'tarifa_user', $client_tarifa);
				update_user_meta($user->ID, 'vat_number', $client_nif);
			}
		}
	}
}

function FONE_get_tarifa($product_id,$sku){
	if (get_option('FacturaONE_tarespecial')==0){return false;}
	$fone_client_id=get_user_meta(get_current_user_id(),'fone_client_id',true);
	if($sku==0 && $product_id>0){
		$objProduct=wc_get_product($product_id); 
		$sku=$objProduct->get_sku();
	}
	if($fone_client_id>0 && $sku>0){
		global $wpdb;
		$resultado = $wpdb->get_row("select item_client_price from ".$wpdb->prefix."fone_item_clients where client_id=".$fone_client_id." and item_lookup_id=".$sku."; " );
		if ($resultado==false){return 0;}else{return $resultado->item_client_price;}
	}else{
		return false;
	}
}

function FONE_cliente_tarifas_especiales($client_id){
	if($client_id>0){
		if (get_option('FacturaONE_ivaincluido')==0){
			$resultarifa = FONE_url_get_contents2('tarifas_especiales_client_siniva',$client_id);
		}else{
			$resultarifa = FONE_url_get_contents2('tarifas_especiales_client',$client_id);
		}	
		$response = json_decode(gzuncompress(base64_decode($resultarifa)),true);
		global $wpdb;
		$table_name = $wpdb->prefix.'fone_item_clients';
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			FONE_create_FONE_item_clientes();
		}else{
			$delsql = $wpdb->get_results("DELETE FROM `{$wpdb->base_prefix}fone_item_clients` WHERE `client_id`=".$client_id.";");
		}
		//carga tarifas
		foreach($response[0] as $values) {
			if (count($values)>0){
				foreach($values as $lineas){
					$insertsql = $wpdb->get_results("
						REPLACE INTO `{$wpdb->base_prefix}fone_item_clients` (`item_client_id`,`client_id`,`item_lookup_id`,`item_client_price`,`item_tax_rate_id`) VALUES ('".$lineas["item_client_id"]."','".$lineas["client_id"]."','".$lineas["item_lookup_id"]."','".$lineas["item_client_price"]."','".$lineas["item_tax_rate_id"]."'); " );
				}
			}
		}
		$resultado = $wpdb->get_row("select count(*) as registros from ".$wpdb->prefix."fone_item_clients; " );
		if($resultado->registros>0){update_option('FacturaONE_tarespecial',1);}else{update_option('FacturaONE_tarespecial',0);}
	}
}
register_activation_hook(__FILE__, 'FONE_create_FONE_item_clientes');
function FONE_create_FONE_item_clientes(){
  global $wpdb;
  // set the default character set and collation for the table
  $charset_collate = $wpdb->get_charset_collate();
  // Check that the table does not already exist before continuing
  $sql = "	CREATE TABLE IF NOT EXISTS `{$wpdb->base_prefix}fone_item_clients` (
				`item_client_id` INT(11) NOT NULL AUTO_INCREMENT,
				`item_create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`client_id` INT(1) NOT NULL DEFAULT '0',
				`item_lookup_id` INT(1) NOT NULL DEFAULT '0',
				`item_client_price` DECIMAL(20,4) NOT NULL DEFAULT '0.0000',
				`item_tax_rate_id` INT(11) NULL DEFAULT '0',
				PRIMARY KEY (`item_client_id`) USING BTREE,
				UNIQUE INDEX `client_id_item_lookup_id` (`client_id`, `item_lookup_id`) USING BTREE
			)
			".$charset_collate."
			ENGINE=InnoDB
			AUTO_INCREMENT=1; ";
  require_once ABSPATH . 'wp-admin/includes/upgrade.php';
  dbDelta( $sql );
  $is_error = empty( $wpdb->last_error );
  return $is_error;
}





function FONE_actualizastockproducto($item_lookup_id, $stock, $item_wp_stock_control, $item_wp_stock_visible, $item_onorder, $item_wp_backorders){
	$_product_id = wc_get_product_id_by_sku($item_lookup_id);
	if ($_product_id > 0) {
		// Get an instance of the WC_Product Object
		$objProduct = wc_get_product( $_product_id );
		if ($item_wp_stock_control==0){
			//producto siempre disponible (con o sin stock)
			$objProduct->set_stock_quantity(9999);
			$objProduct->set_stock_status('instock'); // in stock or outofstock
			$objProduct->set_manage_stock(false); 
		}else{
			//producto solo disponible si hay stock
			$objProduct->set_stock_quantity($stock);
			if ($stock>0 || $item_wp_backorders>0){ //si $item_wp_backorders = yes o notify manage=true
				$objProduct->set_stock_status('instock'); // in stock or outofstock
				if ($item_wp_stock_visible==1){
					$objProduct->set_manage_stock(true); 	
				}else{
					$objProduct->set_manage_stock(false); 	
				}
			}else{
				$objProduct->set_stock_status('outofstock'); // in stock or outofstock
				$objProduct->set_manage_stock(false);
			}
		}
		if($item_onorder==1){
			$objProduct->set_manage_stock(false);
			$objProduct->set_stock_status('outofstock'); // in stock or out of stock value
			$objProduct->set_stock_quantity(0);
		}		
		if ($item_wp_backorders==1){
			$objProduct->set_backorders('notify');
		}else if ($item_wp_backorders==2){
			$objProduct->set_backorders('yes');
		}else{
			$objProduct->set_backorders('no');
		}
		$objProduct->save(); // Save to database and sync
		//update_option('_abcbab_afacturaone_status_array','rr0'.$stock);	
	}
}

function FONE_subeproducto($baseurlfact, $deletevariaciones, $item_lookup_id, $item_name, $item_description, $item_description_web, $urlimage, $item_price, $tarifa1, $tarifa2, $tarifa3, $tarifa4, $tarifa5, $item_kgs_neto, $item_kgs_bruto, $familia_id, $subfamilia_id, $item_activado, $tax_rate_id, $variaciones, $compuestos, $item_txt_alternativo, $marca, $ean, $item_clientid, $item_onorder, $item_wp_stock_control, $item_wp_stock_visible, $item_seo_fraseobjetivo, $item_seo_metadescripcion, $item_wp_visibilidad, $item_wp_destacado, $item_slug, $item_description_web_corta, $item_wp_backorders, $item_categorias, $item_sku,$familia_web_activate,$marca_web_activate,$largo='',$ancho='',$alto='',$item_minquantity='', $item_maxquantity='', $item_stepquantity=''){
	$_product_id = wc_get_product_id_by_sku($item_lookup_id);
	$itemnameorig=$item_name;
	if (get_option('FacturaONE_nombre_producto')=='item_description'){$item_name=$item_description;}
	if (get_option('FacturaONE_nombre_producto')=='item_description_web'){$item_name=$item_description_web_corta;}
	if (get_option('FacturaONE_nombre_producto')=='item_namedescription'){$item_name=$item_name.' '.$item_description;}
	if (get_option('FacturaONE_nombre_producto')=='item_namedescription_web'){$item_name=$item_name.' '.$item_description_web_corta;}
	if (get_option('FacturaONE_nombre_producto')=='item_description_name'){$item_name=$item_description.' '.$item_name;}

	if (trim($item_seo_fraseobjetivo)==''){$item_seo_fraseobjetivo=$item_name;}
	if (trim($item_description_web_corta)==''){$item_description_web_corta = $item_description;}
	if (trim($item_seo_metadescripcion)==''){$item_seo_metadescripcion=$item_description_web_corta;}
	if (trim($item_name=='')){$item_name=$itemnameorig;}

	if ($_product_id>0){
		// si existe producto... actualiza
		$objProduct = wc_get_product( $_product_id );
		$objProduct->set_price($item_price); // Set the price
		$objProduct->set_regular_price($item_price); // Set the regular price
		if ($item_wp_stock_visible==1){
			$objProduct->set_manage_stock(true); // true or false	
		}else{
			$objProduct->set_manage_stock(false); // true or false	
		}
		if ($item_wp_stock_control==0){
			//producto siempre disponible (con o sin stock)
			$objProduct->set_stock_quantity(9999);
			$objProduct->set_stock_status('instock'); // in stock or outofstock
			$objProduct->set_manage_stock(false); // true or false	
		}
		if($item_onorder==1){
			$objProduct->set_stock_status('outofstock'); // in stock or out of stock value
			$objProduct->set_stock_quantity(0);
		}		
		if ($item_wp_backorders==1){
			$objProduct->set_backorders('notify');
		}else if ($item_wp_backorders==2){
			$objProduct->set_backorders('yes');
		}else{
			$objProduct->set_backorders('no');
		}			

		$objProduct->set_name($item_name);
		if ($item_activado==1){
			//wp_update_post( array('ID' => $_product_id, 'post_status' => 'publish'));
			$objProduct->set_status("publish");  // can be publish,draft or any wordpress post status
			if ($item_wp_visibilidad==1){
				$objProduct->set_catalog_visibility("visible"); 
			}else if($item_wp_visibilidad==2){
				$objProduct->set_catalog_visibility("catalog");  
			}else if($item_wp_visibilidad==3){
				$objProduct->set_catalog_visibility("search");  
			}else if($item_wp_visibilidad==0){
				$objProduct->set_catalog_visibility("hidden"); 
			}				
		}else{
			//wp_update_post( array('ID' => $_product_id, 'post_status' => 'draft'));
			$objProduct->set_status("draft");  // can be publish,draft or any wordpress post status
			$objProduct->set_catalog_visibility("hidden");  // can be publish,draft or any wordpress post status
		}
		if ($item_wp_destacado==1){
			$objProduct->set_featured(TRUE); //Set if the product is featured. 	
		}else{
			$objProduct->set_featured(FALSE); //Set if the product is featured. 
		}		

		if (get_option('FONE_OcultaProductosNOIMAGEN')==1 && $urlimage=='') {
			$objProduct->set_status("draft");
			$objProduct->set_catalog_visibility("hidden");  // can be publish,draft or any wordpress post status
		}			
		$objProduct->set_description($item_description_web);

		$objProduct->set_short_description($item_description_web_corta);
		$objProduct->set_weight($item_kgs_bruto);
		//$objProduct->set_props(array('code' => '12345', 'discount' => 10, 'discount_tax' => 5));
		$objProduct->update_meta_data('_tarifa1', sanitize_text_field( (float) $tarifa1 ) );
		$objProduct->update_meta_data('_tarifa2', sanitize_text_field( (float) $tarifa2 ) );
		$objProduct->update_meta_data('_tarifa3', sanitize_text_field( (float) $tarifa3 ) );
		$objProduct->update_meta_data('_tarifa4', sanitize_text_field( (float) $tarifa4 ) );
		$objProduct->update_meta_data('_tarifa5', sanitize_text_field( (float) $tarifa5 ) );
		$objProduct->update_meta_data('_gtin', sanitize_text_field( $ean ) );
		$objProduct->update_meta_data('_skup', sanitize_text_field( $item_sku ) );
		$objProduct->update_meta_data('_marca', sanitize_text_field( $marca ) );
		$objProduct->update_meta_data('_item_clientid', sanitize_text_field( $item_clientid ) );
        
        //actualiza información min max control plugin
        $objProduct->update_meta_data('min_quantity', sanitize_text_field( $item_minquantity ) );
        $objProduct->update_meta_data('max_quantity', sanitize_text_field( $item_maxquantity ) );
        $objProduct->update_meta_data('product_step', sanitize_text_field( $item_stepquantity ) );
            
		//$objProduct->set_short_description('My short description'); //Set product short description.
		//$objProduct->set_sku('U-123'); //Set SKU
		//$objProduct->set_weight(); //Set the product's weight

		//$objProduct->set_tax_status(  isset($args['tax_status']) ? $args['tax_status'] : 'taxable' );
		$tax_class = get_option('FacturaONE_taxrate_'.$tax_rate_id);
		if ($tax_class!='' && $tax_class!=-1){
			$objProduct->set_tax_class($tax_class);
		}else{
			$objProduct->set_tax_class('tasa-cero');
		}
//			if ($tax_rate_id==get_option('FacturaONE_rate_standard')){
//				$objProduct->set_tax_class('standard');
//			}else if ($tax_rate_id==get_option('FacturaONE_rate_reducedrate')){
//				$objProduct->set_tax_class('reduced-rate');
//			}else if ($tax_rate_id==get_option('FacturaONE_rate_tasacero')){
//				$objProduct->set_tax_class('tasa-cero');
//			}else{
//				$objProduct->set_tax_class('tasa-cero');
//			}

		//familia y subfamilia
		$arraycategorias=array();
		$t_id = get_option("familia_id_$familia_id");
		$t_id_subfamilia = get_option("subfamilia_id_$subfamilia_id");
		array_push($arraycategorias,$t_id);
		array_push($arraycategorias,$t_id_subfamilia);
		//añade categorias
		$itemcategorias=json_decode($item_categorias);
		if(!empty($itemcategorias->familia)){
			foreach($itemcategorias->familia as $value){array_push($arraycategorias, get_option("familia_id_$value"));}   
		}
		if(!empty($itemcategorias->subfamilia)){
			foreach($itemcategorias->subfamilia as $value){array_push($arraycategorias, get_option("subfamilia_id_$value"));}   
		}
		$objProduct->set_category_ids($arraycategorias);

//			$parent_term = term_exists( $familia_name, 'product_cat' ); // array is returned if taxonomy is given
//			$subparent_term = term_exists( $subfamilia_name, 'product_cat' ); // array is returned if taxonomy is given
//			if ($parent_term['term_id']>0){$objProduct->set_category_ids(array($parent_term['term_id'],$subparent_term['term_id']));}

		//en el caso de familia no visible oculta articulo
//		$categoria_activate = get_option("FONE_categoria_activate_term_id_".$t_id);
//		if ($categoria_activate!='' && $categoria_activate==0){
//			$objProduct->set_catalog_visibility("hidden");
//		}

		//tag = marca
		wp_set_post_terms( $_product_id, $marca, 'product_tag', true); 		
		FONE_creamarca($marca,'', $_product_id,-1);
		
		//si llega con marcaactive=0 hidden producto
//		if ( defined( 'YITH_WCBR' ) && $marca!='' ) {
//			$idmarca = term_exists($marca,YITH_WCBR::$brands_taxonomy);
//			$term_id_marca = $idmarca['term_id'];
//			$marca_activate = get_option("FONE_marca_activate_term_id_".$term_id_marca);
//			if ($marca_activate!='' && $marca_activate==0){
//				$objProduct->set_catalog_visibility("hidden");
//			}		
//		}

		if ($familia_web_activate==0 || $marca_web_activate==0){
			$objProduct->set_catalog_visibility("hidden");
			$objProduct->set_status("draft");
		}

		//medidas
		if($largo>0){$objProduct->set_length(round($largo,4)+0);}else{$objProduct->set_length("");}
		if($ancho>0){$objProduct->set_width(round($ancho,4)+0);}else{$objProduct->set_width("");}
		if($alto>0){$objProduct->set_height(round($alto,4)+0);}else{$objProduct->set_height("");}
		
		//graba
		$product_id = $objProduct->save(); // Save to database and sync 	

		if ($deletevariaciones==true && $variaciones!=''){FONE_delete_product_image_variaciones($_product_id);} //elimina variaciones anteriores}

		FONE_tipo_object_terms($_product_id, $variaciones, $compuestos, $item_price, $item_wp_stock_control, $item_wp_stock_visible);

		if (trim($item_slug)==''){$item_slug=$item_name;}
		$slug = FONE_generate_slug($item_slug, $_product_id);
		FONE_seoupdate($item_lookup_id, $slug, $item_seo_fraseobjetivo, $item_seo_metadescripcion);
	}else{
		//si no existe... crea nuevo producto
		$objProduct = new WC_Product();
		$objProduct->set_name($item_name);
		
		if ($item_activado==1){
			//wp_update_post( array('ID' => $_product_id, 'post_status' => 'publish'));
			$objProduct->set_status("publish");  // can be publish,draft or any wordpress post status
			if ($item_wp_visibilidad==1){
				$objProduct->set_catalog_visibility("visible"); 
			}else if($item_wp_visibilidad==2){
				$objProduct->set_catalog_visibility("catalog");  
			}else if($item_wp_visibilidad==3){
				$objProduct->set_catalog_visibility("search");  
			}else if($item_wp_visibilidad==0){
				$objProduct->set_catalog_visibility("hidden"); 
			}
		}else{
			//wp_update_post( array('ID' => $_product_id, 'post_status' => 'draft'));
			$objProduct->set_status("draft");  // can be publish,draft or any wordpress post status
			$objProduct->set_catalog_visibility("hidden");  // can be publish,draft or any wordpress post status
		}
		if ($item_wp_destacado==1){
			$objProduct->set_featured(TRUE); //Set if the product is featured. 	
		}else{
			$objProduct->set_featured(FALSE); //Set if the product is featured. 
		}		
		
		if (get_option('FONE_OcultaProductosNOIMAGEN')==1 && $urlimage=='') {
			$objProduct->set_status("draft");
			$objProduct->set_catalog_visibility("hidden");  // can be publish,draft or any wordpress post status
		}
		$objProduct->set_description($item_description_web);
		$objProduct->set_short_description($item_description_web_corta);
		$objProduct->set_weight($item_kgs_bruto);
		$objProduct->set_sku($item_lookup_id); //can be blank in case you don't have sku, but You can't add duplicate sku's
		$objProduct->set_price($item_price); // set product price
		$objProduct->set_regular_price($item_price); // set product regular price
		$objProduct->update_meta_data('_tarifa1', sanitize_text_field( (float) $tarifa1 ) );
		$objProduct->update_meta_data('_tarifa2', sanitize_text_field( (float) $tarifa2 ) );
		$objProduct->update_meta_data('_tarifa3', sanitize_text_field( (float) $tarifa3 ) );
		$objProduct->update_meta_data('_tarifa4', sanitize_text_field( (float) $tarifa4 ) );
		$objProduct->update_meta_data('_tarifa5', sanitize_text_field( (float) $tarifa5 ) );
		$objProduct->update_meta_data('_gtin', sanitize_text_field( $ean ) );
		$objProduct->update_meta_data('_skup', sanitize_text_field( $item_sku ) );
		$objProduct->update_meta_data('_marca', sanitize_text_field( $marca ) );
		$objProduct->update_meta_data('_item_clientid', sanitize_text_field( $item_clientid ) );
        
        //actualiza información min max control plugin
        $objProduct->update_meta_data('min_quantity', sanitize_text_field( $item_minquantity ) );
        $objProduct->update_meta_data('max_quantity', sanitize_text_field( $item_maxquantity ) );
        $objProduct->update_meta_data('product_step', sanitize_text_field( $item_stepquantity ) );
        
		//muestra stock al cliente web
		if ($item_wp_stock_visible==1){
			$objProduct->set_manage_stock(true); // true or false	
		}else{
			$objProduct->set_manage_stock(false); // true or false	
		}
		if ($item_wp_stock_control==0){
			//producto siempre disponible (con o sin stock)
			$objProduct->set_stock_quantity(9999);
			$objProduct->set_stock_status('instock'); // in stock or outofstock
			$objProduct->set_manage_stock(false); // true or false	
		}
		if($item_onorder==1){
			$objProduct->set_stock_status('outofstock'); // in stock or out of stock value
			$objProduct->set_stock_quantity(0);
		}
		if ($item_wp_backorders==1){
			$objProduct->set_backorders('notify');
		}else if ($item_wp_backorders==2){
			$objProduct->set_backorders('yes');
		}else{
			$objProduct->set_backorders('no');
		}
		$objProduct->set_reviews_allowed(true);
		$objProduct->set_sold_individually(false);
		//$objProduct->set_category_ids(array(1,2,3)); // array of category ids, You can get category id from WooCommerce Product Category Section of Wordpress Admin

		//$objProduct->set_tax_status(  isset($args['tax_status']) ? $args['tax_status'] : 'taxable' );
		$tax_class = get_option('FacturaONE_taxrate_'.$tax_rate_id);
		if ($tax_class!='' && $tax_class!=-1){
			$objProduct->set_tax_class($tax_class);
		}else{
			$objProduct->set_tax_class('tasa-cero');
		}
//		if ($tax_rate_id==get_option('FacturaONE_rate_standard')){
//			$objProduct->set_tax_class('standard');
//		}else if ($tax_rate_id==get_option('FacturaONE_rate_reducedrate')){
//			$objProduct->set_tax_class('reduced-rate');
//		}else if ($tax_rate_id==get_option('FacturaONE_rate_tasacero')){
//			$objProduct->set_tax_class('tasa-cero');
//		}else{
//			$objProduct->set_tax_class('tasa-cero');
//		}

		//familia y subfamilia
		$arraycategorias=array();
		$t_id = get_option("familia_id_$familia_id");
		$t_id_subfamilia = get_option("subfamilia_id_$subfamilia_id");
		array_push($arraycategorias,$t_id);
		array_push($arraycategorias,$t_id_subfamilia);
		//añade categorias
		$itemcategorias=json_decode($item_categorias);
		if(!empty($itemcategorias->familia)){
			foreach($itemcategorias->familia as $value){array_push($arraycategorias, get_option("familia_id_$value"));}   
		}
		if(!empty($itemcategorias->subfamilia)){
			foreach($itemcategorias->subfamilia as $value){array_push($arraycategorias, get_option("subfamilia_id_$value"));}   
		}
		$objProduct->set_category_ids($arraycategorias);	
		
//		$parent_term = term_exists( $familia_name, 'product_cat' ); // array is returned if taxonomy is given
//		$subparent_term = term_exists( $subfamilia_name, 'product_cat' ); // array is returned if taxonomy is given
//		if ($parent_term['term_id']>0){$objProduct->set_category_ids(array($parent_term['term_id'],$subparent_term['term_id']));}

		
		//en el caso de familia no visible oculta articulo
//		$categoria_activate = get_option("FONE_categoria_activate_term_id_".$t_id);
//		if ($categoria_activate!='' && $categoria_activate==0){
//			$objProduct->set_catalog_visibility("hidden");
//		}
		
		$_product_id = wc_get_product_id_by_sku($item_lookup_id);
		
		//tag = marca
		wp_set_post_terms( $_product_id, $marca, 'product_tag', true);	
		FONE_creamarca($marca,'', $_product_id,-1);
		
		//si llega con marcaactive=0 hidden producto
//		if ( defined( 'YITH_WCBR' ) && $marca!='' ) {
//			$idmarca = term_exists($marca,YITH_WCBR::$brands_taxonomy);
//			$term_id_marca = $idmarca['term_id'];
//			$marca_activate = get_option("FONE_marca_activate_term_id_".$term_id_marca);
//			if ($marca_activate!='' && $marca_activate==0){
//				$objProduct->set_catalog_visibility("hidden");
//			}		
//		}
		
		if ($familia_web_activate==0 || $marca_web_activate==0){
			$objProduct->set_catalog_visibility("hidden");
			$objProduct->set_status("draft");
		}
		
		//medidas
		if($largo>0){$objProduct->set_length(round($largo,4)+0);}else{$objProduct->set_length("");}
		if($ancho>0){$objProduct->set_width(round($ancho,4)+0);}else{$objProduct->set_width("");}
		if($alto>0){$objProduct->set_height(round($alto,4)+0);}else{$objProduct->set_height("");}
		
		//graba
		$product_id = $objProduct->save(); // it will save the product and return the generated product id
		
		FONE_tipo_object_terms($_product_id, $variaciones, $compuestos, $item_price, $item_wp_stock_control, $item_wp_stock_visible);
		
		if (trim($item_slug)==''){$item_slug=$item_name;}
		$slug = FONE_generate_slug($item_slug, $product_id);
		FONE_seoupdate($item_lookup_id, $slug, $item_seo_fraseobjetivo, $item_seo_metadescripcion);
		
		//sube imagenes detenido en importación general
		if ($urlimage!='' && 1==2){
			$_product_id = wc_get_product_id_by_sku($item_lookup_id);
			if ( $_product_id > 0 ) {
				if (strpos($urlimage, 'http') !== false) {
					$fileurl = $urlimage;
				}else{
					$fileurl = $baseurlfact."uploads/documentos/articulos/".$urlimage;
				}
				//$fileurl = 'https://www.iptecno.com/web/image/product.template/3271/image';
				//FONE_delete_product_images($_product_id, FALSE);
				FONE_uploadMedia($fileurl,$_product_id,$item_name,$item_lookup_id,$item_txt_alternativo);
			}
		}		
	}
}
function FONE_seoupdate($item_lookup_id, $slug, $item_seo_fraseobjetivo, $item_seo_metadescripcion){
	//se graba siempre para informar del _wp_old_slug en el buscador extendido, sino no encuentra pizzas
	//if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) || is_plugin_active( 'wordpress-seo-premium/wp-seo-premium.php' ) ) {
		$_product_id = wc_get_product_id_by_sku($item_lookup_id);
		if ( $_product_id > 0 ) {
			$objProduct = wc_get_product( $_product_id );
			$objProduct->update_meta_data('_yoast_wpseo_focuskw', trim($item_seo_fraseobjetivo));
			$objProduct->update_meta_data('_yoast_wpseo_metadesc', trim($item_seo_metadescripcion));
			$objProduct->update_meta_data('_wp_old_slug', $slug);
			$product_id = $objProduct->save(); // Save to database and sync 
		}
	//}
}
function FONE_generate_slug($title, $postid){
	//https://wordpress.stackexchange.com/questions/218343/how-to-generate-slugs
    $new_slug = sanitize_title( $title );
	// use this line if you have multiple posts with the same title
    //$new_slug = wp_unique_post_slug( $new_slug, $post->ID, $post->post_status, $post->post_type, $post->post_parent );
	wp_update_post(
		array (
			'ID'        	=> $postid,
			'post_name' 	=> $new_slug,
			'post_author' 	=> 1
		)
	);
	return $new_slug;
}
function FONE_createSlug($str, $delimiter = '-'){
    $unwanted_array = ['ś'=>'s', 'ą' => 'a', 'ć' => 'c', 'ç' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ź' => 'z', 'ż' => 'z',
        'Ś'=>'s', 'Ą' => 'a', 'Ć' => 'c', 'Ç' => 'c', 'Ę' => 'e', 'Ł' => 'l', 'Ń' => 'n', 'Ó' => 'o', 'Ź' => 'z', 'Ż' => 'z']; // Polish letters for example
    $str = strtr( $str, $unwanted_array );
    $slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));
    return trim($slug);
}
//function FONE_get_rate_class_name($FONETarifa){
//	global $wpdb;
//	$product_ids = $wpdb->get_row("select * from $wpdb->options where option_name like '%FacturaONE_rateclassid%'" );
//		print_r($product_ids);
//}
//echo FONE_get_rate_class_name(1);die;



//$base_url="https://wp.facturaone.com/wp-content/uploads/2020/12/1608039443.jp g";
//FONE_creamarca("samsung", $base_url, $_product_id);
//FONE_creamarca("samsung",'', $_product_id);
//FONE_creamarca("samsung",'', NULL);
//FONE_creamarca("samsung",$base_url, NULL);
function FONE_creamarca($marca, $base_url='', $_product_id=NULL, $marca_web_activate=NULL){
	//si tiene YITH_WCBR para asignar marca
	if ( defined( 'YITH_WCBR' ) && $marca!='' ) {
		$idmarca = term_exists($marca,YITH_WCBR::$brands_taxonomy);
		if ( !$idmarca ){
			$termarr     = array(
				'slug'        => $marca,
				'description' => "",
				'parent'      => 0
			);
			//si no existe la marca la crea
			$idmarca = wp_insert_term( $marca, YITH_WCBR::$brands_taxonomy, $termarr );
		}
		$term_id_marca = $idmarca['term_id'];
		if ($_product_id){
			//si llega con producto establece marca en producto
			wp_set_object_terms( $_product_id, $marca, YITH_WCBR::$brands_taxonomy );
			update_post_meta( $_product_id,'_yoast_wpseo_primary_yith_product_brand', $term_id_marca );
		}
		if ($base_url){
			//si tiene imagen la borra
			$marca_image_id = get_term_meta( $term_id_marca, 'thumbnail_id', true);
			if ($marca_image_id){wp_delete_post( $marca_image_id );}
			//sube imagen
			$attachment_id = FONE_process_attachment( $marca, $base_url, $term_id_marca );
			if ( ! is_wp_error( $attachment_id ) ) {
				yith_wcbr_update_term_meta( $term_id_marca, 'thumbnail_id', absint( $attachment_id ) );
			}
		}else{
			if ($_product_id==NULL){
				//en el caso de no actualizar producto y actualizar familia, y no tiene imagen la borra
				$marca_image_id = get_term_meta( $term_id_marca, 'thumbnail_id', true);
				if ($marca_image_id){wp_delete_post( $marca_image_id );}	
			}
		}

		if ($marca_web_activate!==-1 && $marca_web_activate!==NULL){
			update_option("FONE_marca_activate_term_id_".$term_id_marca, $marca_web_activate);
			//oculta productos
			FONE_OcultaProductos($term_id_marca, $marca_web_activate, YITH_WCBR::$brands_taxonomy);
		}

		//oculta menu
		FONE_UpdateDynamicMenu();
	}
}
function FONE_process_attachment($marca, $base_url, $post_id){
	require_once(ABSPATH . 'wp-admin/includes/media.php');
	require_once(ABSPATH . 'wp-admin/includes/file.php');
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	$image_tmp = download_url($base_url);	
	if( is_wp_error( $image_tmp ) ){
		return is_wp_error( $image_tmp );
	}else {
		$image_size = filesize($image_tmp);
		$file = array(
			'name' 			=> 'marca_'.$marca.'-'.time().'.jpg', // ex: wp-header-logo.png 
			'type' 			=> 'image/jpg',
			'tmp_name' 		=> $image_tmp,
			'error' 		=> 0,
			'size' 			=> $image_size
		);
		//This image/file will show on media page...
		$thumb_id = media_handle_sideload( $file, $post_id, 'marca_'.$marca, array('post_author'=>1));
		update_post_meta( $thumb_id,'_wp_attached_file_brand', $post_id );
		return $thumb_id;
	}
}

function FONE_tipo_object_terms($_product_id, $variaciones, $compuestos, $item_price, $item_wp_stock_control, $item_wp_stock_visible){
	global $wpdb;
	$product_ids = $wpdb->get_col(" delete FROM $wpdb->posts WHERE post_parent=".$_product_id." AND post_type='product_variation';" );
	if ($variaciones!=''){
		//en el caso de producto variable
		wp_remove_object_terms( $_product_id, 'simple', 'product_type' );
		wp_remove_object_terms( $_product_id, 'composite', 'product_type' );
		wp_set_object_terms( $_product_id, 'variable', 'product_type', true );
		FONE_variaciones($_product_id, $variaciones, $item_price, $item_wp_stock_control, $item_wp_stock_visible);
	}else if ($compuestos!=''){
		wp_remove_object_terms( $_product_id, 'simple', 'product_type' );
		wp_remove_object_terms( $_product_id, 'variable', 'product_type' );
		wp_set_object_terms( $_product_id, 'composite ', 'product_type', true );
		FONE_componentes($_product_id, $compuestos);
	}else{
		//en el caso de ser producto simple
		wp_remove_object_terms( $_product_id, 'variable', 'product_type' );
		wp_remove_object_terms( $_product_id, 'composite', 'product_type' );
		wp_set_object_terms( $_product_id, 'simple', 'product_type', true );
	}
}

function FONE_componentes($_product_id, $compuestos){
	//extrae compuestos
	$compuestos = str_replace('"', '*A/1*', $compuestos);
	$compuestos = str_replace("'", '"', $compuestos);
	$compuestos = str_replace("*A/1*", "''", $compuestos);
	$array_from_compuestos = json_decode($compuestos, true);
	//carga array
	$wooco_components = array();
	foreach ($array_from_compuestos as $key => $value) {
		$productid = wc_get_product_id_by_sku($value['sku']);
		array_push($wooco_components, array(
					'name'         	=> $value['name'],
					'desc'        	=> $value['description'],
					'type'        	=> 'products',
					'orderby'       => 'default',
					'order'        	=> 'default',
					'products'      => $productid,
					'categories'    => 'abc',
					'tags'        	=> '',
					'exclude'       => '',
					'default'       => 'null',
					'optional'      => 'no',
					'price'        	=> $value['price'],
					'qty'        	=> $value['quantity'],
					'custom_qty'    => 'no',
					'min'        	=> '0',
					'max'        	=> '1'
				)
	   );
	}
	$wooco_pricing = 'include'; //exclude  include  only
	$wooco_discount_percent = '';
	$wooco_qty_min = '';
	$wooco_qty_max = '';
	$wooco_same_products = 'allow';
	$wooco_shipping_fee = 'whole';
	$wooco_custom_price = '';
	$wooco_before_text = '';
	$wooco_after_text = '';
	if ( isset( $wooco_components ) ) {
		update_post_meta( $_product_id, 'wooco_components', $wooco_components  );
	} else {
		delete_post_meta( $_product_id, 'wooco_components' );
	}
	if ( isset( $wooco_pricing ) ) {
		update_post_meta( $_product_id, 'wooco_pricing', sanitize_text_field( $wooco_pricing ) );
	}
	if ( isset( $wooco_discount_percent ) ) {
		update_post_meta( $_product_id, 'wooco_discount_percent', sanitize_text_field( $wooco_discount_percent ) );
	}
	if ( isset( $wooco_qty_min ) ) {
		update_post_meta( $_product_id, 'wooco_qty_min', sanitize_text_field( $wooco_qty_min ) );
	}
	if ( isset( $wooco_qty_max ) ) {
		update_post_meta( $_product_id, 'wooco_qty_max', sanitize_text_field( $wooco_qty_max ) );
	}
	if ( isset( $wooco_same_products ) ) {
		update_post_meta( $_product_id, 'wooco_same_products', sanitize_text_field( $wooco_same_products ) );
	}
	if ( isset( $wooco_shipping_fee ) ) {
		update_post_meta( $_product_id, 'wooco_shipping_fee', sanitize_text_field( $wooco_shipping_fee ) );
	}
	if ( ! empty( $wooco_custom_price ) ) {
		update_post_meta( $_product_id, 'wooco_custom_price', addslashes( $wooco_custom_price ) );
	} else {
		delete_post_meta( $_product_id, 'wooco_custom_price' );
	}
	if ( ! empty( $wooco_before_text ) ) {
		update_post_meta( $_product_id, 'wooco_before_text', addslashes( $wooco_before_text ) );
	} else {
		delete_post_meta( $_product_id, 'wooco_before_text' );
	}
	if ( ! empty( $wooco_after_text ) ) {
		update_post_meta( $_product_id, 'wooco_after_text', addslashes( $wooco_after_text ) );
	} else {
		delete_post_meta( $_product_id, 'wooco_after_text' );
	}
}

function FONE_variaciones($_product_id, $variaciones, $item_price, $item_wp_stock_control, $item_wp_stock_visible){
//https://stackoverflow.com/questions/47518333/create-programmatically-a-variable-product-and-two-new-attributes-in-woocommerce			
//https://stackoverflow.com/questions/53587200/set-the-product-type-when-creating-a-product-programmatically-in-woocommerce-3			
//https://gist.github.com/Musilda/b77a94b43dfe508d6a30d0ecc20051bb	
//https://woocommerce.wp-a2z.org/oik_api/delete_variations/

	//elimina media imagenes de las variaciones anteriores
	$product = wc_get_product($_product_id);
	$children_ids = $product->get_children(); // Get children product variation IDs in an array
	foreach($children_ids as $childrenid){
		$attachment_id = get_post_thumbnail_id( $childrenid );
		wp_delete_attachment($attachment_id, true);
	}

	//borrado ///////////////////////////////////////////////////////////////////////
	$data_store = WC_Data_Store::load( 'product-variable' );
	$data_store->delete_variations( $_product_id, true );

	//extrae variaciones
	$variaciones = str_replace('"', '*A/1*', $variaciones);
	$variaciones = str_replace("'", '"', $variaciones);
	$variaciones = str_replace("*A/1*", "''", $variaciones);
	$array_from_variaciones = json_decode($variaciones, true);

	//limpia valores 0
	$arrayvariaciones = array();		
	foreach($array_from_variaciones as $key => $subarray)
	{ 
		foreach($subarray as $key => $value) 
		{ 
			if($value === '0') 
			{ 
				unset($subarray[$key]); 
			} 
		} 
		array_push($arrayvariaciones, $subarray);
	} 

	//crea atributos generales la primera linea cabecera y coge los terminos para usarlos mas adelante
	$terminos=array();
	foreach ($arrayvariaciones[0] as $key => $value) {
		if ($key!='talla' && $key!='color'){continue;} //se salta si no es talla y color
		FONEcreate_global_attribute($key,$key);

		//carga terminos de cada atributo
		$temparray=array();
		foreach ($arrayvariaciones as $element){array_push($temparray,$element[$key]);}
		$terminos[$key]=$temparray;
	}
	//establece terminos dentro del producto
	foreach ($terminos as $key => $value) {
		wp_set_object_terms($_product_id, $value, 'pa_'.$key , false);
	}

	//crea atributos dentro producto desde primera linea cabecera (pa_color y pa_talla)
	$upc = array();
	foreach ($arrayvariaciones[0] as $key => $value) {
		if ($key!='talla' && $key!='color'){continue;} //se salta si no es talla y color
		$upc = $upc + array(	'pa_'.$key=>array(	'name'=>'pa_'.$key,
													'value'=>'',
													'is_visible' => '1', 
													'is_variation' => '1',
													'is_taxonomy' => '1'
													),
						   );
	}			
	update_post_meta( $_product_id,'_product_attributes',$upc);

	//crea variaciones en producto
	foreach ($arrayvariaciones as $key => $value) {
		$sku = $value['sku'];unset($value['sku']); //necesita eliminar sku para que no entre dentro de los attributes
		$item_stock = $value['item_stock'];unset($value['item_stock']); 
		$item_price = $value['item_price'];unset($value['item_price']); 
		$item_tarifa2 = $value['item_tarifa2'];unset($value['item_tarifa2']); 
		$item_tarifa3 = $value['item_tarifa3'];unset($value['item_tarifa3']); 
		$item_tarifa4 = $value['item_tarifa4'];unset($value['item_tarifa4']); 
		$item_tarifa5 = $value['item_tarifa5'];unset($value['item_tarifa5']); 

		//elimina variacion producto si existe
		$existe_sku = wc_get_product_id_by_sku($sku);
		if ($existe_sku){
			$product = wc_get_product($existe_sku);
			$product->delete(true);
			$existe_sku = wc_get_product_id_by_sku($sku);
		}
		
		if (!$existe_sku){
			// update_option('tttrrrratest'.rand(10,1000000),$value); 
			// The variation data
			$variation_data =  array(	'attributes' 	=> $value,
										'sku'           => $sku,
										'regular_price' => $item_price,
										'sale_price'    => '',
										'stock_qty'     => $item_stock,
									);
			$variation_id = FONE_create_product_variation( $_product_id, $variation_data, $item_wp_stock_control, $item_wp_stock_visible);

			if ($variation_id>0){
				update_post_meta( $variation_id, '_tarifa1', $item_price );
				update_post_meta( $variation_id, '_tarifa2', $item_tarifa2 );
				update_post_meta( $variation_id, '_tarifa3', $item_tarifa3 );
				update_post_meta( $variation_id, '_tarifa4', $item_tarifa4 );
				update_post_meta( $variation_id, '_tarifa5', $item_tarifa5 );
			}
		}
	}
}
function FONE_create_product_variation( $product_id, $variation_data, $item_wp_stock_control, $item_wp_stock_visible ){
    // Get the Variable product object (parent)
    $product = wc_get_product($product_id);

    $variation_post = array(
        'post_title'  => $product->get_title(),
        'post_name'   => 'product-'.$product_id.'-variation',
        'post_status' => 'publish',
        'post_parent' => $product_id,
        'post_type'   => 'product_variation',
        'guid'        => $product->get_permalink()
    );

    // Creating the product variation
    $variation_id = wp_insert_post( $variation_post );

    // Get an instance of the WC_Product_Variation object
    $variation = new WC_Product_Variation( $variation_id );

    // Iterating through the variations attributes
    foreach ($variation_data['attributes'] as $attribute => $term_name )
    {
        $taxonomy = 'pa_'.$attribute; // The attribute taxonomy

        // If taxonomy doesn't exists we create it (Thanks to Carl F. Corneil)
        if( ! taxonomy_exists( $taxonomy ) ){
            register_taxonomy(
                $taxonomy,
               'product_variation',
                array(
                    'hierarchical' => false,
                    'label' => ucfirst( $attribute ),
                    'query_var' => true,
                    'rewrite' => array( 'slug' => sanitize_title($attribute) ), // The base slug
                )
            );
        }

        // Check if the Term name exist and if not we create it.
        if( ! term_exists( $term_name, $taxonomy ) )
            wp_insert_term( $term_name, $taxonomy ); // Create the term

        $term_slug = get_term_by('name', $term_name, $taxonomy )->slug; // Get the term slug

        // Get the post Terms names from the parent variable product.
        $post_term_names =  wp_get_post_terms( $product_id, $taxonomy, array('fields' => 'names') );

        // Check if the post term exist and if not we set it in the parent variable product.
        if( ! in_array( $term_name, $post_term_names ) )
            wp_set_post_terms( $product_id, $term_name, $taxonomy, true );

        // Set/save the attribute data in the product variation
        update_post_meta( $variation_id, 'attribute_'.$taxonomy, $term_slug );
    }

    ## Set/save all other data

    // SKU
    if( ! empty( $variation_data['sku'] ) )
        $variation->set_sku( $variation_data['sku'] );

    // Prices
    if( empty( $variation_data['sale_price'] ) ){
        $variation->set_price( $variation_data['regular_price'] );
    } else {
        $variation->set_price( $variation_data['sale_price'] );
        $variation->set_sale_price( $variation_data['sale_price'] );
    }
    $variation->set_regular_price( $variation_data['regular_price'] );

    // Stock
    if( ! empty($variation_data['stock_qty'])  ){
		if ($variation_data['stock_qty']>0){
			$variation->set_stock_quantity( $variation_data['stock_qty'] );
			if ($item_wp_stock_visible==1){
				$variation->set_manage_stock(true); 	
			}else{
				$variation->set_manage_stock(false); 	
			}
			//$variation->set_stock_status('');
			$variation->set_stock_status('instock');
		}else{
			$variation->set_stock_quantity(0);
			$variation->set_manage_stock(false); 	
			$variation->set_stock_status('outofstock');
		}
	}
	if ($item_wp_stock_control==0){
		//producto siempre disponible (con o sin stock)
		$variation->set_stock_quantity(9999);
		$variation->set_stock_status('instock'); // in stock or outofstock
		$variation->set_manage_stock(false); // true or false	
	}
	
    $variation->set_weight(''); // weight (reseting)

    $variation->save(); // Save the data
	
	return $variation_id;
}
function FONEcreate_global_attribute($name, $slug)
{

    $taxonomy_name = wc_attribute_taxonomy_name( $slug );

    if (taxonomy_exists($taxonomy_name))
    {
        return wc_attribute_taxonomy_id_by_name($slug);
    }

    //logg("Creating a new Taxonomy! `".$taxonomy_name."` with name/label `".$name."` and slug `".$slug.'`');

    $attribute_id = wc_create_attribute( array(
        'name'         => $name,
        'slug'         => $slug,
        'type'         => 'select',
        'order_by'     => 'menu_order',
        'has_archives' => false,
    ) );

    //Register it as a wordpress taxonomy for just this session. Later on this will be loaded from the woocommerce taxonomy table.
    register_taxonomy(
        $taxonomy_name,
        apply_filters( 'woocommerce_taxonomy_objects_' . $taxonomy_name, array( 'product' ) ),
        apply_filters( 'woocommerce_taxonomy_args_' . $taxonomy_name, array(
            'labels'       => array(
                'name' => $name,
            ),
            'hierarchical' => true,
            'show_ui'      => false,
            'query_var'    => true,
            'rewrite'      => false,
        ) )
    );

    //Clear caches
    delete_transient( 'wc_attribute_taxonomies' );

    return $attribute_id;
}






function FONE_categoria($item_lookup_id,$familia_name,$subfamilia_name){
	//nousado
	$_product_id = wc_get_product_id_by_sku($item_lookup_id);
	if ( $_product_id > 0 ) {
		$objProduct = wc_get_product( $_product_id );
		//familia
		$parent_term = term_exists( $familia_name, 'product_cat' ); // array is returned if taxonomy is given
		$subparent_term = term_exists( $subfamilia_name, 'product_cat' ); // array is returned if taxonomy is given
		if ($parent_term['term_id']>0){$objProduct->set_category_ids(array($parent_term['term_id'],$subparent_term['term_id']));}
		$product_id = $objProduct->save(); // Save to database and sync
	}
}
function FONE_subeimagen($baseurlfact,$item_lookup_id,$urlimage,$item_name,$item_txt_alternativo,$borragaleriaimag,$sobreescribe){
	$urlimage=trim($urlimage);
	$item_lookup_id=trim($item_lookup_id);
	$_product_id = wc_get_product_id_by_sku($item_lookup_id);
	if ($_product_id>0 && $sobreescribe==true){
		//elimina siempre variaciones anteriores
		//FONE_delete_product_image_variaciones($_product_id);
		FONE_delete_product_images($_product_id, $borragaleriaimag);
	}
	//sube imagenes
	if ($urlimage!='' && $item_lookup_id!=''){
		if ($_product_id>0){
			//$objProduct = wc_get_product( $_product_id );
			//set imagen
			$userdb = get_option('FacturaONE_userdb');
			if ($userdb==''){
				//carga userdb para cachepage
				$resultado = FONE_url_get_contents('userdb');
				$userdb = gzuncompress(base64_decode($resultado));
				update_option('FacturaONE_userdb',$userdb);
			}
			if (get_option('FacturaONE_conexionSSL')=='1'){$urlssl='https';}else{$urlssl='http';}
			
			if (strpos(strtoupper($urlimage), 'HTTP') !== false) {
				//se usa el time para evitar la cache del wp
				$fileurl = $urlssl.'://app.ifactura.es/externalpics/cropimag.php?i=' . $item_lookup_id . '&time='.time().'&d=' . $userdb . '&f=1' . '&n=' . base64_encode($urlimage); 
				
				//$imagenthumb = $exterca.'cropimag.php?i=' . $row['item_lookup_id'] . '&d=' . $userdb . '&n=' . base64_encode($row['IMAGEN']);
				//$fileurl = $urlimage;
			}else{
				$fileurl = $baseurlfact."uploads/documentos/articulos/".$urlimage;
				if (get_option('FacturaONE_conexionSSL')!='1'){
					$fileurl = $urlssl.'://app.ifactura.es/externalpics/cropimag.php?i=' . $item_lookup_id . '&time='.time().'&d=' . $userdb . '&f=1' . '&n=' . base64_encode($fileurl); 
				}
			}
			//$fileurl = 'https://www.iptecno.com/web/image/product.template/3271/image';
			//echo $item_lookup_id. ' ' . $fileurl . '<br>';
			// above function FONE_upload Media, I have written which takes an image url as an argument and upload image to wordpress and returns the media id, later we will use this id to assign the image to product.
			
			if(trim($item_txt_alternativo)==''){$item_txt_alternativo = str_replace("_"," ", str_replace("-"," ",$item_name) );}
				
			if ($sobreescribe==true){
				FONE_uploadMedia($fileurl,$_product_id,$item_name,$item_lookup_id,$item_txt_alternativo);
			}else{
				$image = wp_get_attachment_image_src( get_post_thumbnail_id($_product_id), 'single-post-thumbnail' );
				if (!$image){
					FONE_uploadMedia($fileurl,$_product_id,$item_name,$item_lookup_id,$item_txt_alternativo);
				}
			}
		}
//	}else if ($urlimage=='' && $item_lookup_id!=''){
//		$_product_id = wc_get_product_id_by_sku($item_lookup_id);
//		if ($_product_id>0){
//			FONE_delete_product_images($_product_id, $borragaleriaimag);
//		}
	}
}

function FONE_subeimagen_variacion($baseurlfact, $hijo_item_lookup_id, $urlimage, $pic_item_name_variacion, $pic_item_txt_alternativo_variacion,$sobreescribe){
	$urlimage=trim($urlimage);
	//sube imagenes
	if ($urlimage!='' && $hijo_item_lookup_id!=''){
		$variation_id = wc_get_product_id_by_sku($hijo_item_lookup_id);
		if ($variation_id>0){
			if ($sobreescribe==true){
				//FONE_delete_product_image_variaciones($variation_id);
				//FONE_delete_product_images($variation_id, false);
				$variation = new WC_Product_Variation($variation_id);
				if ( $variation ) {
					$featured_image_id = $variation->get_image_id();
					wp_delete_post( $featured_image_id );
				}
			}	
			//set imagen
			$userdb = get_option('FacturaONE_userdb');
			if ($userdb==''){
				//carga userdb para cachepage
				$resultado = FONE_url_get_contents('userdb');
				$userdb = gzuncompress(base64_decode($resultado));
				update_option('FacturaONE_userdb',$userdb);
			}			
			if (strpos(strtoupper($urlimage), 'HTTP') !== false) {
				//se usa el time para evitar la cache del wp
				$fileurl = 'https://app.ifactura.es/externalpics/cropimag.php?i=' . $hijo_item_lookup_id . '&time='.time().'&d=' . $userdb . '&f=1' . '&n=' . base64_encode($urlimage); 
			}else{
				$fileurl = $baseurlfact."uploads/documentos/articulos/".$urlimage;
			}
			
			if(trim($pic_item_txt_alternativo_variacion)==''){$pic_item_txt_alternativo_variacion = str_replace("_"," ", str_replace("-"," ",$pic_item_name_variacion) );}
			
			if ($sobreescribe==true){
				FONE_uploadMedia($fileurl,$variation_id,$pic_item_name_variacion,$hijo_item_lookup_id,$pic_item_txt_alternativo_variacion);
			}else{
				if (get_post_thumbnail_id($variation_id)==0){
					FONE_uploadMedia($fileurl,$variation_id,$pic_item_name_variacion,$hijo_item_lookup_id,$pic_item_txt_alternativo_variacion);
				}
			}
			//update_option('aaaaaaaaaaa'.$variation_id, get_post_thumbnail_id($variation_id));
		}
	}
}
function FONE_uploadMedia($image_url,$post_id,$item_name,$item_lookup_id,$item_txt_alternativo){
	require_once(ABSPATH . 'wp-admin/includes/media.php');
	require_once(ABSPATH . 'wp-admin/includes/file.php');
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	$image_tmp = download_url($image_url);	

	if( is_wp_error( $image_tmp ) ){
        //echo "<br> Image Download Fail:";
		FONE_delete_product_images($post_id, FALSE);
    }else {
        $image_size = filesize($image_tmp);
        //$image_name = basename($image_url) . ".j pg"; // .j pg optional
		//Download complete now upload in your project
		$filename = strtolower(str_replace(" ","-", str_replace("_","-",$item_name )));
        $file = array(
			'name' 			=> trim($filename).'-'.time().'.jpg', // ex: wp-header-logo.png
//			'name' 			=> trim($item_name).'.jpg', // ex: wp-header-logo.png
			'type' 			=> 'image/jpg',
			'tmp_name' 		=> $image_tmp,
			'error' 		=> 0,
			'size' 			=> $image_size
        );
		
		//wp_delete_file($thumb_id);
		//delete_post_thumbnail( $post_id ); // pass the post ID
		
		if(trim($item_txt_alternativo)==''){$item_txt_alternativo = str_replace("_"," ", str_replace("-"," ",$item_name) );}
		
        //This image/file will show on media page...
        $thumb_id = media_handle_sideload( $file, $post_id, $item_name, array('post_author'=>1));
		if ($item_txt_alternativo){update_post_meta($thumb_id, '_wp_attachment_image_alt', $item_txt_alternativo );}
        set_post_thumbnail($post_id, $thumb_id); //optional
	}
}

function FONE_familias($familia, $subfamilia, $familia_id, $subfamilia_id, $familia_web_activate, $familia_wpseo_title, $familia_wpseo_focuskw, $familia_wpseo_desc, $familia_slug){
	//update_term_meta(719, "abc", sanitize_text_field("deffff"));
	if ($familia!='' && $subfamilia==''){
		//busca el indice familia_id en options
		$t_id = get_option("familia_id_$familia_id");
		if ($t_id!=''){
			//si existe option_name verifica si todavia existe categoria creada
			if (!get_term_by( 'term_id', $t_id, 'product_cat')){
				//si no existe borra variable para que cree la familia
				$t_id='';
			}
		}else{
			//busca por string_name $familia (en casos antiguos versiones wp)
			$parent_term = term_exists( $familia, 'product_cat' ); // array is returned if taxonomy is given
			if ( $parent_term !== 0 && $parent_term !== null ) { //echo $parent_term['term_id']; 
				//si existe
				update_option("familia_id_$familia_id", sanitize_text_field($parent_term['term_id']));
				$t_id = $parent_term['term_id'];
			}
		}	
		//crea familia
		if($familia_slug!=''){$slug=$familia_slug;}else{$slug=$familia;}
		if ( $t_id!='' ) { //echo $parent_term['term_id']; 
			//modifica familia
			//$ret = wp_update_term($t_id, $familia, array('slug' => $familia) );
			$ret = wp_update_term($t_id, "product_cat", array('name' => $familia, 'slug' => $slug, 'description'=> $familia_wpseo_desc), $familia);			
		}else{
			//crea familia
			$term_data = wp_insert_term(
				$familia, // the term 
				'product_cat', // the Woocommerce product category taxonomy
				array( // (optional)
					'description'=> $familia_wpseo_desc, // (optional)
					'slug' => $slug, // optional
					''
					//'parent'=> $parent_term['term_id']  // (Optional) The parent numeric term id
				)
			);
			update_option("familia_id_$familia_id", sanitize_text_field($term_data['term_id']));	
		}
		update_option("FONE_categoria_activate_term_id_".$t_id, $familia_web_activate);
		FONE_OcultaProductos($t_id, $familia_web_activate, "product_cat");
		$t_id_seo = $t_id;
	}else if ($familia_id!=0 && $familia!='' && $subfamilia!=''){
		$t_id = get_option("familia_id_$familia_id");
		if ($t_id!=''){
			//busca el indice subfamilia_id en options
			$t_id_subfamilia = get_option("subfamilia_id_$subfamilia_id");
			if ($t_id_subfamilia!=''){
				//si existe option_name verifica si todavia existe categoria creada
				if (!get_term_by( 'term_id', $t_id_subfamilia, 'product_cat')){
					//si no existe borra variable para que cree la familia
					$t_id_subfamilia='';
				}
			}else{
				//busca por string_name $familia (en casos antiguos versiones wp)
//				$term_id_subfamilia = term_exists( $subfamilia, 'product_cat' ); // array is returned if taxonomy is given
//				if ( $term_id_subfamilia !== 0 && $term_id_subfamilia !== null ) { //echo term_id_subfamilia['term_id']; 
					//si existe
//					update_option("subfamilia_id_$subfamilia_id", sanitize_text_field($term_id_subfamilia['term_id']));
//					$t_id_subfamilia = $term_id_subfamilia['term_id'];
//				}
			}	
			if($familia_slug!=''){$slug=$familia_slug;}else{$slug=$subfamilia.'-'.$familia;}
			if ( $t_id_subfamilia!='' ){
				$ret = wp_update_term($t_id_subfamilia, "product_cat", array('name' => $subfamilia, 'slug' => $slug, 'description'=> $familia_wpseo_desc, 'parent' => $t_id), $subfamilia);
				update_option("subfamilia_id_$subfamilia_id", $t_id_subfamilia);
			}else{
				// si existe $familia inserta subfamilia
				$cat_id = wp_insert_term(
					$subfamilia, // the term 
					'product_cat', // the taxonomy
					array(
						'description'=> $familia_wpseo_desc,
						'slug' => $slug,
						'parent'=> $t_id
					)
				);
				update_option("subfamilia_id_$subfamilia_id", $cat_id['term_id']);
			}		
			$t_id_seo = $t_id_subfamilia;
		}
	}
	if($t_id_seo>0 && (is_plugin_active( 'wordpress-seo/wp-seo.php' ) || is_plugin_active( 'wordpress-seo-premium/wp-seo-premium.php' ))){		global $wpseo_taxonomy;
		WPSEO_Taxonomy_Meta::set_values($t_id_seo, 'product_cat', array('wpseo_title' => $familia_wpseo_title, 'wpseo_focuskw' => $familia_wpseo_focuskw, 'wpseo_desc' => $familia_wpseo_desc));
	}
	
//foreach familias
		//crea subfamilia
//		$args = array(
	//		'orderby'       => 'name', 
//			'order'         => 'ASC',
//			'hide_empty'    => false, 
//			'child_of'      => $term, 
//			'parent'        =>0
//		); 
//		$subproducts = get_terms( 'product_cat', $args);
//		print_r($subproducts);
//		foreach ($subproducts as $subproduct) { 
	// 		echo $subproduct->name; 
	//	}		

//$term = get_queried_object()->term_id;
//$termid = get_terms($term, 'product_cat' );
//print_r($termid);
	FONE_UpdateDynamicMenu();
}

function FONE_UpdateDynamicMenu(){
	if (get_option('FacturaONE_dynamicmenu')!=''){
		//inicia array
		$menu_items = array();
		//carga marcas
		if ( defined( 'YITH_WCBR' ) ) {
			$terms = get_terms( YITH_WCBR::$brands_taxonomy, array('hide_empty' => true,) );
			if (count($terms)>0){
				$new_item = (object)array(
					'ID' => 9999111222333444,
					'db_id' => 9999111222333444,
					'menu_item_parent' => "",
					'post_type' => "nav_menu_item",
					'object_id' => 9999111222333444,
					'object' => "product_cat",
					'type' => "taxonomy",
					'type_label' => __("Product Category", "textdomain"),
					'title' => 'Marcas',
					'url' => '',
					'target' => '',
					'attr_title' => '',
					'classes' => 'fone-sub-menu-columns',
					'status' => '',
					'xfn' => '',
					'description' => ''
				);
				array_push($menu_items, $new_item);
				$cuentamarcas=0;
				foreach ($terms as $marca){
					$marca_activate = get_option("FONE_marca_activate_term_id_".$marca->term_id);
					if ($marca_activate!='' && $marca_activate==0){continue;}
					$cuentamarcas=$cuentamarcas+1;
					$new_item = (object)array(
						'ID' => intval($marca->term_id),
						'db_id' => intval($marca->term_id),
						'menu_item_parent' => 9999111222333444,
						'post_parent' =>'',
						'post_type' => "nav_menu_item",
						'object_id' => intval($marca->term_id),
						'object' => "product_cat",
						'type' => "taxonomy",
						'type_label' => __("Product Category", "textdomain"),
						'title' => $marca->name,
						'url' => get_term_link($marca),
						'target' => '',
						'attr_title' => '',
						'classes' => 'fone-sub-menu',
						'status' => '',
						'xfn' => '',
						'description' => ''
					);
					array_push($menu_items, $new_item);
				}
				if ($cuentamarcas==0){$menu_items = array();}
			}
		}

		// Get all the product categories
		$categories = get_categories(array(
			'taxonomy' => 'product_cat',
			'orderby' => 'name',
			'show_count' => 0,
			'pad_counts' => 0,
			'hierarchical' => 1,
			'hide_empty' => 1,
	//        'depth' => $depth,
			'title_li' => '',
			'echo' => 0
		));

		// Create menu items
		foreach($categories as $category) {
			$category_parent = $category->category_parent;
			if ($category_parent>0){}else{$category_parent=0;}
	//		if ($category->category_parent==0){
	//			$children = get_term_children($category->term_id, get_query_var('taxonomy')); 
	//			//print_r($children);
	//			if (sizeof($children)==0){continue;}
	//		}
			$categoria_activate = get_option("FONE_categoria_activate_term_id_".$category->term_id);
			if ($categoria_activate!='' && $categoria_activate==0){continue;}
			$categoria_activate = get_option("FONE_categoria_activate_term_id_".$category_parent);
			if ($categoria_activate!='' && $categoria_activate==0){continue;}
			if (strpos(strtolower($category->name), 'otros') !== false) {continue;}
			if (strpos(strtolower($category->name), 'sin categorizar') !== false) {continue;}

			//si ocultaproductos y categoria no tiene productos imagenes, salta categoria
			if (get_option('FONE_OcultaProductosNOIMAGEN')==1) {
				global $wpdb;
				$conimagen = $wpdb->get_col("
					SELECT COUNT(*) AS conimagen
					FROM $wpdb->posts p 
					LEFT JOIN $wpdb->term_relationships AS tr ON tr.object_id = p.ID
					LEFT JOIN $wpdb->postmeta pm ON pm.post_id = p.ID AND pm.meta_key = '_thumbnail_id'
					LEFT JOIN $wpdb->postmeta am ON	am.post_id = pm.meta_value AND am.meta_key = '_wp_attached_file'
					JOIN $wpdb->term_taxonomy AS tt ON tt.taxonomy = 'product_cat' AND tt.term_taxonomy_id = tr.term_taxonomy_id 
					JOIN $wpdb->terms AS t ON t.term_id = tt.term_id
					WHERE p.post_type in('product', 'product_variation') AND p.post_status = 'publish' AND am.meta_value <> '' AND t.term_id=".$category->term_id."
				" );
				if ($conimagen[0]==0){continue;}
			}

			$new_item = (object)array(
				'ID' => intval($category->term_id),
				'db_id' => intval($category->term_id),
				'menu_item_parent' => intval($category_parent),
				'post_parent' =>'',
				'post_type' => "nav_menu_item",
				'object_id' => intval($category->term_id),
				'object' => "product_cat",
				'type' => "taxonomy",
				'type_label' => __("Product Category", "textdomain"),
				'title' => $category->name,
				'url' => get_term_link($category),
	//            'classes' => array(),
				'target' => '',
				'attr_title' => '',
				'classes' => '',
				'status' => '',
				'xfn' => '',
				'description' => ''
			);
			array_push($menu_items, $new_item);
		}
		$menu_order = 0;
		// Set the order property
		foreach ($menu_items as $menu_item) {
			$menu_order++;
			$menu_item->menu_order = $menu_order;
		}
		unset($menu_item);
		update_option('FONE_menubardynamic', $menu_items);
	}
}
function FONE_OcultaProductos($term_id, $visibilidad, $taxonomy){
	//gestion desde erp f1
	return;
	if ($term_id>0){
		global $wpdb;
		$product_ids = $wpdb->get_results("
			SELECT ID FROM (		
				SELECT p.ID
				FROM $wpdb->posts p 
				LEFT JOIN $wpdb->term_relationships AS tr ON tr.object_id = p.ID
				JOIN $wpdb->term_taxonomy AS tt ON tt.taxonomy = '".$taxonomy."' AND tt.term_taxonomy_id = tr.term_taxonomy_id 
				JOIN $wpdb->terms AS t ON t.term_id = tt.term_id
				WHERE p.post_type in('product', 'product_variation') and t.term_id=".$term_id."
				GROUP BY p.ID
			) AS a; " );
		foreach ($product_ids as $product){
			$objProduct = wc_get_product( $product->ID );
			if ($visibilidad==1){$objProduct->set_catalog_visibility("visible");}else{$objProduct->set_catalog_visibility("hidden");}
			$product_id = $objProduct->save();
		}
//		$product_ids = $wpdb->get_results("
//			SELECT GROUP_CONCAT(ID SEPARATOR ',') as ID FROM (		
//				SELECT p.ID
//				FROM $wpdb->posts p 
//				LEFT JOIN $wpdb->term_relationships AS tr ON tr.object_id = p.ID
//				JOIN $wpdb->term_taxonomy AS tt ON tt.taxonomy = '".$taxonomy."' AND tt.term_taxonomy_id = tr.term_taxonomy_id 
//				JOIN $wpdb->terms AS t ON t.term_id = tt.term_id
//				WHERE p.post_type in('product', 'product_variation') and t.term_id=".$term_id."
//				GROUP BY p.ID
//			) AS a; " );
//		$idprods='';
//		foreach ($product_ids as $product){
//			$idprods = $product->ID;
//			break;
//		}
//		if ($idprods!=''){
//			if ($visibilidad==1){
//				$product_ids = $wpdb->get_results("	UPDATE $wpdb->posts SET post_status='publish' WHERE ID IN (".$idprods."); " );
//			}else{
//				$product_ids = $wpdb->get_results("	UPDATE $wpdb->posts SET post_status='draft' WHERE ID IN (".$idprods."); " );
//			}		
//		}
//		
//		$terms = array( 'exclude-from-search', 'exclude-from-catalog' ); // for hidden..
//		wp_set_post_terms( $prod_ID, $terms, 'product_visibility', false );
//				SELECT *
//				FROM PVWVb_posts p 
//				LEFT JOIN PVWVb_term_relationships AS tr ON tr.object_id = p.ID
//				JOIN PVWVb_term_taxonomy AS tt ON tt.taxonomy = 'product_visibility' AND tt.term_taxonomy_id = tr.term_taxonomy_id 
//				JOIN PVWVb_terms AS t ON t.term_id = tt.term_id
//				WHERE p.ID = 28879		
	}
}

//categoria productos campos personalizados
//https://www.webhat.in/article/woocommerce-tutorial/adding-custom-fields-to-woocommerce-product-category/

function FONE_product_ids_ocultos_para_usuario($current_user_id){
	global $wpdb;
	if ($current_user_id==0){
		$product_ids = $wpdb->get_col("SELECT post_id FROM $wpdb->postmeta WHERE post_id > 0 and meta_key = '_item_clientid' and meta_value<>0 ;" );
		return $product_ids;
	}else{
		$fone_client_id = get_user_meta($current_user_id, 'fone_client_id', true );
		if ($fone_client_id>0){
			$product_ids = $wpdb->get_col("SELECT post_id FROM $wpdb->postmeta WHERE post_id > 0 and meta_key = '_item_clientid' and meta_value<>0 and meta_value <> ".$fone_client_id." ;" );
			return $product_ids;
		}else{
			$product_ids = $wpdb->get_col("SELECT post_id FROM $wpdb->postmeta WHERE post_id > 0 and meta_key = '_item_clientid' and meta_value<>0 ;" );
			return $product_ids;
		}
	}
}
function FONE_is_site_admin(){
    return in_array('administrator',  wp_get_current_user()->roles);
}



add_action( 'woocommerce_order_status_changed', 'FONE_order_status_changed', 99, 3 );
function FONE_order_status_changed( $order_id, $old_status, $new_status ){
	//pending -> pendiente pago
	//processing -> procesando
	//on-hold -> en espera (transferencia bancaria)
	//completed -> completado
	//cancelled -> cancelado
	//refunded -> reembolsado
	//failed -> fallido

	//se genera cuando es contrareembolso o visa
	if( $new_status == "on-hold" || $new_status == "pending" || $new_status == "processing" ) {
		$order = new WC_Order( $order_id );  
        $order_data = $order->get_data();
        //pruebas
        //$json_order_data = json_encode( $order_data, JSON_PRETTY_PRINT );
        //mail('pruebas@gmail.com', 'Detalles del Pedido', $json_order_data);
        //die; 
					 
		$lineas_items=array();
		foreach ( $order->get_items() as $item ) {
			if ($item['variation_id']>0){
				$product = wc_get_product($item['variation_id']);
				$sku = $product->get_sku();
			}else{
				$product = wc_get_product($item['product_id']);
				$sku = $product->get_sku();
			}
			//print_r($product);die;
			//print_r( $item );die;
			$sub = number_format(($item['subtotal']/$item['qty']),4,".","");
			array_push($lineas_items, array('sku'=>$sku,'nam'=>$item['name'],'qty'=>$item['qty'],'sub'=>$sub));
		}
		//lineas de cuota
		$get_fees = $order->get_fees();
		if(count($get_fees)>0){
			foreach( $order->get_items('fee') as $itemfee ) {
				//mail('emailpruebas@gmail.com', 'pruebassssss', implode(", ",  $order->get_items('fee')   )); //die;
				//		
				// {"id":1221,"order_id":44342,"name":"Contra reembolso","tax_class":"0","tax_status":"taxable","amount":"5","total":"5","total_tax":"0","taxes":{"total":[]},"meta_data":[]}
				//
				// {"id":1269,"order_id":44354,"name":"Contra reembolso","tax_class":"","tax_status":"taxable","amount":"4.132231","total":"4.132231","total_tax":"0.87","taxes":{"total":{"8":"0.867769"}},"meta_data":[]}
				$totalcuota = ($itemfee['total'] + $itemfee['total_tax']) / 1.21;
				$sub = number_format(( $totalcuota ),4,".","");
				array_push($lineas_items, array('sku'=>0,'nam'=>$itemfee['name'],'qty'=>1,'sub'=>$sub));
			}
		}
		array_push($lineas_items, array('sku'=>0,'nam'=>'Transporte','qty'=>1,'sub'=>$order->get_shipping_total()));

		//carga información del usuario
		$user_name='';
		$user_email='';
		$client_vat='';
		$fone_client_id='';
		if($order->get_customer_id()>0){ //pedido con user_id
			$fone_client_id = get_the_author_meta( 'fone_client_id', $order->get_customer_id() );
			$user_info = get_userdata(get_current_user_id()); // datos del usuario logeado user_id
			if ($user_info) {
				$user_name = $user_info->display_name;
				$user_email = $user_info->user_email;
			}
			$client_vat = get_the_author_meta( 'vat_number', $order->get_customer_id() );
		}
		if ($user_name==''){
    		$user_name = $order->get_billing_first_name().' '.$order->get_billing_last_name();
			$user_email = $order->get_billing_email();
		}
		if ($client_vat==''){
			if ( method_exists($order, 'get_billing_vat') ) { $client_vat = $order->get_billing_vat(); }
		}
		if ($client_vat==''){$client_vat = get_post_meta( $order_id, '_vat_number', true );}
        $billing_company = $order_data['billing']['company'];
		//mail('email@pruebas.com', 'pruebas', $fone_client_id.'mensajepruebas');die;

		
		//$client_nota = $order->get_customer_note();
		//$client_nota = implode(", ", $order->get_items());
		$client_nota="";
		
		//plugin cost calculator builder
		if (function_exists('is_plugin_active')) {
			// Comprueba si el plugin está activo
			if (is_plugin_active('cost-calculator-builder/cost-calculator-builder.php')) {
				$datacalc = json_decode( implode(", ", $order->get_items()) , true);
				if ($datacalc && isset($datacalc['meta_data'][0]['value']['calc_data'])) {
					$calc_data = $datacalc['meta_data'][0]['value']['calc_data'];
					$result_string = "";
					foreach ($calc_data as $field) {
						$label = $field['label'];
						$value = $field['value'];
						$result_string .= "$label $value\n";
					}
					$client_nota.="\n\n".$result_string;
				}
			}
		}
		$data = array( 	'client_name'			=> $order->get_billing_first_name().' '.$order->get_billing_last_name(),
					  	'client_email' 			=> $order->get_billing_email(),
					  	'client_phone' 			=> $order->get_billing_phone(),
					  	'client_address_1' 		=> $order->get_billing_address_1(),
					  	'client_city' 			=> $order->get_billing_city(),
					  	'client_postcode' 		=> $order->get_billing_postcode(),
					  	'client_country' 		=> $order->get_billing_country(), //$order->get_billing_country(),
					    'shipping_name' 		=> $order->get_shipping_first_name().' '.$order->get_shipping_last_name(),
					    'shipping_address_1' 	=> $order->get_shipping_address_1(), //$order->get_billing_country(),
					    'shipping_city' 		=> $order->get_shipping_city(), //$order->get_billing_country(),
					    'shipping_postcode' 	=> $order->get_shipping_postcode(), //$order->get_billing_country(),
					    'shipping_country' 		=> $order->get_shipping_country(), //$order->get_billing_country(),
					  	'numero_pedido' 		=> $order_id,
                        'billing_company' 		=> $billing_company,
					  	'total' 				=> $order->get_total(),
					  	'status'				=> $order->get_status(),
					  	'payment_method_title'	=> $order->get_payment_method_title(),
					    'user_name'				=> $user_name,
					    'user_email'			=> $user_email,
					  	'client_nota'			=> base64_encode($client_nota),
						'client_id' 			=> $fone_client_id,
					  	'client_vat' 			=> $client_vat,
					  	'lineas_items'			=> $lineas_items
					  );
		
		if (is_multisite()){
			$original_site_id = get_current_blog_id();
			$FacturaONE_multisiteped = get_blog_option($original_site_id,'FacturaONE_multisiteped');
			switch_to_blog($FacturaONE_multisiteped);
			$site_url = get_site_url($original_site_id);
			$data['client_nota'] = base64_encode("\n\n".$site_url.$client_nota);
			$resultado = FONE_url_get_contents2('insertquote',json_encode($data));
			//$response = json_decode(gzuncompress(base64_decode($resultado)));
			$response = json_decode($resultado);
			restore_current_blog();
			if ($response->success==1 && $response->client_id>0){
				$user_id = email_exists($order->get_billing_email());
				if ($user_id > 0){
					if($response->item_tarifa>0){update_user_meta($user_id, 'tarifa_user', $response->item_tarifa);}
					if($response->client_id>0){update_user_meta($user_id, 'fone_client_id', $response->client_id);}
				}
			}
		}else{
			$resultado = FONE_url_get_contents2('insertquote',json_encode($data));
			//$response = json_decode(gzuncompress(base64_decode($resultado)));
			$response = json_decode($resultado);
			if ($response->success==1 && $response->client_id>0){
				$user_id = email_exists($order->get_billing_email());
				if ($user_id > 0){
					if($response->item_tarifa>0){update_user_meta($user_id, 'tarifa_user', $response->item_tarifa);}
					if($response->client_id>0){update_user_meta($user_id, 'fone_client_id', $response->client_id);}
				}
			}
		}
	}
}



function FONE_import2erp($idkey,$hash){
	// Get all the product categories
	$categories = get_categories(array(
		'taxonomy' => 'product_cat',
		'orderby' => 'name',
		'show_count' => 0,
		'pad_counts' => 0,
		'hierarchical' => 1,
		'hide_empty' => 1,
		'title_li' => '',
		'echo' => 0
	));
	$familias = array();
	foreach($categories as $category) {
		//'ID' => intval($category->term_id), //'menu_item_parent' => intval($category->category_parent),
		array_push($familias, $category->name);
	}
	$data = array('idkey'=>$idkey,'hash'=>$hash,'familias'=>json_encode($familias),'products'=>'');
	$resultado = FONE_url_get_contents2('import2erp', json_encode($data));

	// Get all products
	$get_products = wc_get_products( array( 'status' => 'publish', 'type' => array( 'simple','variation','composite' ), 'limit' => -1 ) );
	$products = array();
	$contador = 0;
	$tmpsku = 0;
	foreach ( $get_products as $product ){
		$contador = $contador+1;
		$image_id = $product->get_image_id();
		$product_id = $product->get_id(); //$product->ID
		$cat = get_the_terms( $product_id, 'product_cat' );
		if($cat){
			foreach ($cat as $categoria) {
				if($categoria->parent == 0){
					$familianame = $categoria->name;
					break;
				}
			}	
		}
		$tmpsku = $tmpsku + 1;
		$sku = $product->get_sku();
		if ($sku>0){ }else if($product_id>0){
			$objProduct = wc_get_product( $product_id );
			$objProduct->set_sku($tmpsku);
			$product_id_r = $objProduct->save(); 
			$sku = $tmpsku;
		}
		$current_product=array(	'sku' 					=> $sku, 
				  				'title' 				=> $product->get_title(),
				  				'description' 			=> base64_encode(sanitize_textarea_field($product->get_description())),
				  				'short_description' 	=> base64_encode(sanitize_textarea_field($product->get_short_description())),	
				  				'price' 				=> $product->get_regular_price(), //$product->get_price(),
				  				'slug'					=> $product->get_slug(),
				  				'image'					=> wp_get_attachment_image_url($image_id, 'full'),
							   	'familianame'			=> $familianame
				 		);
		array_push($products, $current_product);
		if ($contador>50){
			$data = array('idkey'=>$idkey,'hash'=>$hash,'familias'=>'','products'=>json_encode($products));
			$resultado = FONE_url_get_contents2('import2erp', json_encode($data));
			$products = array();
			$contador = 0;
		}
	}
	$data = array('idkey'=>$idkey,'hash'=>$hash,'familias'=>'','products'=>json_encode($products));
	$resultado = FONE_url_get_contents2('import2erp', json_encode($data));
}

function FONE_encrypt_decrypt($string, $action = 'encrypt')
{
    $encrypt_method = "AES-256-CBC";
    $secret_key = get_option('FacturaONE_APIKEY'); //'AA74CDCC2BBRT935136HH7B63C27'; // user define private key
    $secret_iv = '5fgf5HJ5g27'; // user define secret key
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16); // sha256 is hash_hmac_algo
    if ($action == 'encrypt') {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if ($action == 'decrypt') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}
	
function FONE_documentos($urluser,$item_lookup_id,$docfiles){
	$_product_id = wc_get_product_id_by_sku($item_lookup_id);
	if ( $_product_id > 0 ) {
		// Get an instance of the WC_Product Object
		$objProduct = wc_get_product( $_product_id );
		//lee documentos adjuntos y los mete en array
		$filesarray = array();
		foreach ($docfiles as $docfile)
			{
				$namefile = str_replace("U".$item_lookup_id."/", "", $docfile);
				$urlfile = $urluser.'/uploads/documentos/'.$docfile;
			
				if (strpos(strtoupper($urlfile), '.URL') !== false) {
					$urlfile = file_get_contents($urlfile);
					$namefile = str_replace(".url","",$namefile).'-'.basename($urlfile);
				}
			
				$urlfile = get_site_url().'/FONEfiles/?i='.FONE_encrypt_decrypt($urlfile, 'encrypt');
					
				array_push($filesarray, array('label'=>$namefile, 'file_location' => $urlfile));
			}
		$docadjuntos=array (0 => 
							  array (
								'name' => '',
								'default' => true,
								'documents' => $filesarray,
							  ),
							);
		$objProduct->update_meta_data('_wc_product_documents_title', 'Ficheros Adjuntos (Instrucciones, docs...)' );
		$objProduct->update_meta_data('_wc_product_documents_display', 'yes' );
		$objProduct->update_meta_data('_wc_product_documents', $docadjuntos );
		//graba
		$product_id = $objProduct->save(); // Save to database and sync 
	}
}
function FONE_clearmetadata($meta_type, $object_id, $meta_key, $meta_value, $delete_all)
{
//	$meta_type  = 'post';           // since we are deleting data for CPT 
//	$object_id  = 0;                // no need to put id of object since we are deleting all
//	$meta_key   = '_wc_product_documents';    // Your target meta_key added using update_post_meta()
//	$meta_value = '';               // No need to check for value since we are deleting all
//	$delete_all = true;             // This is important to have TRUE to delete all post meta

	// This will delete all post meta data having the specified key 
	delete_metadata( $meta_type, $object_id, $meta_key, $meta_value, $delete_all );
}

function FONE_subecategoria($name){
	$parent_term = term_exists( $name, 'product_cat' ); // array is returned if taxonomy is given
		$term_data = wp_insert_term(
			$name, // the term 
			'product_cat', // the Woocommerce product category taxonomy
			array( // (optional)
//				'description'=> 'This is a red apple1.', // (optional)
				'slug' => $name, // optional
//				'parent'=> $parent_term['term_id']  // (Optional) The parent numeric term id
			)
		);

	
	//	if ($parent_term['term_id']>0){
	//		wp_update_term( $parent_term, 'product_cat', array(
	//			'name' => $name,
	//			'slug' => $name
	//		));
	//	}else{
	//	}
}


function FONE_validardatos(){
	//set imagen
	//carga userdb para cachepage
	$resultado = FONE_url_get_contents('userdb');
	if($resultado!=''){
		$userdb = gzuncompress(base64_decode($resultado));
		update_option('FacturaONE_userdb',$userdb);
		//check conexion
		$resultado = FONE_url_get_contents('mod_wordpress_psalt',TRUE);
		$response = json_decode(gzuncompress(base64_decode($resultado)),true);
		if ($response['err']==0 && $response['mod_wordpress']>0){
			update_option('mod_wordpress', $response['mod_wordpress']);
			update_option('FacturaONE_catalogo_tarifa_cliente',$response['catalogo_tarifa_cliente']);
			update_option('FacturaONE_psalt',base64_decode(strtr($response['psalt'], '-_,', '+/=')));
			update_option('FacturaONE_tip',base64_decode(strtr($response['tip'], '-_,', '+/=')));
			echo("<div class='updated message' style='padding:10px;margin:16px 4px;margin-right: 19px;'>Verificación correcta. Opciones guardadas.</div>");
		}else{
			update_option('mod_wordpress','');
			echo("<div class='error message' style='padding:10px;margin:16px 4px;margin-right: 19px;'>Error identificación no válida. ".$resultado."</div>");
		}		
	}else{
		update_option('mod_wordpress','');
		echo("<div class='error message' style='padding:10px;margin:16px 4px;margin-right: 19px;'>Error identificación no válida.</div>");
	}
	//comprueba propagacion dns
	$dominio = $_SERVER['SERVER_NAME'];
	$ipdominio = trim(gethostbyname($dominio));
	$ipserver = trim($_SERVER['SERVER_ADDR']); //$ipserver = getHostByName(getHostName());
	if($ipdominio!='' && $ipserver!='' && $ipdominio!=$ipserver){
		echo("<div class='notice notice-warning' style='padding:10px;margin:16px 4px;margin-right: 19px;'>El dominio <b>".strtoupper($dominio)."</b> resuelve a la ip <b>".$ipdominio."</b> en lugar de la ip del servidor actual <b>".$ipserver."</b>.<br>Esto puede ser porque usa un CDN o bien porque las DNS no se han propagado correctamente.<br>Si el plugin funciona correctamente ignore este mensaje, sino tendra que esperar a que se propagen las DNS esto puede tardar entre 24/48 Horas</div>");
	}		
}
function FONE_borrar_producto($sku){
	$_product_id = wc_get_product_id_by_sku($sku);	
	FONE_delete_product_image_variaciones($_product_id);
	FONE_delete_product_images($_product_id, TRUE);
	FONE_wh_deleteProduct($_product_id, TRUE);
}
function FONE_borrarproductos(){
	//carga url db general y check conexion
	$resultado = FONE_url_get_contents('url');
	$response = gzuncompress(base64_decode($resultado));
	if ($response=='error' or $response==''){
		echo("<div class='error message' style='padding:10px;margin:16px 4px;margin-right: 19px;'>Error identificación no válida.</div>");
		return;
	}
	$resultado = FONE_url_get_contents('borrarproductos');
	$response = json_decode(gzuncompress(base64_decode($resultado)), true);
	//print_r($response);exit;

	$p = new FONE_ProgressBar();
	$p->FONE_render();
	foreach ($response as $posicionA=>$jugadorA)
	   {
		foreach ($jugadorA as $posicionB=>$jugadorB)
		   {
				if ($posicionB=='q1'){
					//echo 'ACTUALIZACION DE ARTICULOS<br />';
					$totalregistros = count($jugadorB); $i=0;
					foreach ($jugadorB as $posicionC=>$jugadorC)
					   {
							$_product_id = wc_get_product_id_by_sku($jugadorC['item_lookup_id']);
							if ($_product_id>0){
								FONE_delete_product_image_variaciones($_product_id);
								FONE_delete_product_images($_product_id, TRUE);
								FONE_wh_deleteProduct($_product_id, TRUE);
								//barraprogress
								$i=$i+1; $p->FONE_setProgressBarProgress($i*100/$totalregistros, 'ARTICULOS');usleep(10000);
							}
					   }
				}
		   }
	   }
	$p->FONE_hidebar();
	echo("<div class='updated message' style='padding:10px;margin:16px 4px;margin-right: 19px;'>Productos e imagenes eliminados con éxito.</div>");
	//exit;	
}
function FONE_delete_product_images( $post_id, $borragaleriaimag )
{
	$product = wc_get_product( $post_id );
    if ( !$product ) {
        return;
    }
	$featured_image_id = $product->get_image_id();
	$image_galleries_id = $product->get_gallery_image_ids();

	if( !empty( $featured_image_id ) ) {
		wp_delete_post( $featured_image_id );
	}

	if( $borragaleriaimag==TRUE && !empty( $image_galleries_id ) ) {
		foreach( $image_galleries_id as $single_image_id ) {
			wp_delete_post( $single_image_id );
		}
	}
}
function FONE_delete_product_image_variaciones( $post_id )
{
	$product = wc_get_product( $post_id );
    if ( !$product ) {
        return;
    }
	if( $product->is_type( 'variable' ) ){
		$variations = $product->get_available_variations();
		if( !empty( $variations ) ) {
			foreach( $variations as $variation ) {
				$image_id = $variation['image_id'];
				if ($image_id>0){
					wp_delete_post( $image_id );
				}
			}
		}
	}
}
function FONE_wh_deleteProduct($id, $force = FALSE)
{
    $product = wc_get_product($id);

    if(empty($product))
        return new WP_Error(999, sprintf(__('No %s is associated with #%d', 'woocommerce'), 'product', $id));
    // If we're forcing, then delete permanently.
    if ($force)
    {
        if ($product->is_type('variable'))
        {
            foreach ($product->get_children() as $child_id)
            {
                $child = wc_get_product($child_id);
                $child->delete(true);
            }
        }
        elseif ($product->is_type('grouped'))
        {
            foreach ($product->get_children() as $child_id)
            {
                $child = wc_get_product($child_id);
                $child->set_parent_id(0);
                $child->save();
            }
        }

        $product->delete(true);
        $result = $product->get_id() > 0 ? false : true;
    }
    else
    {
        $product->delete();
        $result = 'trash' === $product->get_status();
    }

    if (!$result)
    {
        return new WP_Error(999, sprintf(__('This %s cannot be deleted', 'woocommerce'), 'product'));
    }

    // Delete parent product transients.
    if ($parent_id = wp_get_post_parent_id($id))
    {
        wc_delete_product_transients($parent_id);
    }
    return true;
}

function FONE_get_version(){
	if( ! function_exists( 'get_plugin_data' ) ) { require_once( ABSPATH . 'wp-admin/includes/plugin.php' ); }
    $plugin_data = get_plugin_data( __FILE__ );
	$FacturaONE_version = $plugin_data['Version'];
	update_option('FacturaONE_version',$FacturaONE_version);
	if($FacturaONE_version>'2.86' && get_option('FacturaONE_updatedver')<'2.87'){
		update_option('FacturaONE_updatedver', $FacturaONE_version);
		global $wpdb;
		$wpdb->get_results('update '.$wpdb->prefix.'postmeta set meta_value = replace(meta_value,"files.php","FONEfiles") WHERE meta_key = "_wc_product_documents" AND meta_value LIKE "%files.php%" AND meta_value LIKE "%file_location%"; ');
	}
}

//plugins recomendados
if(is_admin()){
	$REQUEST_URI = strtoupper($_SERVER['REQUEST_URI']);
	if (strpos($REQUEST_URI, 'WPFACTURAONE') !== false || strpos($REQUEST_URI, 'TGMPA') !== false) {
		require_once plugin_dir_path( __FILE__ ) . 'class-tgm-plugin-activation.php';
		add_action( 'tgmpa_register', 'FONE_my_plugin_register_required_plugins' );
		function FONE_my_plugin_register_required_plugins() {
		  $plugins = array(
			array(
			  'name'    => 'WooCommerce',
			  'slug'    => 'woocommerce'
			),
			array(
			  'name'    => 'WooCommerce Menu Cart',
			  'slug'    => 'woocommerce-menu-bar-cart'
			),
			array(
			  'name'    => 'WooCommerce Product Documents',
			  'slug'    => 'woocommerce-product-documents'
			  //'version' => '1.11.2',
			),
			array(
			  'name'    => 'WooCommerce Variation Swatches',
			  'slug'    => 'woo-variation-swatches'
			),
			array(
			  'name'    => 'YITH WooCommerce Wishlist',
			  'slug'    => 'yith-woocommerce-wishlist'
			),
		  );

		  if ( !defined( 'YITH_WCBR' ) ) {
			array_push($plugins, array('name'=>'YITH WooCommerce Brands Add-On', 'slug'=>'yith-woocommerce-brands-add-on') );  
		  }
          if ( !defined( 'WC_MMQ' ) ) {
			array_push($plugins, array('name'=>'Min Max Quantity & Step Control for WooCommerce', 'slug'=>'woo-min-max-quantity-step-control-single') );
		  }
		  // http://tgmpluginactivation.com/configuration/#h-plugin-parameters
		  //https://getshortcodes.com/docs/integrating-the-core-plugin-with-plugins-and-themes/
		  $config = array( 'id' => 'my_plugin' );
		  tgmpa( $plugins, $config );
		}
	}
}
function FONE_clearcache(){
	//cache clear
	if ( function_exists( 'wp_cache_post_change' ) ) { wp_cache_clear_cache(); }
	if ( function_exists( 'w3tc_pgcache_flush') ) { w3tc_pgcache_flush(); }
	if ( function_exists( 'wpfc_clear_all_cache') ) { wpfc_clear_all_cache(true); }
	if ( function_exists( 'rocket_clean_domain' ) ) { rocket_clean_domain(); }
	if ( function_exists( 'wp_cache_flush' ) ) { wp_cache_flush(); }
	//update_option('_aei_clearcache'.time(),1);
}


//view files
if (strpos($_SERVER['REQUEST_URI'], 'FONEfiles') !== false) {
	if (isset($_GET['i'])){$url = FONE_encrypt_decrypt($_GET['i'], 'decrypt');} else {exit;};
	if ($url!=''){
		if (strpos(strtoupper($url), 'LOGIN.IFACTURA.ES') === false && strpos(strtoupper($url), 'HTTP') !== false) {
			header("Location: ".$url);
			die;
		}else if (strpos(strtoupper($url), 'LOGIN.IFACTURA.ES') !== false) {
			$upload_dir = wp_upload_dir();
			$basedir = $upload_dir['basedir'];
			$baseurl = $upload_dir['baseurl'];
			if (!file_exists($basedir.'/files')) {mkdir($basedir.'/files', 0777, true);}
			$file_name = basename($url);
			if(file_put_contents( $basedir.'/files/'.$file_name,file_get_contents($url))) {
				$url = $baseurl."/files/".$file_name;
				header("Location: ".$url);
				die;
			}
		}
	}
}

//url execución curl
if (strpos($_SERVER['REQUEST_URI'], 'FONE_curlexec') !== false) {
	if(isset($_POST['phpfile'])) {$phpfile = $_POST['phpfile'];} else {$phpfile='';}
	if(isset($_POST['action'])) {$action = $_POST['action'];} else {$action='';}
	if(isset($_POST['erp'])) {$erp = $_POST['erp'];} else {$erp='';}
	if(isset($_POST['apikey'])) {$apikey = $_POST['apikey'];} else {$apikey='';}
	if(isset($_POST['psalt'])) {$psalt = $_POST['psalt'];} else {$psalt='';}
	if(isset($_POST['tip'])) {$tip = $_POST['tip'];} else {$tip='';}
	if ($apikey!=get_option('FacturaONE_APIKEY')){
		echo json_encode(array('success'=>0,'status'=>'error'));
		exit;
	}else{
		update_option('FacturaONE_psalt',base64_decode(strtr($psalt, '-_,', '+/=')));
		update_option('FacturaONE_tip',base64_decode(strtr($tip, '-_,', '+/=')));
	}
	if (FONE_url_get_contents2('rest2', json_encode($_SERVER))!=1){
		echo json_encode(array('success'=>0,'status'=>'error'));
		exit;
	}
    if ($action=='statimport'){
        //progressbar
        $status_realizado = get_option('_facturaone_status_realizado');
        $status_realizado = trim(substr($status_realizado, 1)); 
        $status_backgroundid = get_option('_facturaone_status_backgroundid');
        $status_value = get_option('_facturaone_status_value');
        $status_title = get_option('_facturaone_status_title');
        $count_status_value = count(explode(';', $status_value));
        $count_status_realizado = count(explode(';', $status_realizado));
        if ($count_status_value>0){$porcentaje=($count_status_realizado/$count_status_value)*100;}
        $response = array('success'=>1, 'string' => $status_realizado, 'status_backgroundid' => $status_backgroundid, 'porcentaje' => $porcentaje, 'status_title' => $status_title);
        echo json_encode($response);
        exit;        
	}
	if ($action=='productsku'){
		$sku = $_POST['sku'];
		$product_id = wc_get_product_id_by_sku($sku);
		if ($product_id){
			echo json_encode(array('success'=>1,'link'=>get_permalink($product_id)));
		}else{
			echo json_encode(array('success'=>0));
		}
		exit;
	}
	if ($action=='clearcache'){
		FONE_clearcache();
		exit;
	}	
	
	//si esta iniciando una accion start comprueba si tiene proceso y no lo detiene, relanza ajax
	if (($action=='start' || $action=='startall') && get_option('_facturaone_status_backgroundid')!=0){
		$status_backgroundid = time().rand(99999,9999999999);
		$FacturaONE_backgroundtask = get_option('_FacturaONE_backgroundtask');
		FONE_execajax($FacturaONE_backgroundtask,$status_backgroundid);
		$response = array('success'=>1,'status'=>'reejecutado');
		echo json_encode($response);
		exit; 
	}
	if ($phpfile!=''){
		if ($erp!=1){
			if (file_exists(dirname(__FILE__).'/ajax.json')){unlink(dirname(__FILE__).'/ajax.json');}
			update_option('_facturaone_status_backgroundid','0'); 
			update_option('_facturaone_status_realizado',''); 
			update_option('_facturaone_status_title',''); 
		}
		//carga variables en array
		$vars = array('action' => $action);
		if (isset($_POST['item_lookup_id'])){$vars = array('action' => $action, 'item_lookup_id' => $_POST['item_lookup_id']);}
		if (isset($_POST['data'])){$vars = array('action' => $action, 'data' => $_POST['data']);}
		if (isset($_POST['client_id'])){$vars = array('action' => $action, 'client_id' => $_POST['client_id']);}
		if (isset($_POST['marca_id'])){$vars = array('action' => $action, 'marca_id' => $_POST['marca_id']);}
		if (isset($_POST['familia_id'])){$vars = array('action' => $action, 'familia_id' => $_POST['familia_id']);}
		if (isset($_POST['subfamilia_id'])){$vars = array('action' => $action, 'subfamilia_id' => $_POST['subfamilia_id']);}
		if (isset($_POST['facturaurl'])){$vars = array('action' => $action, 'facturaurl' => $_POST['facturaurl']);}
		if (isset($_POST['invoice_wp_order'])){$vars = array('action' => $action, 'invoice_wp_order' => $_POST['invoice_wp_order']);}
		if (isset($_POST['facturaurl']) && isset($_POST['invoice_wp_order'])){$vars = array('action' => $action, 'facturaurl' => $_POST['facturaurl'], 'invoice_wp_order' => $_POST['invoice_wp_order']);}
		if (isset($_POST['idkey']) && isset($_POST['hash'])){
			if (isset($_POST['imagenes'])){
				$vars = array('action' => $action, 'idkey' => $_POST['idkey'], 'hash' => $_POST['hash'], 'imagenes' => $_POST['imagenes']);
			}else{
				$vars = array('action' => $action, 'idkey' => $_POST['idkey'], 'hash' => $_POST['hash']);
			}
		}
		$url = plugins_url('wp-facturaone').'/'.$phpfile;
		//update_option('aras',$url);
		if (get_option('FacturaONE_conexionSSL')=='1'){
			$wpremotepost = array(
				'method' => 'POST',
				'timeout' => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array(),
				'body' => $vars,
				'cookies' => array()
			   );
		}else{
			$wpremotepost = array(
				'method' => 'POST',
				'timeout' => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array(),
				'sslverify' => false,
				'body' => $vars,
				'cookies' => array()
			   );
		}
		$response = wp_safe_remote_post($url, $wpremotepost);
//		$vars = '';
//		if (isset($_POST['item_lookup_id'])){$vars = $vars.'&item_lookup_id='.$_POST['item_lookup_id'];}
//		if (isset($_POST['data'])){$vars = $vars.'&data='.$_POST['data'];}
//		if (isset($_POST['client_id'])){$vars = $vars.'&client_id='.$_POST['client_id'];}
//		if (isset($_POST['marca_id'])){$vars = $vars.'&marca_id='.$_POST['marca_id'];}
//		if (isset($_POST['familia_id'])){$vars = $vars.'&familia_id='.$_POST['familia_id'];}
//		if (isset($_POST['subfamilia_id'])){$vars = $vars.'&subfamilia_id='.$_POST['subfamilia_id'];}
//		if (isset($_POST['facturaurl'])){$vars = $vars.'&facturaurl='.$_POST['facturaurl'];}
//		if (isset($_POST['invoice_wp_order'])){$vars = $vars.'&invoice_wp_order='.$_POST['invoice_wp_order'];}
//		if(function_exists('exec') && 1==2){
//			include_once('PHPBackgroundProcesser.php');
//			$proc = new BackgroundProcess();
//			$proc->execBackProcess('curl -s '.$url.' -d "action='.$action.$vars.'"'); 
//		}else{
//			$ch = curl_init();
//			curl_setopt($ch, CURLOPT_URL, $url);
//			curl_setopt($ch, CURLOPT_HEADER, 0);
//			curl_setopt($ch, CURLOPT_POST, TRUE);
//			curl_setopt($ch, CURLOPT_POSTFIELDS, 'action='.$action.$vars);
//			curl_setopt($ch, CURLOPT_RETURNTRANSER,0);
//			$content=curl_exec($ch);
//			curl_close($ch);	
//		}
	}
	if ($action=='start' || $action=='startall'){
		$response = array('success'=>1,'status'=>'ejecutado');
		echo json_encode($response);
		exit; 
	}else{
		
		$response = array('success'=>1,'email'=>get_option('FacturaONE_EMAIL'));
		echo json_encode($response);
		exit;
	}
}
function FONE_execajax($backgroundtask,$backgroundid)
{
	update_option('_FacturaONE_backgroundtask',$backgroundtask);
	//para primeras llamadas desde update_familia o para rellamadas desde ajax.php
	$url = plugins_url('wp-facturaone').'/ajax.php'.'?backgroundtask='.$backgroundtask.'&backgroundid='.$backgroundid.'&apikey='.get_option('FacturaONE_APIKEY');
	if (get_option('FacturaONE_conexionSSL')=='1'){
		$wpremotepost = array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'body' => array('backgroundid' => $backgroundid,'apikey' => get_option('FacturaONE_APIKEY')),
			'cookies' => array()
			);
	}else{
		$wpremotepost = array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'sslverify' => false,
			'body' => array('backgroundid' => $backgroundid,'apikey' => get_option('FacturaONE_APIKEY')),
			'cookies' => array()
			);
	}	
	$response = wp_safe_remote_post($url, $wpremotepost);		
//	if(function_exists('exec') && 1==2){
//		include_once('PHPBackgroundProcesser.php');
//		$proc = new BackgroundProcess();
//		$proc->execBackProcess('curl -s '.$url.' -d "backgroundid='.$backgroundid.'&apikey='.get_option('FacturaONE_APIKEY').'"'); 
//	}else{
//		$ch = curl_init();
//		curl_setopt($ch, CURLOPT_URL, $url);
//		curl_setopt($ch, CURLOPT_HEADER, 0);
//		curl_setopt($ch, CURLOPT_POST, TRUE);
//		curl_setopt($ch, CURLOPT_POSTFIELDS, 'backgroundid='.$backgroundid.'&apikey='.get_option('FacturaONE_APIKEY'));
//		curl_setopt($ch, CURLOPT_RETURNTRANSER,0);
//		$content=curl_exec($ch);
//		curl_close($ch);
//	}
}


function FONE_validDniCifNie($dni){
  $dni = str_replace(' ', '', $dni);
  $dni = str_replace('-', '', $dni);
  $dni = str_replace('.', '', $dni);	
  $cif = strtoupper($dni);
  for ($i = 0; $i < 9; $i ++){
    $num[$i] = substr($cif, $i, 1);
  }
  // Si no tiene un formato valido devuelve error
  if (!preg_match('/((^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$|^[T]{1}[A-Z0-9]{8}$)|^[0-9]{8}[A-Z]{1}$)/', $cif)){
    return false;
  }
  // Comprobacion de NIFs estandar
  if (preg_match('/(^[0-9]{8}[A-Z]{1}$)/', $cif)){
    if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr($cif, 0, 8) % 23, 1)){
      return true;
    }else{
      return false;
    }
  }
  // Algoritmo para comprobacion de codigos tipo CIF
  $suma = $num[2] + $num[4] + $num[6];
  for ($i = 1; $i < 8; $i += 2){
    $suma += (int)substr((2 * $num[$i]),0,1) + (int)substr((2 * $num[$i]), 1, 1);
  }
  $n = 10 - substr($suma, strlen($suma) - 1, 1);
  // Comprobacion de NIFs especiales (se calculan como CIFs o como NIFs)
  if (preg_match('/^[KLM]{1}/', $cif)){
    if ($num[8] == chr(64 + $n) || $num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr($cif, 1, 8) % 23, 1)){
      return true;
    }else{
      return false;
    }
  }
  // Comprobacion de CIFs
  if (preg_match('/^[ABCDEFGHJNPQRSUVW]{1}/', $cif)){
    if ($num[8] == chr(64 + $n) || $num[8] == substr($n, strlen($n) - 1, 1)){
      return true;
    }else{
      return false;
    }
  }
  // Comprobacion de NIEs
  // T
  if (preg_match('/^[T]{1}/', $cif)){
    if ($num[8] == preg_match('/^[T]{1}[A-Z0-9]{8}$/', $cif)){
      return true;
    }else{
      return false;
    }
  }
  // XYZ
  if (preg_match('/^[XYZ]{1}/', $cif)){
    if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr(str_replace(array('X','Y','Z'), array('0','1','2'), $cif), 0, 8) % 23, 1)){
      return true;
    }else{
      return false;
    }
  }
  // Si todavía no se ha verificado devuelve error
  return false;
}	
?>