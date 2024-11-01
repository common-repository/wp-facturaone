<?php
if (isset($_POST['action']) && ($_POST['action']=='start' || $_POST['action']=='startall' )){
	if ($_POST['action']=='startall'){$sobreescribe=1;}else{$sobreescribe=0;}
	//genera proceso sobre todas las db
	$pagePath = explode('/wp-content/', dirname(__FILE__));
	include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
	//carga url db general y check conexion
	$resultado = FONE_url_get_contents('url');
	$response = gzuncompress(base64_decode($resultado));
	if ($response!='error' && $response!=''){
		$baseurlfact = $response;
		//carga lista productos
		$resultado = FONE_url_get_contents('updateimagenes');
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
								array_push($statusarray, array($idlinarray,array($posicionB,$jugadorC['item_lookup_id'],$jugadorC['item_name'],$jugadorC['item_txt_alternativo'],$baseurlfact,$jugadorC['urlimage'],$sobreescribe)));
								$userids = $userids.'; '.$idlinarray;
								$idlinarray=$idlinarray+1;
								//$userids = $userids.'; '.$jugadorC['item_lookup_id'];
						   }
					}
					if ($posicionB=='q2'){
						foreach ($jugadorB as $posicionC=>$jugadorC)
						   {
								array_push($statusarray, array($idlinarray,array($posicionB,$jugadorC['item_lookup_id'],$jugadorC['item_name'],$jugadorC['item_txt_alternativo'],$baseurlfact,$jugadorC['urlimage'],$sobreescribe)));
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
		
		//en el caso que pasa de 50 vuelve a ejecutar bucle desde inicio
		FONE_execajax('update_images',$status_backgroundid);
	}
}
function q1($arrayvariables){
	//$arrayvariables[1] para opcionvar 1
	$idproducto=trim($arrayvariables[1]);
	$item_name=trim($arrayvariables[2]);
	$item_txt_alternativo=trim($arrayvariables[3]);
	$baseurlfact=trim($arrayvariables[4]);
	$urlimage=trim($arrayvariables[5]);
	$sobreescribe=trim($arrayvariables[6]);
	if ($sobreescribe==1){$sobreescribe=true;}else{$sobreescribe=false;}

	if ($idproducto!='' && $urlimage!=''){
		update_option('_facturaone_status_title','Actualizando Imagenes ...<br>'.$item_name);
		$pagePath = explode('/wp-content/', dirname(__FILE__));
		include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
		FONE_subeimagen($baseurlfact,$idproducto,$urlimage,$item_name,$item_txt_alternativo,false,$sobreescribe);
	}
}
function q2($arrayvariables){
	$item_lookup_id=trim($arrayvariables[1]);
	$item_name=trim($arrayvariables[2]);
	$item_txt_alternativo=trim($arrayvariables[3]);
	$baseurlfact=trim($arrayvariables[4]);
	$urlimage=trim($arrayvariables[5]);
	$sobreescribe=trim($arrayvariables[6]);
	if ($sobreescribe==1){$sobreescribe=true;}else{$sobreescribe=false;}
	
	if ($item_lookup_id!='' && $urlimage!=''){
		update_option('_facturaone_status_title','Actualizando Variaciones Imagenes ...<br>'.$item_name);
		$pagePath = explode('/wp-content/', dirname(__FILE__));
		include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
		FONE_subeimagen_variacion($baseurlfact, $item_lookup_id, $urlimage, $item_name, $item_txt_alternativo,$sobreescribe);
	}
}
?>