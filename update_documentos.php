<?php
if (isset($_POST['action']) && $_POST['action']=='start'){
	//genera proceso sobre todas las db
	$pagePath = explode('/wp-content/', dirname(__FILE__));
	include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
	//carga url db general y check conexion
	$resultado = FONE_url_get_contents('url');
	$response = gzuncompress(base64_decode($resultado));
	$urluser = $response;
	if ($response!='error' && $response!=''){
		//carga lista productos
		$resultado = FONE_url_get_contents('documentos');
		$response = json_decode(gzuncompress(base64_decode($resultado)), true);
		
		//elimina anteriores documentos
		FONE_clearmetadata('post', 0, '_wc_product_documents', '', true);
		FONE_clearmetadata('post', 0, '_wc_product_documents_display', '', true);
		FONE_clearmetadata('post', 0, '_wc_product_documents_title', '', true);
		
		$idlinarray=0;
		$userids='';
		$statusarray=array();
		foreach ($response as $jugadorA)
		   {
				$idlookup = $jugadorA['idlookup'];
				$filesarray=array();
				foreach ($jugadorA['files'] as $jugadorB)
				{
					array_push($filesarray, $jugadorB);
				}
			
				array_push($statusarray, array($idlinarray,array("q1",$urluser,$idlookup,$filesarray)));
				$userids = $userids.'; '.$idlinarray;
				$idlinarray=$idlinarray+1;
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
		FONE_execajax('update_documentos',$status_backgroundid);
	}
}
function q1($arrayvariables){
	$urluser=trim($arrayvariables[1]);
	$idlookup=trim($arrayvariables[2]);
	$filesarray=$arrayvariables[3];
	
	if ($urluser!='' && $idlookup!=''){
		update_option('_facturaone_status_title','Actualizando Ficheros Adjuntos');
		$pagePath = explode('/wp-content/', dirname(__FILE__));
		include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
		FONE_documentos($urluser, $idlookup, $filesarray);
	}
}


//actualiza producto desde f1
if (isset($_POST['action']) && $_POST['action']=='updatefactura' && $_POST['facturaurl']!='' && $_POST['invoice_wp_order']!=''){
	$facturaurl = base64_decode($_POST['facturaurl']);
	$invoice_wp_order = $_POST['invoice_wp_order'];

	//genera proceso sobre todas las db
	$pagePath = explode('/wp-content/', dirname(__FILE__));
	include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
	
	update_post_meta( $invoice_wp_order, '_facturaURL', $facturaurl );
}
?>