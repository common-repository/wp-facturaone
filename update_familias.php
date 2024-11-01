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
		$baseurlfact = $response;
		$resultado = FONE_url_get_contents('familias');
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
								array_push($statusarray, array($idlinarray, array($posicionB, $jugadorC['familia_name'], $jugadorC['subfamilia_name'], $jugadorC['familia_id'], $jugadorC['subfamilia_id'], $jugadorC['familia_web_activate'],$jugadorC['familia_wpseo_title'],$jugadorC['familia_wpseo_focuskw'],$jugadorC['familia_wpseo_desc'],$jugadorC['familia_slug'])));
								$userids = $userids.'; '.$idlinarray;
								$idlinarray=$idlinarray+1;
								//$userids = $userids.'; '.$jugadorC['item_lookup_id'];
						   }
					}
					if ($posicionB=='q2'){
						foreach ($jugadorB as $posicionC=>$jugadorC)
						   {
								array_push($statusarray, array($idlinarray, array("q2", $jugadorC['marca_name'], $jugadorC['marca_image'], $baseurlfact, $jugadorC['marca_web_activate'])));
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
		FONE_execajax('update_familias',$status_backgroundid);
	}
}
function q1($arrayvariables){
	//$arrayvariables[1] para opcionvar 1
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
		update_option('_facturaone_status_title','Actualizando Familias/Subfamilias ...<br>'.$familia_name.' '.$subfamilia_name);
		$pagePath = explode('/wp-content/', dirname(__FILE__));
		include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
		FONE_familias($familia_name, $subfamilia_name, $familia_id, $subfamilia_id, $familia_web_activate, $familia_wpseo_title, $familia_wpseo_focuskw, $familia_wpseo_desc, $familia_slug);
	}
}
function q2($arrayvariables){
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

//actualiza marca desde f1
//https://wp.facturaone.com/wp-content/plugins/wp-facturaone/update_familias.php
//if (1==1){
if (isset($_POST['action']) && $_POST['action']=='updatemarca' && $_POST['marca_id']!=''){
	$marca_id = $_POST['marca_id'];
	//$marca_id = 3;
	//genera proceso sobre todas las db
	$pagePath = explode('/wp-content/', dirname(__FILE__));
	include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
	//carga url db general y check conexion
	$resultado = FONE_url_get_contents('url');
	$response = gzuncompress(base64_decode($resultado));
	if ($response!='error' && $response!=''){
		$baseurlfact = $response;
		//carga url db general y check conexion
		$marca_name = '';
		$marca_image = '';
		$marca_web_activate = '';
		$resultado = FONE_url_get_contents2('updatemarca',$marca_id); //idproducto=4 url externa
		$response = json_decode(gzuncompress(base64_decode($resultado)),true);
		foreach ($response["0"]["q1"] as $area) {
			$marca_name = $area["marca_name"];
			$marca_web_activate = $area["marca_web_activate"];
			$base_url = $baseurlfact.'/uploads/documentos/marcas/'.$area["marca_image"];
			break;
		}
		if ($marca_name!=''){
			FONE_creamarca($marca_name,$base_url, NULL, $marca_web_activate);
		}		
	}
	FONE_clearcache();
}
//if ($_GET['action']=='updatesubfamilia' && $_POST['subfamilia_id']!=''){
//actualiza producto desde f1
if (isset($_POST['action']) && $_POST['action']=='updatefamilia' && $_POST['familia_id']!=''){
	$familia_id = $_POST['familia_id'];
	//genera proceso sobre todas las db
	$pagePath = explode('/wp-content/', dirname(__FILE__));
	include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));	
	//carga url db general y check conexion
	$familia_name = '';
	$resultado = FONE_url_get_contents2('updatefamilia',$familia_id); //idproducto=4 url externa
	$response = json_decode(gzuncompress(base64_decode($resultado)),true);
	foreach ($response["0"]["q1"] as $area) {
		$familia_name = $area["familia_name"];
		$familia_web_activate = $area["familia_web_activate"];
		$familia_wpseo_title=$area["familia_wpseo_title"];
		$familia_wpseo_focuskw=$area["familia_wpseo_focuskw"];
		$familia_wpseo_desc=$area["familia_wpseo_desc"];
		$familia_slug=$area["familia_slug"];
		break;
	}
	if ($familia_name!=''){
		FONE_familias($familia_name, '', $familia_id, '', $familia_web_activate, $familia_wpseo_title, $familia_wpseo_focuskw, $familia_wpseo_desc, $familia_slug);
	}
	FONE_clearcache();
}
if (isset($_POST['action']) && $_POST['action']=='updatesubfamilia' && $_POST['subfamilia_id']!=''){
	$subfamilia_id = $_POST['subfamilia_id'];
	//genera proceso sobre todas las db
	$pagePath = explode('/wp-content/', dirname(__FILE__));
	include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));	
	//carga url db general y check conexion
	$subfamilia_name = '';
	$resultado = FONE_url_get_contents2('updatesubfamilia',$subfamilia_id); //idproducto=4 url externa
	$response = json_decode(gzuncompress(base64_decode($resultado)),true);
	foreach ($response["0"]["q1"] as $area) {
		$familia_name = $area["familia_name"];
		$familia_id = $area["familia_id"];
		$subfamilia_name = $area["subfamilia_name"];
		$subfamilia_id = $area["subfamilia_id"];
		$subfamilia_wpseo_title = $area["subfamilia_wpseo_title"];
		$subfamilia_wpseo_focuskw = $area["subfamilia_wpseo_focuskw"];
		$subfamilia_wpseo_desc = $area["subfamilia_wpseo_desc"];
		$subfamilia_slug = $area["subfamilia_slug"];
		break;
	}
	if ($familia_name!=''){
		FONE_familias($familia_name, $subfamilia_name, $familia_id, $subfamilia_id, '', $subfamilia_wpseo_title, $subfamilia_wpseo_focuskw, $subfamilia_wpseo_desc, $subfamilia_slug);
	}
	FONE_clearcache();
}
?>