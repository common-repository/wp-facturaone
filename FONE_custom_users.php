<?php
//campo tarifa en usuarios
add_action('show_user_profile', 'FONE_show_user_tarifa');
add_action('edit_user_profile', 'FONE_show_user_tarifa');
function FONE_show_user_tarifa($user) {
	global $post;
?>
	<h3 style="margin-top:50px;">ERP FacturaONE</h3>
	<table class="form-table" style="margin-bottom:50px;">
	<tbody>
	<tr><th>FacturaOne TARIFA</th>
	<td>
	    <input class="regular-text" name="tarifa_user" type="text" id="tarifa_user" value="<?php echo $user->tarifa_user;?>">
	</td>
	<tr><th>FacturaOne CLIENTID</th>
	<td>
	    <input placeholder="numero ID del cliente dentro de FacturaOne" class="regular-text" name="fone_client_id" type="text" id="fone_client_id" value="<?php echo $user->fone_client_id;?>">
	</td>
	</tr>
	</tbody>
	</table>
<?php 
}
add_action('personal_options_update', 'FONE_update_tarifa_user');
add_action('edit_user_profile_update', 'FONE_update_tarifa_user');
function FONE_update_tarifa_user($user_id) {
  update_user_meta($user_id, 'tarifa_user', sanitize_text_field($_POST['tarifa_user']));
  update_user_meta($user_id, 'fone_client_id', sanitize_text_field($_POST['fone_client_id']));
}
//añade columna tarifa en usuarios
function FONE_modify_user_table( $column ) {
    $column['tarifa_user'] = 'Tarifa';
    $column['fone_client_id'] = 'ID FacturaOne';
	$column['vat_number'] = 'CIF/NIF';
    return $column;
}
add_filter( 'manage_users_columns', 'FONE_modify_user_table' );
function FONE_modify_user_table_row( $val, $column_name, $user_id ) {
    switch ($column_name) {
        case 'tarifa_user' :
            return get_the_author_meta( 'tarifa_user', $user_id );
        case 'fone_client_id' :
            return get_the_author_meta( 'fone_client_id', $user_id );
		case 'vat_number' :
            $vat_number = get_the_author_meta( 'vat_number', $user_id );
            if ($vat_number==''){ $vat_number = get_user_meta( $user_id, 'vat_number', true ); }
            //if ($vat_number==''){ $vat_number = get_post_meta( $order->id, '_vat_number', true )}
			return $vat_number;
        default:
    }
    return $val;
}
add_filter( 'manage_users_custom_column', 'FONE_modify_user_table_row', 10, 3 );






/**  Show VAT Number in WooCommerce Checkout  */
function FONE_claserama_rearrange_checkout_fields($fields){
	$fields['billing']['billing_company']['class'][0] = 'form-row-first';
	$fields['billing']['billing_company']['label'] = __( 'Nombre empresa' );
	//if( get_option('FacturaONE_sincif')==1 ) {
		$fields['billing']['billing_company']['required'] = FALSE;	
	//}else{
	//	$fields['billing']['billing_company']['required'] = TRUE;	
	//}
    $fields['billing']['vat_number']['priority'] = $fields['billing']['billing_company']['priority'] + 1;
	$fields['billing']['vat_number']['class'][0] = 'form-row-last';
	$fields['billing']['vat_number']['placeholder'] = __( 'Introduce tu CIF o DNI' );
	$fields['billing']['vat_number']['label'] = __( 'NIF/CIF/NIE' );
	if( get_option('FacturaONE_sincif')==1 ) {
		$fields['billing']['vat_number']['required'] = FALSE;	
	}else{
		$fields['billing']['vat_number']['required'] = TRUE;	
	}
    return $fields;
}
add_filter('woocommerce_checkout_fields','FONE_claserama_rearrange_checkout_fields');
//function FONE_woocommerce_vat_field( $checkout ) {
//	echo '<div id="woocommerce_vat_field"><h2>' . __('CIF / DNI') . '</h2>';
//	woocommerce_form_field( 'vat_number', array(
//		  'type' => 'text',     
//		  'class' => array( 'vat-number-field form-row-wide') ,
//		  'label' => __( 'CIF / DNI' ),
//		  'placeholder' => __( 'Introduce tu CIF o DNI' ), ), 
//		  $checkout->get_value( 'vat_number' )); 
//	echo '</div>';
//}
//add_action( 'woocommerce_after_order_notes','FONE_woocommerce_vat_field');

/** Save VAT Number in the order meta */
function FONE_woocommerce_checkout_vat_number_update_order_meta( $order_id ) {
  if ( ! empty( $_POST['vat_number'] ) ) {
	  if ( is_user_logged_in() ) {
		 $user_id = get_current_user_id();
		 update_user_meta( $user_id, 'vat_number', sanitize_text_field( $_POST['vat_number'] ) );
	  }else{
		 update_post_meta( $order_id, '_vat_number', sanitize_text_field( $_POST['vat_number'] ) );
	  }
  }
}
add_action( 'woocommerce_checkout_update_order_meta', 'FONE_woocommerce_checkout_vat_number_update_order_meta' ); 
//check field order
add_action('woocommerce_checkout_process', 'FONE_checkout_field_process');
function FONE_checkout_field_process() {
	if( isset( $_POST['vat_number']) ) {
		if($_POST['vat_number']!=''){
			$billing_country = isset($_POST['billing_country']) ? sanitize_text_field($_POST['billing_country']) : '';
			if($billing_country=='ES'){
				// Check if set, if its not set add an error.
				$vat_number = $_POST['vat_number'];
				$resultado = FONE_validDniCifNie($vat_number);
				if ( ! $vat_number || $resultado==FALSE )
					wc_add_notice( __( 'El NIF/CIF/NIE <b>'.$vat_number.'</b> no es correcto' ), 'error' );
			}
		}
	}
}
/** Display VAT Number in order edit screen */
function FONE_woocommerce_vat_number_display_admin_order_meta( $order ) {
    $post = $order->get_id();
	$vatnumber = get_post_meta( $post, '_vat_number', true );
    if ( $vatnumber == '' ) {
        $user_id = get_post_meta( $post, '_customer_user', true ); // Obtén el user ID
        if ( $user_id ) {
            $vatnumber = get_user_meta( $user_id, 'vat_number', true );
        }
    }
    //$user_id = get_current_user_id();
	//echo '<strong>' . __( 'CIF', 'woocommerce' ) . ':</strong> ' . get_user_meta( $user_id, 'vat_number', true );
	echo '<strong>' . __( 'VAT Number', 'woocommerce' ) . ':</strong> ' . $vatnumber;
}
add_action( 'woocommerce_admin_order_data_after_billing_address', 'FONE_woocommerce_vat_number_display_admin_order_meta', 10, 1 );







// Add the custom field "vatnumber"
add_action( 'woocommerce_edit_account_form', 'FONE_vatnumber' );
function FONE_vatnumber() {
    $user = wp_get_current_user();
    ?>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="vat_number"><?php _e( 'Numero Fiscal NIF/CIF/NIE', 'woocommerce' ); ?></label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="vat_number" id="vat_number" value="<?php echo esc_attr( $user->vat_number ); ?>" />
    </p>
    <?php
}
// Save the custom field 'vatnumber' 
add_action( 'woocommerce_save_account_details', 'FONE_save_vatnumber', 12, 1 );
function FONE_save_vatnumber( $user_id ) {
    if( isset( $_POST['vat_number'] ) )
        update_user_meta( $user_id, 'vat_number', sanitize_text_field( $_POST['vat_number'] ) );
}
?>