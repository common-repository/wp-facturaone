<?php
if ($_POST['action'] == 'update'){
	$pagePath = explode('/wp-content/', dirname(__FILE__));
	include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));	

	$resultado = FONE_url_get_contents2('updateusuario',$_POST['data']);
	$response = json_decode(gzuncompress(base64_decode($resultado)));
	
	FONE_clientes_updatepass($response->user_email, $response->pass);
}
if ($_POST['action'] == 'deleteusuario'){
	$pagePath = explode('/wp-content/', dirname(__FILE__));
	include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));	

	$resultado = FONE_url_get_contents2('deleteusuario',$_POST['data']);
	$response = json_decode(gzuncompress(base64_decode($resultado)));
	
	FONE_clientes_delete($response->user_email, $response->client_id);
}
if ($_POST['action'] == 'creausuario'){
	$pagePath = explode('/wp-content/', dirname(__FILE__));
	include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));	
	
	$resultado = FONE_url_get_contents2('creausuario',$_POST['data']);
	$response = json_decode(gzuncompress(base64_decode($resultado)));
	
	$email_address = $response->user_email;
	$password = $response->pass;
	$username = $response->user_name;
	$tarifa = $response->client_tarifa;
	$client_name = $response->client_name;
	$client_address_1 = $response->client_address_1;
	$client_city = $response->client_city;
	$client_zip = $response->client_zip;
	$phone = $response->client_phone;
	$fone_client_id = $response->client_id;
	$client_country = "ES";
	$client_nif = $response->client_nif;
	FONE_clientes($email_address,$password,$username,$tarifa,$client_name,$client_address_1,$client_city,$client_zip,$phone,$fone_client_id,$client_country,$client_nif,1);
	FONE_cliente_tarifas_especiales($fone_client_id);
}
if ($_POST['action'] == 'updatecliente'){
	$pagePath = explode('/wp-content/', dirname(__FILE__));
	include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));	
	$client_id = $_POST['client_id'];
	$client_tarifa = '';
	$resultado = FONE_url_get_contents2('tarifaclient',$client_id); //idproducto=4 url externa
	$response = json_decode(gzuncompress(base64_decode($resultado)),true);
	foreach ($response["0"]["q1"] as $area) {
		$client_tarifa = trim(($area["client_tarifa"]));
		$client_nif = trim(($area["client_nif"]));
		break;
	}
	if ($client_tarifa>0){
		FONE_cliente_update($client_id, $client_tarifa, $client_nif);
	}
	//actualiza precios especiales
	FONE_cliente_tarifas_especiales($client_id);
}
if ($_POST['action'] == 'update_tarifas_especiales'){
	$pagePath = explode('/wp-content/', dirname(__FILE__));
	include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));	
	$client_id = $_POST['client_id'];
	//actualiza precios especiales
	FONE_cliente_tarifas_especiales($client_id);
}
?>