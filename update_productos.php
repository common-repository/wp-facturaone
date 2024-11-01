<?php
//https://www.facturaone.com/tienda/wp-content/plugins/wp-facturaone/update_productos.php
//ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');
//error_reporting(E_ALL);
$pagePath = explode('/wp-content/', dirname(__FILE__));
include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
//FONE_wooco_save_option_field(1);
//return;

if (isset($_POST['action']) && $_POST['action']=='start'){
	$imagenes = (isset($_POST['imagenes'])) ? $_POST['imagenes'] : 0;

	//genera proceso sobre todas las db
	$pagePath = explode('/wp-content/', dirname(__FILE__));
	include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
	//carga url db general y check conexion
	$resultado = FONE_url_get_contents('url');
	$response = gzuncompress(base64_decode($resultado));
	if ($response!='error' && $response!=''){
		$baseurlfact = $response;
		//carga lista productos
		if (get_option('FacturaONE_ivaincluido')==0){
			$resultado = FONE_url_get_contents('datafactsiniva');
		}else{
			$resultado = FONE_url_get_contents('datafact');
		}
		
		$response = json_decode(gzuncompress(base64_decode($resultado)), true);
		
		$idlinarray=0;
		$userids='';
		$statusarray=array();
		
		foreach ($response as $posicionA=>$jugadorA)
		   {
			foreach ($jugadorA as $posicionB=>$jugadorB)
			   {
					if ($posicionB=='q1'){
						foreach ($jugadorB as $posicionC=>$jugadorC)
						   {
								$catalogo_tarifa_cliente = $jugadorC['catalogo_tarifa_cliente'];
								update_option('FacturaONE_catalogo_tarifa_cliente', $catalogo_tarifa_cliente);
						   }
					}
					if ($posicionB=='q2'){
						foreach ($jugadorB as $posicionC=>$jugadorC)
						   {
								array_push($statusarray, array($idlinarray,array("q2",$jugadorC['familia_name'],$jugadorC['subfamilia_name'],$jugadorC['familia_id'],$jugadorC['subfamilia_id'],$jugadorC['familia_web_activate'],$jugadorC['familia_wpseo_title'],$jugadorC['familia_wpseo_focuskw'],$jugadorC['familia_wpseo_desc'],$jugadorC['familia_slug'])));
								$userids = $userids.'; '.$idlinarray;
								$idlinarray=$idlinarray+1;							
						   }
					}
					if ($posicionB=='q3'){
						//if ( ! defined( 'ABSPATH' ) ) {
						//	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
						//}
						//require_once(ABSPATH.'wp-admin/includes/image.php');
						//require_once(ABSPATH.'wp-admin/includes/file.php');
						//require_once(ABSPATH.'wp-admin/includes/media.php');

						//wp_suspend_cache_addition(true);
						foreach ($jugadorB as $posicionC=>$jugadorC)
						   {
								// echo $jugadorC['item_lookup_id'].' '.$jugadorC['item_name']; echo '<br>';
								//selecciona tarifa web desde opcion catalogo / tarifa de precios web
								if ($catalogo_tarifa_cliente==1){
									$item_price = $jugadorC['item_price'];
								} else if ($catalogo_tarifa_cliente==2){
									$item_price = $jugadorC['item_tarifa2'];
								} else if ($catalogo_tarifa_cliente==3){
									$item_price = $jugadorC['item_tarifa3'];
								} else if ($catalogo_tarifa_cliente==4){
									$item_price = $jugadorC['item_tarifa4'];
								} else if ($catalogo_tarifa_cliente==5){
									$item_price = $jugadorC['item_tarifa5'];
								} else {
									$item_price = 0;
								}
								array_push($statusarray, array($idlinarray,array("q3",$baseurlfact,$jugadorC['item_lookup_id'],$jugadorC['item_name'],$jugadorC['item_description'],$jugadorC['item_description_web'],$jugadorC['urlimage'],$item_price,$jugadorC['item_price'],$jugadorC['item_tarifa2'],$jugadorC['item_tarifa3'],$jugadorC['item_tarifa4'],$jugadorC['item_tarifa5'],$jugadorC['item_kgs_neto'],$jugadorC['item_kgs_bruto'],$jugadorC['familia_id'],$jugadorC['subfamilia_id'],$jugadorC['item_activado'],$jugadorC['tax_rate_id'],$jugadorC['variaciones'],$jugadorC['compuestos'],$jugadorC['item_txt_alternativo'],$jugadorC['marca'],$jugadorC['ean'],$jugadorC['item_clientid'],$jugadorC['item_onorder'],$jugadorC['item_wp_stock_control'],$jugadorC['item_wp_stock_visible'],$jugadorC['item_seo_fraseobjetivo'],$jugadorC['item_seo_metadescripcion'],$jugadorC['item_wp_visibilidad'],$jugadorC['item_wp_destacado'],$jugadorC['item_slug'],$jugadorC['item_description_web_corta'],$jugadorC['item_wp_backorders'],$jugadorC['item_categorias'],$jugadorC['item_sku'],$jugadorC['familia_web_activate'],$jugadorC['marca_web_activate'],$jugadorC['largo'] ,$jugadorC['ancho'],$jugadorC['alto'], $jugadorC['item_minquantity'], $jugadorC['item_maxquantity'] ,$jugadorC['item_stepquantity'] )));
							
								$userids = $userids.'; '.$idlinarray;
								$idlinarray=$idlinarray+1;	
						   }
					}
					if ($posicionB=='q4'){
						foreach ($jugadorB as $posicionC=>$jugadorC)
						   {
								array_push($statusarray, array($idlinarray,array("q4",$jugadorC['user_email'],$jugadorC['user_password'],$jugadorC['user_name'],$jugadorC['user_tarifa'], $jugadorC['client_name'], $jugadorC['client_address_1'], $jugadorC['client_city'], $jugadorC['client_zip'] ,$jugadorC['phone'], $jugadorC['client_id'], $jugadorC['client_country'], $jugadorC['client_nif'])));
								$userids = $userids.'; '.$idlinarray;
								$idlinarray=$idlinarray+1;	
						   }
					}			
					if ($posicionB=='q5'){
						foreach ($jugadorB as $posicionC=>$jugadorC)
						   {
								array_push($statusarray, array($idlinarray,array("q5",$jugadorC['id'],$jugadorC['stock'],$jugadorC['item_wp_stock_control'],$jugadorC['item_wp_stock_visible'],$jugadorC['item_onorder'],$jugadorC['item_wp_backorders'])));
								$userids = $userids.'; '.$idlinarray;
								$idlinarray=$idlinarray+1;	
						   }
					}
					if ($posicionB=='q6'){
						foreach ($jugadorB as $posicionC=>$jugadorC)
						   {
								array_push($statusarray, array($idlinarray, array("q6", $jugadorC['marca_name'], $jugadorC['marca_image'], $baseurlfact, $jugadorC['marca_web_activate'])));
								$userids = $userids.'; '.$idlinarray;
								$idlinarray=$idlinarray+1;							
						   }
					}	
					if ($posicionB=='q7'){
						foreach ($jugadorB as $posicionC=>$jugadorC)
						   {
								array_push($statusarray, array($idlinarray, array("q7", $jugadorC['item_lookup_id'], $jugadorC['item_name'], $jugadorC['item_txt_alternativo'], $baseurlfact, $jugadorC['urlimage'])));
								$userids = $userids.'; '.$idlinarray;
								$idlinarray=$idlinarray+1;
						   }
					}	
					if ($posicionB=='q8' && $imagenes==1){
						foreach ($jugadorB as $posicionC=>$jugadorC)
						   {
								array_push($statusarray, array($idlinarray, array("q8", $jugadorC['item_lookup_id'], $jugadorC['item_name'], $jugadorC['item_txt_alternativo'], $baseurlfact, $jugadorC['urlimage'])));
								$userids = $userids.'; '.$idlinarray;
								$idlinarray=$idlinarray+1;
						   }
					}					
				}
		   }	

		$status_backgroundid = time().rand(99999,9999999999);
		update_option('_facturaone_status_title','...');
		update_option('_facturaone_status_value',$userids);
		update_option('_facturaone_status_realizado','');
		update_option('_facturaone_status_backgroundid',$status_backgroundid);

		//update_option('_facturaone_status_array',json_encode($statusarray));
		if (file_exists('ajax.json')){unlink('ajax.json');}
		file_put_contents('ajax.json', json_encode($statusarray));
		//$fp = fopen('ajax.json', 'w');
		//fwrite($fp, json_encode($statusarray));
		//fclose($fp);

//		$wpdb->show_errors();
//		update_option('_facturaone_status_array',json_encode($statusarray));
//		if ($wpdb->last_error!=''){
//			update_option('_facturaone_err',$wpdb->last_error);
//			exit;
//		}
		
		//en el caso que pasa de 50 vuelve a ejecutar bucle desde inicio
		FONE_execajax('update_productos',$status_backgroundid);
	}
}
function q2($arrayvariables){
	$familia_name=trim($arrayvariables[1]);
	$subfamilia_name=trim($arrayvariables[2]);
	$familia_id=trim($arrayvariables[3]);
	$subfamilia_id=trim($arrayvariables[4]);
	$familia_web_activate=trim($arrayvariables[5]);
	$familia_wpseo_title=trim($arrayvariables[6]);
	$familia_wpseo_focuskw=trim($arrayvariables[7]);
	$familia_wpseo_desc=trim($arrayvariables[8]);
	$familia_slug=trim($arrayvariables[9]);
	
	if ($familia_name!=''){
		update_option('_facturaone_status_title','Actualizando Familias/Subfamilias... <br>'.$familia_name.' '.$subfamilia_name);
		$pagePath = explode('/wp-content/', dirname(__FILE__));
		include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
		FONE_familias($familia_name, $subfamilia_name, $familia_id, $subfamilia_id, $familia_web_activate, $familia_wpseo_title, $familia_wpseo_focuskw, $familia_wpseo_desc, $familia_slug);
	}
}
function q3($arrayvariables){
	$baseurlfact=trim($arrayvariables[1]);
	$item_lookup_id=trim($arrayvariables[2]);
	$item_name=trim($arrayvariables[3]);
	$item_description=trim($arrayvariables[4]);
	$item_description_web=trim($arrayvariables[5]);
	$urlimage=trim($arrayvariables[6]);
	$itemprice=trim($arrayvariables[7]);
	$item_price=trim($arrayvariables[8]);
	$item_tarifa2=trim($arrayvariables[9]);
	$item_tarifa3=trim($arrayvariables[10]);
	$item_tarifa4=trim($arrayvariables[11]);
	$item_tarifa5=trim($arrayvariables[12]);
	$item_kgs_neto=trim($arrayvariables[13]);
	$item_kgs_bruto=trim($arrayvariables[14]);
	$familia_id=trim($arrayvariables[15]);
	$subfamilia_id=trim($arrayvariables[16]);
	$item_activado=trim($arrayvariables[17]);
	$tax_rate_id=trim($arrayvariables[18]);
	$variaciones=trim($arrayvariables[19]);
	$compuestos=trim($arrayvariables[20]);
	$item_txt_alternativo=trim($arrayvariables[21]);
	$marca=trim($arrayvariables[22]);
	$ean=trim($arrayvariables[23]);
	$item_clientid=trim($arrayvariables[24]);
	$item_onorder=trim($arrayvariables[25]);
	$item_wp_stock_control=trim($arrayvariables[26]);
	$item_wp_stock_visible=trim($arrayvariables[27]);
	$item_seo_fraseobjetivo=trim($arrayvariables[28]);
	$item_seo_metadescripcion=trim($arrayvariables[29]);
	$item_wp_visibilidad=trim($arrayvariables[30]);
	$item_wp_destacado=trim($arrayvariables[31]);
	$item_slug=trim($arrayvariables[32]);
	$item_description_web_corta=trim($arrayvariables[33]);
	$item_wp_backorders=trim($arrayvariables[34]);
	$item_categorias=trim($arrayvariables[35]);
	$item_sku=trim($arrayvariables[36]);
	$familia_web_activate=trim($arrayvariables[37]);
	$marca_web_activate=trim($arrayvariables[38]);
	$largo=trim($arrayvariables[39]);
	$ancho=trim($arrayvariables[40]);
	$alto=trim($arrayvariables[41]);
    $item_minquantity=trim($arrayvariables[42]);
    $item_maxquantity=trim($arrayvariables[43]);
    $item_stepquantity=trim($arrayvariables[44]);
	if ($item_lookup_id!=''){
		update_option('_facturaone_status_title','Actualizando Productos y Precios...<br>'.$item_name);
		$pagePath = explode('/wp-content/', dirname(__FILE__));
		include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
		FONE_subeproducto($baseurlfact, false, $item_lookup_id, $item_name, $item_description, $item_description_web, $urlimage, $itemprice, $item_price, $item_tarifa2, $item_tarifa3, $item_tarifa4, $item_tarifa5, $item_kgs_neto, $item_kgs_bruto, $familia_id, $subfamilia_id, $item_activado, $tax_rate_id, $variaciones, $compuestos, $item_txt_alternativo, $marca, $ean, $item_clientid, $item_onorder, $item_wp_stock_control, $item_wp_stock_visible, $item_seo_fraseobjetivo, $item_seo_metadescripcion, $item_wp_visibilidad, $item_wp_destacado, $item_slug, $item_description_web_corta, $item_wp_backorders, $item_categorias, $item_sku, $familia_web_activate, $marca_web_activate, $largo, $ancho, $alto, $item_minquantity, $item_maxquantity, $item_stepquantity);
	}	
}
function q4($arrayvariables){
	$user_email=trim($arrayvariables[1]);
	$user_password=trim($arrayvariables[2]);
	$user_name=trim($arrayvariables[3]);
	$user_tarifa=trim($arrayvariables[4]);
	$client_name=trim($arrayvariables[5]);
	$client_address_1=trim($arrayvariables[6]);
	$client_city=trim($arrayvariables[7]);
	$client_zip=trim($arrayvariables[8]);
	$phone=trim($arrayvariables[9]);
	$fone_client_id=trim($arrayvariables[10]);
	$client_country=trim($arrayvariables[11]);
	$client_nif=trim($arrayvariables[12]);

	if ($user_email!=''){
		update_option('_facturaone_status_title','Actualizando Clientes ... <br>'.$client_name);
		$pagePath = explode('/wp-content/', dirname(__FILE__));
		include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
		FONE_clientes($user_email, $user_password, $user_name, $user_tarifa, $client_name, $client_address_1, $client_city, $client_zip , $phone,$fone_client_id,$client_country,$client_nif,0);
		FONE_cliente_tarifas_especiales($fone_client_id);
	}	
}
function q5($arrayvariables){
	$id=trim($arrayvariables[1]);
	$stock=trim($arrayvariables[2]);
	$item_wp_stock_control=trim($arrayvariables[3]);
	$item_wp_stock_visible=trim($arrayvariables[4]);
	$item_onorder=trim($arrayvariables[5]);
	$item_wp_backorders=trim($arrayvariables[6]);
	
	if ($id!=''){
		update_option('_facturaone_status_title','Actualizando Stock ...');
		$pagePath = explode('/wp-content/', dirname(__FILE__));
		include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
		FONE_actualizastockproducto($id, $stock, $item_wp_stock_control, $item_wp_stock_visible, $item_onorder, $item_wp_backorders);
	}
}
function q6($arrayvariables){
	$marca_name=trim($arrayvariables[1]);
	$marca_image=trim($arrayvariables[2]);
	$baseurlfact=trim($arrayvariables[3]);
	$marca_web_activate=trim($arrayvariables[4]);
	
	if ($marca_name!=''){
		update_option('_facturaone_status_title','Actualizando Marcas ...<br>'.$marca_name);
		$pagePath = explode('/wp-content/', dirname(__FILE__));
		include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
		$base_url = $baseurlfact.'/uploads/documentos/marcas/'.$marca_image;
		FONE_creamarca($marca_name,$base_url, NULL, $marca_web_activate);
	}
}
function q7($arrayvariables){
	$item_lookup_id=trim($arrayvariables[1]);
	$item_name=trim($arrayvariables[2]);
	$item_txt_alternativo=trim($arrayvariables[3]);
	$baseurlfact=trim($arrayvariables[4]);
	$urlimage=trim($arrayvariables[5]);
	
	if ($item_lookup_id!='' && $urlimage!=''){
		update_option('_facturaone_status_title','Actualizando Variaciones Imagenes ...<br>'.$item_name);
		$pagePath = explode('/wp-content/', dirname(__FILE__));
		include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
		FONE_subeimagen_variacion($baseurlfact, $item_lookup_id, $urlimage, $item_name, $item_txt_alternativo, false);
	}
}
function q8($arrayvariables){
	$item_lookup_id=trim($arrayvariables[1]);
	$item_name=trim($arrayvariables[2]);
	$item_txt_alternativo=trim($arrayvariables[3]);
	$baseurlfact=trim($arrayvariables[4]);
	$urlimage=trim($arrayvariables[5]);

	if ($item_lookup_id!='' && $urlimage!=''){
		update_option('_facturaone_status_title','Actualizando Imagenes ...<br>'.$item_name);
		$pagePath = explode('/wp-content/', dirname(__FILE__));
		include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
		FONE_subeimagen($baseurlfact, $item_lookup_id, $urlimage, $item_name, $item_txt_alternativo, false, false);
	}
}

//actualiza producto desde f1
if (isset($_POST['action']) && $_POST['action']=='update' && $_POST['item_lookup_id']!=''){
//if (1==1){
	$item_lookup_id = $_POST['item_lookup_id'];
	//$item_lookup_id = 333;
	
	//genera proceso sobre todas las db
	$pagePath = explode('/wp-content/', dirname(__FILE__));
	include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
	//carga url db general y check conexion
	$resultado = FONE_url_get_contents('url');
	$response = gzuncompress(base64_decode($resultado));
	if ($response!='error' && $response!=''){
		$baseurlfact = $response;
		//carga lista productos
		if (get_option('FacturaONE_ivaincluido')==0){
			$resultado = FONE_url_get_contents2('updateproductosiniva',$item_lookup_id);
		}else{
			$resultado = FONE_url_get_contents2('updateproducto',$item_lookup_id);
		}
		$response = json_decode(gzuncompress(base64_decode($resultado)), true);
		
		$idlinarray=0;
		$userids='';
		$statusarray=array();
		
		foreach ($response as $posicionA=>$jugadorA)
		   {
			foreach ($jugadorA as $posicionB=>$jugadorB)
			   {
					if ($posicionB=='q1'){
						foreach ($jugadorB as $posicionC=>$jugadorC)
						   {
								$catalogo_tarifa_cliente = $jugadorC['catalogo_tarifa_cliente'];
								update_option('FacturaONE_catalogo_tarifa_cliente', $catalogo_tarifa_cliente);
						   }
					}
					if ($posicionB=='q2'){
						foreach ($jugadorB as $posicionC=>$jugadorC)
						   {
								//echo $jugadorC['item_lookup_id'].' '.$jugadorC['item_name']; echo '<br>';continue;
							
								//selecciona tarifa web desde opcion catalogo / tarifa de precios web
								if ($catalogo_tarifa_cliente==1){
									$item_price = $jugadorC['item_price'];
								} else if ($catalogo_tarifa_cliente==2){
									$item_price = $jugadorC['item_tarifa2'];
								} else if ($catalogo_tarifa_cliente==3){
									$item_price = $jugadorC['item_tarifa3'];
								} else if ($catalogo_tarifa_cliente==4){
									$item_price = $jugadorC['item_tarifa4'];
								} else if ($catalogo_tarifa_cliente==5){
									$item_price = $jugadorC['item_tarifa5'];
								} else {
									$item_price = 0;
								}
							
								FONE_subeproducto($baseurlfact, true, $jugadorC['item_lookup_id'],$jugadorC['item_name'],$jugadorC['item_description'],$jugadorC['item_description_web'],$jugadorC['urlimage'],$item_price,$jugadorC['item_price'],$jugadorC['item_tarifa2'],$jugadorC['item_tarifa3'],$jugadorC['item_tarifa4'],$jugadorC['item_tarifa5'],$jugadorC['item_kgs_neto'],$jugadorC['item_kgs_bruto'],$jugadorC['familia_id'],$jugadorC['subfamilia_id'],$jugadorC['item_activado'],$jugadorC['tax_rate_id'],$jugadorC['variaciones'],$jugadorC['compuestos'], $jugadorC['item_txt_alternativo'],$jugadorC['marca'],$jugadorC['ean'],$jugadorC['item_clientid'],$jugadorC['item_onorder'],$jugadorC['item_wp_stock_control'],$jugadorC['item_wp_stock_visible'],$jugadorC['item_seo_fraseobjetivo'],$jugadorC['item_seo_metadescripcion'],$jugadorC['item_wp_visibilidad'],$jugadorC['item_wp_destacado'],$jugadorC['item_slug'],$jugadorC['item_description_web_corta'],$jugadorC['item_wp_backorders'],$jugadorC['item_categorias'],$jugadorC['item_sku'],$jugadorC['familia_web_activate'],$jugadorC['marca_web_activate'],$jugadorC['largo'],$jugadorC['ancho'], $jugadorC['alto'], $jugadorC['item_minquantity'], $jugadorC['item_maxquantity'] ,$jugadorC['item_stepquantity'] );
							
								$urlimage = $jugadorC['urlimage'];
								$item_name = $jugadorC['item_name'];
								$item_txt_alternativo = $jugadorC['item_txt_alternativo'];
								FONE_subeimagen($baseurlfact,$item_lookup_id,$urlimage,$item_name,$item_txt_alternativo,false,true);
						   }
					}
					if ($posicionB=='q3'){
						foreach ($jugadorB as $posicionC=>$jugadorC)
						   {
								$pic_urlimage_variacion=trim(base64_decode($jugadorC['image']));
								if ($pic_urlimage_variacion!='' && $pic_urlimage_variacion!=$urlimage){
									$hijo_item_lookup_id=$jugadorC['item_lookup_id'];
									$pic_item_name_variacion=$jugadorC['item_name'];
									$pic_item_txt_alternativo_variacion=$jugadorC['item_txt_alternativo'];
									FONE_subeimagen_variacion($baseurlfact, $hijo_item_lookup_id, $pic_urlimage_variacion, $pic_item_name_variacion, $pic_item_txt_alternativo_variacion,false);
								}
						   }
					}				
				}
		   }
		FONE_clearcache();
	}
}

if (isset($_POST['action']) && $_POST['action']=='delete' && $_POST['item_lookup_id']!=''){
	//genera proceso sobre todas las db
	$pagePath = explode('/wp-content/', dirname(__FILE__));
	include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
	//borra producto
	FONE_borrar_producto($_POST['item_lookup_id']);
}

//import2erp
if (isset($_POST['action']) && $_POST['action']=='import2erp' && $_POST['idkey']!='' && $_POST['hash']!=''){
	//genera proceso sobre todas las db
	$pagePath = explode('/wp-content/', dirname(__FILE__));
	include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
	FONE_import2erp($_POST['idkey'],$_POST['hash']);
}
?>