<?php
if (isset($_POST['action']) && $_POST['action']=='start'){
	//genera proceso sobre todas las db
	$pagePath = explode('/wp-content/', dirname(__FILE__));
	include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
	//carga url db general y check conexion
	$resultado = FONE_url_get_contents('url');
	$response = gzuncompress(base64_decode($resultado));
	if ($response!='error' && $response!=''){
		//carga lista productos
		$resultado = FONE_url_get_contents('stock');
		$response = json_decode(gzuncompress(base64_decode($resultado)), true);
		
		$idlinarray=0;
		$userids='';
		$statusarray=array();
		foreach ($response as $posicionA=>$jugadorA)
		   {
			foreach ($jugadorA as $posicionB=>$jugadorB)
			   {
					$totalregistros = count($jugadorB);
					if ($posicionB=='q1'){
						foreach ($jugadorB as $posicionC=>$jugadorC)
						   {
								array_push($statusarray, array($idlinarray,array("q1",$jugadorC['id'],$jugadorC['stock'],$jugadorC['item_wp_stock_control'],$jugadorC['item_wp_stock_visible'],$jugadorC['item_onorder'],$jugadorC['item_wp_backorders'])));
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
		
		FONE_execajax('update_stock',$status_backgroundid);
	}
}
function q1($arrayvariables){
	$id=trim($arrayvariables[1]);
	$stock=trim($arrayvariables[2]);
	$item_wp_stock_control=trim($arrayvariables[3]);
	$item_wp_stock_visible=trim($arrayvariables[4]);
	$item_onorder=trim($arrayvariables[5]);
	$item_wp_backorders=trim($arrayvariables[6]);
	
	if ($id!=''){
		update_option('_facturaone_status_title','Actualizando Stock...<br>'.$id.' -> '.$stock.' unidades');
		$pagePath = explode('/wp-content/', dirname(__FILE__));
		include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
		FONE_actualizastockproducto($id, $stock, $item_wp_stock_control, $item_wp_stock_visible, $item_onorder, $item_wp_backorders);
	}
}
if ($_POST['action'] == 'updatestock'){
	$pagePath = explode('/wp-content/', dirname(__FILE__));
	include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));	
	
	$response = json_decode(base64_decode($_POST['data']));
	$item_lookup_id = $response->item_lookup_id;
	$item_stock = $response->item_stock;
	$item_wp_stock_control = $response->item_wp_stock_control;
	$item_wp_stock_visible = $response->item_wp_stock_visible;
	$item_onorder = $response->item_onorder;
	$item_wp_backorders = $response->item_wp_backorders;
	if ($item_lookup_id>0){
		FONE_actualizastockproducto($item_lookup_id, $item_stock, $item_wp_stock_control, $item_wp_stock_visible, $item_onorder, $item_wp_backorders);
	}
}
if ($_POST['action'] == 'updatemultiplestock'){
	$pagePath = explode('/wp-content/', dirname(__FILE__));
	include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));	
	
	$data = json_decode(base64_decode($_POST['data']));
	foreach ($data as $response){
		$item_lookup_id = $response->item_lookup_id;
		$item_stock = $response->item_stock;
		$item_wp_stock_control = $response->item_wp_stock_control;
		$item_wp_stock_visible = $response->item_wp_stock_visible;
		$item_onorder = $response->item_onorder;
		$item_wp_backorders = $response->item_wp_backorders;
		if ($item_lookup_id>0){
			FONE_actualizastockproducto($item_lookup_id, $item_stock, $item_wp_stock_control, $item_wp_stock_visible, $item_onorder, $item_wp_backorders);
		}
		//update_option('_abcbab_afacturaone_status_array'.$row->item_lookup_id,$row->item_stock);
	}
}
?>