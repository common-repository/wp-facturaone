<style>
	#FONE-header-upgrade-message p .dashicons {
		color: #f2a64c;
		margin-right: 5px;
	}
	#FONE-header-upgrade-message {
		text-align: center;
		background-color: #f5f0c0;
		color: #222;
		padding: 10px;
		margin-left: -20px;
		-webkit-box-shadow: 0 0 3px rgb(0 0 0 / 20%);
		box-shadow: 0 0 3px rgb(0 0 0 / 20%);
	}
	#FONE-p-message{
		margin: 0;
	}
	.swal2-popup {
	  font-size: 0.9rem !important;
	  /*font-family: Georgia, serif;*/
	}	
</style>
<?php
$menutab='';
$mensaje='';
//tips
if (get_option('mod_wordpress')<101){
	$tip='	<div id="FONE-header-upgrade-message">
				<p id="FONE-p-message"><span class="dashicons dashicons-info"></span>
					Facturaone te regala el primer año de <b>Hosting PRO Dedicado</b> para tu WooCoommerce <a href="https://www.facturaone.com/hosting" target="_blank">Pulsa aquí para más información</a> Oferta exclusiva para nuevos Clientes Empresa</p>
			</div>';
	if (get_option('FacturaONE_tip')!=''){$tip=get_option('FacturaONE_tip');}	
	echo $tip;
}
//clave registro apikey
if (isset($_POST['fact_nonce_action'])){
	if (wp_verify_nonce($_POST['fact_nonce_action'], 'fact_nonce_action')){
		if(isset($_POST['action']) && $_POST['action'] == "salvaropciones"){
			$FacturaONE_APIKEY = sanitize_text_field(trim($_POST['FacturaONE_APIKEY']));
			$FacturaONE_EMAIL = sanitize_email(trim($_POST['FacturaONE_EMAIL']));
			update_option('FacturaONE_APIKEY',$FacturaONE_APIKEY);
			update_option('FacturaONE_EMAIL',$FacturaONE_EMAIL);
			FONE_validardatos();
			$menutab='Ajustes';
		}
		if(isset($_POST['FacturaONE_ajax_cicle'])){
			update_option('FacturaONE_ajax_cicle',sanitize_text_field($_POST['FacturaONE_ajax_cicle']));
			$mensaje='Cambio realizado correctamente';
			$menutab='Avanzado';
		}
		//asignar taxrate productos entre F1-WooCoomerce
		foreach ($_POST as $key => $value)
		{
		  if (strpos($key, 'FacturaONE_taxrate_') !== false)  
		  {
			$slug = $_POST[$key];
			update_option($key, sanitize_text_field($slug)); 
			$mensaje='Cambio realizado correctamente';
			$menutab='Impuestos';
		  }
		}		
		if(isset($_POST['FacturaONE_ivaincluido'])){
			update_option('FacturaONE_ivaincluido',sanitize_text_field($_POST['FacturaONE_ivaincluido']));
			$mensaje='Cambio realizado correctamente';
			$menutab='Impuestos';
		}
		if(isset($_POST['FacturaONE_preciosusuariosregistrados'])){
			update_option('FacturaONE_preciosusuariosregistrados',sanitize_text_field($_POST['FacturaONE_preciosusuariosregistrados']));
			if ( function_exists( 'wp_cache_post_change' ) ) { wp_cache_clear_cache(); }
			if ( function_exists( 'w3tc_pgcache_flush') ) { w3tc_pgcache_flush(); }
			if ( function_exists( 'wpfc_clear_all_cache') ) { wpfc_clear_all_cache(true); }
			if ( function_exists( 'rocket_clean_domain' ) ) { rocket_clean_domain(); }
			$mensaje='Cambio realizado correctamente';
			$menutab='Avanzado';
		}
		if(isset($_POST['FacturaONE_conexionSSL'])){
			update_option('FacturaONE_conexionSSL',sanitize_text_field($_POST['FacturaONE_conexionSSL']));
			//$mensaje='Cambio realizado correctamente';
			$menutab='Ajustes';
		}
		if(isset($_POST['FacturaONE_multisiteped'])){
			$site_id=get_current_blog_id();
			update_blog_option($site_id, 'FacturaONE_multisiteped', sanitize_text_field($_POST['FacturaONE_multisiteped']));
			$menutab='Ajustes';
		}
		if(isset($_POST['FacturaONE_dynamicmenu'])){
			update_option('FacturaONE_dynamicmenu',sanitize_text_field($_POST['FacturaONE_dynamicmenu']));
			$mensaje='Cambio realizado correctamente';
			$menutab='Avanzado';
		}
		if(isset($_POST['FacturaONE_pedidominimo'])){
			update_option('FacturaONE_pedidominimo',sanitize_text_field($_POST['FacturaONE_pedidominimo']));
			$mensaje='Cambio realizado correctamente';
			$menutab='Avanzado';
		}		
		if(isset($_POST['FacturaONE_backorder_message'])){
			update_option('FacturaONE_backorder_message',sanitize_text_field($_POST['FacturaONE_backorder_message']));
			$mensaje='Cambio realizado correctamente';
			$menutab='Avanzado';
		}		
		if(isset($_POST['FacturaONE_sincif'])){
			update_option('FacturaONE_sincif',sanitize_text_field($_POST['FacturaONE_sincif']));
			$mensaje='Cambio realizado correctamente';
			$menutab='Avanzado';
		}
		if(isset($_POST['FacturaONE_widgetsubcategorias'])){
			update_option('FacturaONE_widgetsubcategorias',sanitize_text_field($_POST['FacturaONE_widgetsubcategorias']));
			$mensaje='Cambio realizado correctamente';
			$menutab='Avanzado';
		}		
		if(isset($_POST['FONE_OcultaProductosNOIMAGEN'])){
			update_option('FONE_OcultaProductosNOIMAGEN',sanitize_text_field($_POST['FONE_OcultaProductosNOIMAGEN']));
			$mensaje='Cambio realizado correctamente';
			$menutab='Avanzado';
		}
		if(isset($_POST['FacturaONE_nombre_producto'])){
			update_option('FacturaONE_nombre_producto',sanitize_text_field($_POST['FacturaONE_nombre_producto']));
			$mensaje='Cambio realizado correctamente';
			$menutab='Avanzado';
		}
		if(isset($_POST['FacturaONE_pers_agotado'])){
			update_option('FacturaONE_pers_agotado',sanitize_text_field($_POST['FacturaONE_pers_agotado']));
			$mensaje='Cambio realizado correctamente';
			$menutab='Avanzado';
		}
		if(isset($_POST['action']) && $_POST['action'] == "BORRAR PRODUCTOS"){
			FONE_borrarproductos();
		}
		if ($mensaje!=''){echo("<div class='updated message' style='padding:10px;margin:16px 4px;margin-right: 19px;'>".$mensaje."</div>");}
	}
}
//if(function_exists('exec')){}else{echo "<div class='notice notice-warning settings-error' style='border-left-color: #ff0000;padding: 10px'>ERROR: Se recomienda la funcion exec() habilitada para el buen funcionamiento del plugin FacturaOne</div>";} 

//if(ini_get("max_execution_time")<300){echo "<div class='notice notice-warning settings-error' style='border-left-color: #ff0000;padding: 10px'>ERROR: max_execution_time: ".ini_get("max_execution_time")." ... se recomienda usar un valor mínimo de 300 para el correcto funcionamiento del plugin FacturaOne</div>";}

if(!is_plugin_active('wordpress-seo/wp-seo.php') && !is_plugin_active('wordpress-seo-premium/wp-seo-premium.php')){
	echo "<div class='notice notice-warning settings-error' style='border-left-color: #ff0000;padding: 10px'>ERROR: el plugin YOAST SEO no esta instalado/activado, FacturaOne no podrá rellenar la información de SEO</div>";
}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>WP FacturaOne</title>
<script src="<?php echo plugins_url( 'js/libs/jquery-1.7.1.min.js', __FILE__ );?>"></script>
<script src="<?php echo plugins_url( 'assets/sweetalert/sweetalert2.all.min.js', __FILE__ );?>"></script>
<link rel="stylesheet" href="<?php echo plugins_url( 'assets/sweetalert/sweetalert2.min.css', __FILE__ );?>">
<script>
	//window.onload = codeAddress;
	//window.onload = function() { codeAddress(); };
	document.addEventListener("DOMContentLoaded", function() {
	  codeAddress();
	});

	function codeAddress() {
		if ('<?php echo $menutab;?>'=='Avanzado'){
			openCity(0, "Avanzado");
		}else if ('<?php echo $menutab;?>'=='Ajustes'){
			openCity(0, "Ajustes");
		}else if ('<?php echo $menutab;?>'=='Impuestos'){
			openCity(0, "Impuestos");
		}else{
			openCity(0, "Importar");
		}
	}

	function openCity(evt, cityName) {
		  var i, tabcontent, tablinks;
		  tabcontent = document.getElementsByClassName("tabcontent");
		  for (i = 0; i < tabcontent.length; i++) {
			tabcontent[i].style.display = "none";
		  }
		  tablinks = document.getElementsByClassName("tablinks");
		  for (i = 0; i < tablinks.length; i++) {
			tablinks[i].className = tablinks[i].className.replace(" active", "");
		  }
		  document.getElementById(cityName).style.display = "block";
		  document.getElementById("button_"+cityName).className += " active";
		  //evt.currentTarget.className += " active";
	}
	function execbackground(phpfile,accion){
		if (accion=='start'){
			swal.fire({
			  type: 'info',
			  title: 'Iniciando Importación...',
			  html: 'El tiempo de carga depende de la cantidad de datos y la velocidad de transferencia al servidor. Por favor espere...',
			  showConfirmButton: false,
			  timer: 4000
			})	
		}
		jQuery.ajax({
			type: "POST",
			url: "<?php echo get_home_url();?>/?FONE_curlexec",
			data: { action: accion, phpfile: phpfile, apikey: '<?php echo get_option('FacturaONE_APIKEY');?>' }, 
			cache:true,
			timeout:0,
			success: function(data) {
				var response = JSON.parse(data);
				if (response.success == '1'){
					if (response.status == 'ejecutado'){
					}else if(response.status == 'reejecutado'){
						swal({
						  title: 'Importando Información...',
						  text: "¿Quiere detener la importación actual?",
						  type: 'warning',
						  showCancelButton: true,
						  confirmButtonColor: '#d33',
						  cancelButtonColor: '#3085d6',
						  confirmButtonText: 'Detener',
						  cancelButtonText: 'Continuar'
							}).then((result) => {
									if (result.value) {
									  execbackground('ajax.php','stop');
									}
								})	
					}
				}
			} 
		});
		event.preventDefault();	
	}
	function all_images(){
		if (document.getElementById('allimages').checked) {
			swal({
				title: 'Importar todas las imagenes',
				text: "¿Quiere volver a importar todas las imagenes?",
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Importar',
				cancelButtonText: 'Cancelar'
				}).then((result) => {
					if (result.value) {
						execbackground('update_images.php','startall')
					}else{
						document.getElementById("allimages").checked = false;
					}
			})	
        }
	}
	function execnoback(phpfile){
		//importante quitar post api
		window.open("<?php echo plugins_url('wp-facturaone'); ?>/" + phpfile, "_blank");
	}
</script>
</head>
<body>
	<div style="margin: 10px 20px 10px 2px;" onClick="window.open('https://www.facturaone.com', '_blank');">
		<div style="height: 0px;position:relative;">
			<img height="94" style="padding:16px;height:94px;width:94px;" src="<?php echo plugins_url( 'assets/icon-256x256.png', __FILE__ );?>">
		</div>
		<div style="background: url('<?php echo plugins_url( 'assets/page-header.jpg', __FILE__ );?>') 5% top/cover no-repeat;
		min-height:80px;display: flex;justify-content: flex-end;align-items: center;padding: 20px;"></div>
	</div>
	<div style="width:99%;">
		<div class="tab">
		  <button class="tablinks" style="width:150px;height:50px;" id="button_Importar" onclick="openCity(event, 'Importar')">Importar</button>
		  <button class="tablinks" style="width:150px;height:50px;" id="button_Ajustes" onclick="openCity(event, 'Ajustes')">Ajustes</button>
		  <button class="tablinks" style="width:150px;height:50px;" id="button_Avanzado" onclick="openCity(event, 'Avanzado')">Avanzado</button>
		  <button class="tablinks" style="width:150px;height:50px;" id="button_Impuestos" onclick="openCity(event, 'Impuestos')">Impuestos</button>
		</div>
		<div id="Importar" class="tabcontent" style="height:660px;">
			<form method='post'>
				<input type="hidden" name="fact_nonce_action" value="<?php echo wp_create_nonce('fact_nonce_action');?>"/>
				<table class="form-table">
					<tr><th>Importar información</th><td><button class="button button-primary" style="width:250px;height:70px;" onClick="execbackground('update_productos.php','start')" >IMPORTA PRODUCTOS</button></td></tr>
					
					<tr><th>Sincronizar Imagenes</th><td>
						<button class="button button-primary" style="width:250px;height:70px;" onClick="execbackground('update_images.php','start')" >IMPORTAR IMAGENES</button>
						<div style="position:relative;left:5px;top:5px;">
							<input id="allimages" type="checkbox" onclick="all_images();" >
							<label for="allimages" style="position:relative;top:-2px;left:-3px;" title="Vuelve a importar todas las imagenes">TODAS LAS IMAGENES</label>
						</div>
					</td></tr>

					<tr><th>Sincronizar Familias/Sub</th><td><button class="button button-primary" style="width:250px;height:70px;" onClick="execbackground('update_familias.php','start')" >ACTUALIZA FAMILIAS/SUB</button></td></tr>

					<tr><th>Actualiza documentos</th><td><button class="button button-primary" style="width:250px;height:70px;" onClick="execbackground('update_documentos.php','start')" >ACTUALIZA FICHEROS ADJUNTOS</button></td></tr>
					
					<tr><th>Sincronizar Stock</th><td><button class="button button-primary" style="width:250px;height:70px;" onClick="execbackground('update_stock.php','start')" >ACTUALIZA STOCK</button></td></tr>
				</table>
			</form>
		</div>
		<div id="Ajustes" class="tabcontent" style="height:660px;">
			<div style="padding: 25px;">
				<form method='post'>
					<input type="hidden" name="fact_nonce_action" value="<?php echo wp_create_nonce('fact_nonce_action');?>"/>
					<input type='hidden' name='action' value='salvaropciones'>
					<table class="form-table">
						<tr><th>FacturaONE_APIKEY</th>
							<td>
								<input class="regular-text" type='text' name='FacturaONE_APIKEY' id='FacturaONE_APIKEY' value='<?=get_option('FacturaONE_APIKEY')?>'>
							</td>
						</tr>
						<tr><th>FacturaONE_EMAIL</th>
							<td>
								<input class="regular-text" type='text' name='FacturaONE_EMAIL' id='FacturaONE_EMAIL' value='<?=get_option('FacturaONE_EMAIL')?>'>
							</td>
						</tr>
						<tr><th>Limite productos</th>
							<td>
								<div style="display:flex;">
								<input disabled class="regular-text" type='text' name='FacturaONE_EMAIL' id='FacturaONE_EMAIL' value='<?=get_option('mod_wordpress')?>'>
								<?php if (get_option('mod_wordpress')<101){echo '<div style="padding:6px;color:darkred;margin-left:-145px;">Modo Demostración</div>';} ?>
								</div>
							</td>
						</tr>
						<tr><th>Tarifa por defecto</th>
							<td>
								<div style="display:flex;">
								<input disabled class="regular-text" type='text' name='FacturaONE_catalogo_tarifa_cliente' id='FacturaONE_catalogo_tarifa_cliente' value='<?=get_option('FacturaONE_catalogo_tarifa_cliente')?>'>
								</div>
							</td>
						</tr>						
						<tr><th>Conexion Servidor SSL</th>
							<td>
								<select style="width:320px;" onchange="this.form.submit()" name="FacturaONE_conexionSSL" id="FacturaONE_conexionSSL">
									<option <?php if(get_option('FacturaONE_conexionSSL')==1){echo 'selected';} ?> value="1">SSL Certificado</option>
									<option <?php if(get_option('FacturaONE_conexionSSL')==0){echo 'selected';} ?> value="0">NO</option>
								</select> 
							</td>
						</tr>
						<?php if (is_multisite()) {
									echo '<tr><th>Multisite para pedidos</th><td>';
									$current_site_id = get_current_blog_id();
									$sites = get_sites();
									if (empty($sites)) {
										echo 'No hay sitios disponibles en esta red multisite.';
									} else {
										echo '<select style="width:320px;" onchange="this.form.submit()" name="FacturaONE_multisiteped" id="FacturaONE_multisiteped">';
										foreach ($sites as $site) {
											$site_id = $site->blog_id;
											$site_url = get_site_url($site_id);
											if(get_blog_option($current_site_id,'FacturaONE_multisiteped')==$site_id){
												echo '<option selected value="'.$site_id.'">'.$site_id.' - '.$site_url.'</option>';
											}else{
												echo '<option value="'.$site_id.'">'.$site_id.' - '.$site_url.'</option>';
											}
										}
										echo '</select>';
									}
									echo '</td></tr>';
								} 
						?>
						<tr><th></th>
							<td colspan='2'>
								<input class="button button-primary" type='submit' value='VALIDAR'>
							</td>
						</tr>
					</table>
				</form>
			</div>
			<div style="padding-left:20px;">
				<p>Crea tu cuenta y haz la prueba gratis durante 30 días <a href="https://www.facturaone.com/registro/" target="_blank">facturaone.com</a></p>
				<p>Encontrará su APIKEY dentro de su cuenta del ERP FacturaOne en el escritorio web <a href="https://www.ifactura.es" target="_blank">ifactura.es</a></p>
				<a href="<?php echo plugins_url( 'assets/apikey_screen.JPG', __FILE__ );?>" target="_blank"><img loading="lazy" src="<?php echo plugins_url( 'assets/apikey_screen.JPG', __FILE__ );?>" alt="" height="120" /></a>
				<p>Pulsa&nbsp;<input class="button button-primary" style="background-color:transparent;color:darkred;border-width:0px;padding:0px;" type='submit' name='action' value='aquí'>&nbsp;para eliminar info y productos de F1</p>
			</div>
		</div>
		

		<div id="Avanzado" class="tabcontent" style="height:660px;">
			<form method='post'>
				<input type="hidden" name="fact_nonce_action" value="<?php echo wp_create_nonce('fact_nonce_action');?>"/>
				<table class="form-table">
					<tr><th>*¹ Muestra Producto por:</th>
						<td>
							<select style="width:320px;" onchange="this.form.submit()" name="FacturaONE_nombre_producto" id="FacturaONE_nombre_producto">
							  <option <?php if (get_option('FacturaONE_nombre_producto')=='item_name'){echo 'selected';} ?> value="item_name">Nombre Articulo</option>
							  <option <?php if (get_option('FacturaONE_nombre_producto')=='item_description'){echo 'selected';} ?> value="item_description">Descripción Articulo</option>
							  <option <?php if (get_option('FacturaONE_nombre_producto')=='item_description_web'){echo 'selected';} ?> value="item_description_web">Descripción Web corta</option>
							  <option <?php if (get_option('FacturaONE_nombre_producto')=='item_namedescription'){echo 'selected';} ?> value="item_namedescription">Nombre Articulo & Descripción</option>
							  <option <?php if (get_option('FacturaONE_nombre_producto')=='item_namedescription_web'){echo 'selected';} ?> value="item_namedescription_web">Nombre Articulo & Descripción Web corta</option>
							  <option <?php if (get_option('FacturaONE_nombre_producto')=='item_description_name'){echo 'selected';} ?> value="item_description_name">Descripción Articulo & Nombre Articulo</option>
							</select> 
						</td>
					</tr>
					<tr><th>*¹ Oculta Productos Sin Imagen</th>
						<td>
							<select style="width:320px;" onchange="this.form.submit()" name="FONE_OcultaProductosNOIMAGEN" id="FONE_OcultaProductosNOIMAGEN">
								<option <?php if(get_option('FONE_OcultaProductosNOIMAGEN')==0){echo 'selected';} ?> value="0">No, oculta productos</option>
								<option <?php if(get_option('FONE_OcultaProductosNOIMAGEN')==1){echo 'selected';} ?> value="1">Si, oculta productos</option>
							</select> 
						</td>
					</tr>		
					<tr><th>Permite pedidos sin 'NIF/CIF/NIE'</th>
						<td>
							<select style="width:320px;" onchange="this.form.submit()" name="FacturaONE_sincif" id="FacturaONE_sincif">
								<option <?php if(get_option('FacturaONE_sincif')==""){echo 'selected';} ?> value="">NO</option>
								<option <?php if(get_option('FacturaONE_sincif')=="1"){echo 'selected';} ?> value="1">SI</option>
							</select> 							
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<p>*¹ Para aplicar estas opciones, debe volver a <a href="#" onClick="execbackground('update_productos.php','start')">Importar Productos</a>
							</p>
						</td>
					</tr>
					<tr><th>Mostrar precios a usuarios</th>
						<td>
							<select style="width:320px;" onchange="this.form.submit()" name="FacturaONE_preciosusuariosregistrados" id="FacturaONE_preciosusuariosregistrados">
								<option <?php if(get_option('FacturaONE_preciosusuariosregistrados')==0){echo 'selected';} ?> value="0">Todos los usuarios ven precios y carrito de compra</option>
								<option <?php if(get_option('FacturaONE_preciosusuariosregistrados')==1){echo 'selected';} ?> value="1">Solo usuarios Registrados</option>
							</select> 
						</td>
					</tr>	
					<!-- https://es.piliapp.com/symbol/subscript-superscript/ -->
					<tr><th>*² Widget Subcategorias</th>
						<td>
							<select style="width:320px;" onchange="this.form.submit()" name="FacturaONE_widgetsubcategorias" id="FacturaONE_widgetsubcategorias">
								<option <?php if(get_option('FacturaONE_widgetsubcategorias')==""){echo 'selected';} ?> value="">Desactivado</option>
								<option <?php if(get_option('FacturaONE_widgetsubcategorias')=="1"){echo 'selected';} ?> value="1">Activado</option>
							</select> 							
						</td>
					</tr>
					<tr><th>*³ Menu Dinámico de productos</th>
						<td>
							<select style="width:320px;" onchange="this.form.submit()" name="FacturaONE_dynamicmenu" id="FacturaONE_dynamicmenu">
								<option <?php if(get_option('FacturaONE_dynamicmenu')==""){echo 'selected';} ?> value="">Desactivado</option>
									<?php
									$locations = get_nav_menu_locations(); //get all menu locations
									foreach($locations as $key => $value) {
										$menu = wp_get_nav_menu_object($locations[$key]);
										if($menu){
											$menuname = $menu->name;
											if ($menuname!=''){
												if(get_option('FacturaONE_dynamicmenu')==$menuname){
													echo '<option selected value="'.$menuname.'">'.$menuname.'</option>';
												}else{
													echo '<option value="'.$menuname.'">'.$menuname.'</option>';
												}
											}
										}
									}
									?>
							</select> 							
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<p>*² Tiene que añadir el Widget F1_Subcategorias desde widgets. Requiere deshabilitar cache de widgets para funcionar correctamente.</a>
							</p>
							<p>*³ Debe crear un menu exclusivo para la ubicación de sus productos a traves de 
								<a href="<?php echo get_site_url().'/wp-admin/nav-menus.php';?>">Apariencia-Menús</a>
							</p>
						</td>
					</tr>
					<tr><th>Pedido mínimo (en euros €)</th>
						<td>
							<input placeholder="Ejemplo: 100€" class="regular-text" type='number' name='FacturaONE_pedidominimo' id='FacturaONE_pedidominimo' value='<?=get_option('FacturaONE_pedidominimo')?>' style="width:320px;">
							<button class="button button-primary">ACEPTAR</button>
						</td>
					</tr>

					<tr><th>Personalizar Texto Agotado:</th>
						<td>
							<input placeholder="Agotado" class="regular-text" type='text' name='FacturaONE_pers_agotado' id='FacturaONE_pers_agotado' value='<?=get_option('FacturaONE_pers_agotado')?>' style="width:320px;">
							<button class="button button-primary">ACEPTAR</button>
						</td>
					</tr>	
		
					<tr><th>Personalizar Texto Reserva:</th>
						<td>
							<input placeholder="Disponible para reserva" class="regular-text" type='text' name='FacturaONE_backorder_message' id='FacturaONE_backorder_message' value='<?=get_option('FacturaONE_backorder_message')?>' style="width:320px;">
							<button class="button button-primary">ACEPTAR</button>
						</td>
					</tr>			
<!--					
					<tr><th>Velocidad de Sincronización</th>
						<td>
							<select style="width:320px;" onchange="this.form.submit()" name="FacturaONE_ajax_cicle" id="FacturaONE_ajax_cicle">
							  <option <?php if (get_option('FacturaONE_ajax_cicle')=='100'){echo 'selected';} ?> value="100">Máximo - Requiere Memoria+Procesador</option>
							  <option <?php if (get_option('FacturaONE_ajax_cicle')=='65'){echo 'selected';} ?> value="65">Medio - Pocos Recursos</option>
							  <option <?php if (get_option('FacturaONE_ajax_cicle')=='20'){echo 'selected';} ?> value="20">Mínimo - Recursos Limitados</option>
							</select> 
						</td>
					</tr>
-->					
				</table>
			</form>
		</div>
	
		<div id="Impuestos" class="tabcontent" style="height:660px;">
			<form method='post'>
				<input type="hidden" name="fact_nonce_action" value="<?php echo wp_create_nonce('fact_nonce_action');?>"/>
				<table class="form-table">
					<tr><th>*¹ Importar precio de articulos con impuestos incluidos</th>
						<td>
							<select style="width:320px;" onchange="this.form.submit()" name="FacturaONE_ivaincluido" id="FacturaONE_ivaincluido">
								<option <?php if(get_option('FacturaONE_ivaincluido')==1){echo 'selected';} ?> value="1">Sí, voy a introducir los precios con impuestos incluidos</option>
								<option <?php if(get_option('FacturaONE_ivaincluido')==0){echo 'selected';} ?> value="0">No, introduciré los precios sin impuestos.</option>
							</select> 
						</td>
					</tr>
					<tr>
						<td colspan="2">
							Vinculo de Impuestos entre el ERP y WooCommerce
						</td>
					</tr>
					<?php 
					//carga taxrates
					$taxrates = array();
					$resultado = FONE_url_get_contents('impuestos');
					if($resultado!=''){
						$response = json_decode(gzuncompress(base64_decode($resultado)), true);
						foreach ($response as $posicionA=>$jugadorA)
						{
							foreach ($jugadorA as $posicionB=>$jugadorB)
							{
								if ($posicionB=='q1'){
								foreach ($jugadorB as $posicionC=>$jugadorC)
								{
								array_push($taxrates,array('tax_rate_id'=>$jugadorC['tax_rate_id'],'tax_rate_name'=>$jugadorC['tax_rate_name']));
								}
								}
							}
						}
					}
					//carga tax_classe_names
					$tax_classe_names = array('standard');
					if ( class_exists( 'WC_Tax' ) ) {
						$tax_classes = WC_Tax::get_tax_rate_classes();
						foreach ($tax_classes as $tax_classe){
							array_push($tax_classe_names,$tax_classe->slug);
						}
					}
					foreach($taxrates as $taxrate){
					?>
						<tr><th>*¹ Impuesto <?php echo $taxrate['tax_rate_name'];?></th>
							<td>
								<select style="width:320px;" onchange="this.form.submit()" name="FacturaONE_taxrate_<?php echo $taxrate['tax_rate_id'];?>" id="FacturaONE_taxrate_<?php echo $taxrate['tax_rate_id'];?>">
								<?php 	echo '<option '.((get_option('FacturaONE_taxrate_'.$taxrate['tax_rate_id'])==-1) ? 'selected' : '').' value = "-1">No Usado</option>';
										foreach($tax_classe_names as $tax_classe_name){
											echo '<option '.((get_option('FacturaONE_taxrate_'.$taxrate['tax_rate_id'])==$tax_classe_name) ? 'selected' : '').' value = "'. $tax_classe_name .'">Tarifa ***'. $tax_classe_name .'*** WooCommerce</option>';
										} ?>
								</select> 
							</td>
						</tr>
					<?php
					}
					?>
					<tr>
						<td colspan="2">
							<p>*¹ Para aplicar estas opciones, debe volver a <a href="#" onClick="execbackground('update_productos.php','start')">Importar Productos</a>
							</p>
						</td>
					</tr>					
				</table>
			</form>
		</div>
		
		<div style="display:none;margin-top:-20px;margin-left:10px;" id="version_num">
			<?php if (get_option('FacturaONE_version')){echo 'v'.get_option('FacturaONE_version');}?>
		</div>
		
		<div id="plug_image" onClick="window.open('https://www.facturaone.com', '_blank');" style="background: url('<?php echo plugins_url( 'assets/plugimage.png', __FILE__ );?>') 0% top/contain no-repeat;min-height:258px;display: flex;justify-content: flex-end;align-items: center;background-size: 405px;display:none;margin-top:10px;"></div>
	</div>
	<script>
		window.addEventListener('load', function () {
			setTimeout("plug_image()", 300); // after 5 secs
		})
		function plug_image() {
		  document.getElementById("plug_image").style.display = "block";
		  document.getElementById("version_num").style.display = "block";
		}
	</script>
	

	
	<div id="progressbox" style="bottom:30px;right:18px;position:fixed;visibility:hidden;background-color:#6AA2D0; padding:6px; border-radius:4px;z-index:1;width:300px;min-height:55px;">
		<div style="color: white;float:left;padding-left:10px;width:50px;" id="txtporcentaje"></div>
		<progress id="progressbar" max="100" value="0" style="width:200px;margin-left:10px;margin-right:30px;top:-1px;position:relative;"></progress>
		<button style="border:none;height: 15px;width: 15px;margin: 1px 5px -10px -18px;position:absolute;background:url(<?php echo plugins_url( 'assets/btncancel.png', __FILE__ );?>) center no-repeat; background-size: 15px 15px; " type="button" onClick="execbackground('ajax.php','stop')" title="Detener"></button>
		<div style="color: white;float:left;padding-left:10px;width:280px;height:38px;overflow:hidden;text-align:left;" id="statustitle"></div>
	</div>
	<!--
		<pre style="white-space: normal;width:100%;font-size:9px;" id="statustext"></pre>
	-->
	<script type="text/javascript">
		$(document).ready(function() {
			var refreshIntervalId = setInterval(function(){compruebafichero();}, 1000*1);
			function compruebafichero(){
				$.ajax({
					type        : 'POST', // define the type of HTTP verb we want to use
					url			: '<?php echo plugins_url('wp-facturaone').'/ajax.php';?>',
					data        : { modo: 'backprocess' }, // our data object
					dataType    : 'json', // what type of data do we expect back.
					//cache		: true,
					timeout		: 0,
					success:function(response){
						var porcentaje = Math.round(response.porcentaje * 10) / 10;
						if (porcentaje>100){porcentaje=100;}
						//document.getElementById('statustext').innerHTML = response.string;
						document.getElementById("txtporcentaje").innerHTML = porcentaje +' %';
						document.getElementById("progressbar").value = response.porcentaje;
						document.getElementById('statustitle').innerHTML = response.status_title;
						if (response.status_backgroundid==0 || response.porcentaje==0){
							//clearInterval(refreshIntervalId);
							//location.reload();
							document.getElementById("progressbox").style.visibility = "hidden";
						}else{
							document.getElementById("progressbox").style.visibility = "visible";
						}
						//console.log("Hello world!");
					}
				});
			}
		});
	</script>
</body>
</html>