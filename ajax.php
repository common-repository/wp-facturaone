<?php
if (isset($_POST['modo']) && $_POST['modo']=='backprocess'){
	//progressbar
	$status_realizado = wp_facturaone_get_optionvalue_from_tableoption('_facturaone_status_realizado');
	$status_realizado = trim(substr($status_realizado, 1)); 
	$status_backgroundid = wp_facturaone_get_optionvalue_from_tableoption('_facturaone_status_backgroundid');
	$status_value = wp_facturaone_get_optionvalue_from_tableoption('_facturaone_status_value');
	$status_title = wp_facturaone_get_optionvalue_from_tableoption('_facturaone_status_title');
	$count_status_value = count(explode(';', $status_value));
	$count_status_realizado = count(explode(';', $status_realizado));
	if ($count_status_value>0){$porcentaje=($count_status_realizado/$count_status_value)*100;}
	$response = array('string' => $status_realizado, 'status_backgroundid' => $status_backgroundid, 'porcentaje' => $porcentaje, 'status_title' => $status_title);
	echo json_encode($response);
	die;//return;
	exit;
}else if (isset($_POST['action']) && $_POST['action']=='stop'){
	//stop
	wp_facturaone_set_optionvalue_from_tableoption('_facturaone_status_backgroundid','0');
	wp_facturaone_set_optionvalue_from_tableoption('_facturaone_status_realizado','');
	wp_facturaone_set_optionvalue_from_tableoption('_facturaone_status_title','');	
	if (file_exists('ajax.json')){unlink('ajax.json');}
	FONE_clearcache();
	exit;
}else{
	//procesos
	if (isset($_GET['backgroundtask'])){$backgroundtask=$_GET['backgroundtask'];}
	if (isset($_GET['backgroundid'])){$backgroundid=$_GET['backgroundid'];}
	if (isset($_GET['apikey'])){$apikey=$_GET['apikey'];}
	//comprueba si es tarea de background--------------------------PARA EJECUTAR EN SERVER VIA CU RL
	if ($backgroundtask!='' && $backgroundid!='' && $apikey==wp_facturaone_get_optionvalue_from_tableoption('FacturaONE_APIKEY')) {
		$status_value = wp_facturaone_get_optionvalue_from_tableoption('_facturaone_status_value');
		if ($status_value==''){exit;}
		$status_realizado = wp_facturaone_get_optionvalue_from_tableoption('_facturaone_status_realizado');
		$array_value = explode(';', $status_value);
		$array_realizado = explode(';', $status_realizado);
		$count = 0;
		//$FacturaONE_ajax_cicle = wp_facturaone_get_optionvalue_from_tableoption('FacturaONE_ajax_cicle');
		//if ($FacturaONE_ajax_cicle==''){$FacturaONE_ajax_cicle=100;}
		$FacturaONE_ajax_cicle=100;

		//carga lista array de processos
		//$statusarray=json_decode(wp_facturaone_get_optionvalue_from_tableoption('_facturaone_status_array'));
		if (!file_exists('ajax.json')){exit;}
		$statusarray = json_decode(file_get_contents('ajax.json'));

		foreach ($array_value as $values)
		{
			//carga status_backgroundid status actual por si se ha puesto a 0 para parar tarea
			$status_backgroundid = wp_facturaone_get_optionvalue_from_tableoption('_facturaone_status_backgroundid');
			if ($status_backgroundid==0 || $status_backgroundid==''){ 
				wp_facturaone_set_optionvalue_from_tableoption('_facturaone_status_realizado','');
				wp_facturaone_set_optionvalue_from_tableoption('_facturaone_status_title','');
				if (file_exists('ajax.json')){unlink('ajax.json');}
				FONE_clearcache();
				exit;  
			}else if ($status_backgroundid!=$backgroundid){
				exit;
			}else{
				//si el valor no esta en el array realizado lo añade y actualiza usuario
				if (!in_array($values, $array_realizado)){
					$count = $count + 1;
					if ($count>$FacturaONE_ajax_cicle){
						//al llegar a 100 ($FacturaONE_ajax_cicle) bucles cierra php actual y abre nuevo cu rl

						//$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	//					if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
	//						$link = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	//					}else{
	//						$link = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	//					}
	//					wp_facturaone_set_optionvalue_from_tableoption('_facturaonurl'.time(), $link);
	//					header('Location: '.$link.'&time='.time()); //header("Refresh:0");

						FONE_execajax($backgroundtask,$backgroundid); 
						exec('kill -9 ' . getmypid());
						die;
						//return;
						exit;
					}else{
						//actualiza para processbar
						$a= wp_facturaone_get_optionvalue_from_tableoption('_facturaone_status_realizado');
						wp_facturaone_set_optionvalue_from_tableoption('_facturaone_status_realizado',$a.';'.$values);

						//ejecuta proceso de dentro de $statusarray		
						$arraythispro=$statusarray[trim($values)][1]; // funcion q1,q2,.: arraythispro[0] y envia array arraythispro[0]

						//log ejecucion
						//$arrk = implode("|",$arraythispro);
						//wp_facturaone_set_optionvalue_from_tableoption('_facturaone_log'.time(), $arraythispro[0].' '.$arrk);
						
						//ejecuta update bucle	//upimagess($values);
						include_once($backgroundtask.'.php');
						call_user_func($arraythispro[0],$arraythispro); 
					}
				}
			}
		}
		//bucle finalizado
		wp_facturaone_set_optionvalue_from_tableoption('_facturaone_status_backgroundid','0');
		wp_facturaone_set_optionvalue_from_tableoption('_facturaone_status_realizado','');
		wp_facturaone_set_optionvalue_from_tableoption('_facturaone_status_title','');
		if (file_exists('ajax.json')){unlink('ajax.json');}
		FONE_clearcache();
		die;
		//return;
		exit;	
	}
}


//funciones de actualizacion mysql ---------------------
function wp_facturaone_get_optionvalue_from_tableoption($option_name){
	//include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php' );
	include_once(dirname(__DIR__,3).'/wp-config.php');
	global $wpdb;
	return get_option($option_name);
}
function wp_facturaone_set_optionvalue_from_tableoption($option_name,$value){
	//include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php' );
	include_once(dirname(__DIR__,3).'/wp-config.php');
	global $wpdb;
	update_option($option_name,$value);
}
?>