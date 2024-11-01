<?php
/*
Plugin Name: Telegram-chat
Plugin URI: http://telegramwordpress.com/
Description: Take all your conversations to your phone by Telegram. Just configure your phone number and ready!
Version: 3.0.4
Author: Totalpat, S.A. de C.V.
Author URI: http://www.totalpat.com
License: Sistema TelegramWordpress.com
Text Domain: telegram-chat
Domain Path: /lang/
  
Copyright 2016 TOTALPAT, S.A. DE C.V.  (email : soporte@totalpat.com)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

//se quitan los errores
error_reporting(0);

//estedisticas de la ip
require_once("lib/browser.lib.php");
//menu administrador
require_once("menu/display.php");
require_once("menu/config.php");
require_once("menu/active.php");
require_once("lib/hash.php");

$GLOBALS['error']="";
$GLOBALS['telegram_colaboradores_verificar']="";
$GLOBALS['telegram_colaboradores']="";
$GLOBALS['telegram_version']="3.0.4";

add_filter( 'wp_enqueue_scripts', 'wpse8170_enqueue_totalpat_telegram_chat', 0 );

function wpse8170_enqueue_totalpat_telegram_chat() {
	
	wp_enqueue_script( 'jquery' );
	wp_register_script('effects-telegram', plugins_url( 'js/jquery.transit.min.js', __FILE__ ) );
	wp_enqueue_script( 'effects-telegram');
	
	wp_register_script('cookie-telegram', plugins_url( 'js/js-cookie-master/src/js.cookie.js', __FILE__ ) );
	wp_enqueue_script('cookie-telegram');
	
	wp_register_script('telegram-chat-js', plugins_url( 'js/telegram-chat.js', __FILE__ ) );
	wp_enqueue_script('telegram-chat-js');
	
	wp_register_script('telegram-function-js', plugins_url( 'js/telegram-functions.js', __FILE__ ) );
	wp_enqueue_script('telegram-function-js');
	
}

add_filter( 'wp_enqueue_scripts', 'wpse8170_enqueue_totalpat_telegram_chat_style', 20 );

function wpse8170_enqueue_totalpat_telegram_chat_style() {
	
	wp_register_style('telegram-chat-styles', plugins_url( 'css/telegram-chat-window.css', __FILE__ ) );
	wp_enqueue_style('telegram-chat-styles');
	
	wp_register_style('telegram-chat-icon', plugins_url( 'font/font-awesome/css/font-awesome.css', __FILE__ ) );
	wp_enqueue_style('telegram-chat-icon');
}

function load_custom_wp_admin_js() {
		
	wp_register_script('telegram-xtra-js', plugins_url( 'js/telegram-xtras.js', __FILE__ ) );
	wp_enqueue_script('telegram-xtra-js');
	
}
add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_js' );



/*
	MULTILANGUAJE
*/
add_action('plugins_loaded', 'wan_load_textdomain');
function wan_load_textdomain() {
	load_plugin_textdomain( 'telegram-chat', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
}

/*
	NOTIFICACIONES   wordpress admin_notices
* /
function sample_admin_notice__success() {
    ?>
    <div class="notice notice-success is-dismissible"> 
        <p><strong>Settings saved.</strong></p>
        <button type="button" class="notice-dismiss" id="cerrarNoticeTelegram">
            <span class="screen-reader-text">Dismiss this notice.</span>
        </button>
        
    </div>
    
    <script language="javascript">
		
		function cerrarNotificacionTelegram(){
			alert('cerrando');
			jQuery('#cerrarNoticeTelegram').click();
		}
			
	</script>
    
    <?php
}
add_action( 'admin_notices', 'sample_admin_notice__success' );
*/


add_action('init', function() {
	
	
    //información de Browser_telegram_chat
    $Browser_telegram_chat=new Browser_telegram_chat();
    $os=$Browser_telegram_chat->getPlatform();
	$navegador=$Browser_telegram_chat->getBrowser_telegram_chat();
	
	/*
	*	información principal del chat
	*/
	global $wpdb;
	$table_info = $wpdb->prefix . 'telegram_chat_info';
	
	//se busca en la tabla
	$results = $wpdb->get_results( "SELECT * FROM ".$table_info." WHERE tipo='colaborador'", ARRAY_A );
	$telegram_colaboradores="";
	
	$telegram_colaboradores_verificar="";
	for($i=0; $i<count($results); $i++){
		
		if($results[$i]['valor2']!="0"){
			$telegram_colaboradores_verificar.=$results[$i]['valor1'].'_|_'.$results[$i]['valor3'].'_|_'.$results[$i]['valor4'].'_$_';
		}
	}
	$GLOBALS['telegram_colaboradores_verificar']=$telegram_colaboradores_verificar;
		
	
	//se busca en la tabla la info principal
	$resultsProfileInfo = $wpdb->get_results( "SELECT * FROM ".$table_info." WHERE tipo='profile_info'", ARRAY_A );
	$resultsProfilePhotos = $wpdb->get_results( "SELECT * FROM ".$table_info." WHERE tipo='profile_photos'", ARRAY_A );
	
	//se pone info si esta vacio
	if($resultsProfileInfo[0]['valor1']==""){
		$resultsProfileInfo[0]['valor1']=__( 'Consultor', 'telegram-chat');
	}
	if($resultsProfileInfo[0]['valor2']==""){
		$resultsProfileInfo[0]['valor2']=__( 'Job', 'telegram-chat');
	}
	if($resultsProfileInfo[0]['valor3']==""){
		$resultsProfileInfo[0]['valor3']=__( 'Department', 'telegram-chat');
	}
	
	
	/*
	*		se pone la cookie
	*/
    if (!isset($_COOKIE['telegram-chat'])) {
		//IP
		$client  = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote  = $_SERVER['REMOTE_ADDR'];
		if(filter_var($client, FILTER_VALIDATE_IP))				$ip = $client;
		elseif(filter_var($forward, FILTER_VALIDATE_IP))		$ip = $forward;
		else													$ip = $remote;
		
		//URL actual
		$url_cookie=home_url();
		
		$upload_dir=wp_upload_dir();
		
		//token unico por dispositivo
		$token=wp_generate_password(20, true, false);
		$tokenpublica=wp_generate_password(50, false, false);
		
		$toCookie = array(array('date'=>date('Y-m-d H:i:s'), 
								'session'=>session_id(), 
								'url'=>$url_cookie, 
								'ip'=>$ip, 
								'token'=>$token, 
								'tokenpublica'=>$tokenpublica,
								'os'=>$os, 
								'navegador'=>$navegador, 
								'plugin'=>plugins_url()."/telegram-chat/",
								'upload'=>$upload_dir['baseurl']."/telegram-chat/",
								
								'cliente_nombre'=>"",
								'cliente_email'=>"",
								'cliente_telefono'=>"",
								
								'colaborador_nombre'=>$resultsProfileInfo[0]['valor1'],
								'colaborador_puesto'=>$resultsProfileInfo[0]['valor2'],
								'colaborador_departamento'=>$resultsProfileInfo[0]['valor3'],
								'colaborador_photo'=>$resultsProfilePhotos[0]['valor1'],
								'colaborador_business'=>$resultsProfilePhotos[0]['valor2'],
								
								'telegram_id'=>"",
								'telegram_line'=>"", 
								'telegram_grupo'=>"",
								'telegram_colaboradores'=>$telegram_colaboradores,
								'telegram_colaboradores_verificar'=>$telegram_colaboradores_verificar	
							)
						);
		
		$json = json_encode($toCookie);
		setcookie("telegram-chat", $json, time() + (86400 * 3000), "/", false, 0);
		$GLOBALS['varGlobal']=$toCookie;
		
    }
	else{
		
		$upload_dir=wp_upload_dir();
		
		//se deshace el cookie
		$cookie_user_array=implode("",explode("\\",$_COOKIE['telegram-chat']));
		$cookie_user_array=stripslashes(trim($cookie_user_array));
		$cookie_user_array=json_decode($cookie_user_array, true);
		
		//si había grupos ya creados, se borran
		if($cookie_user_array[0]['telegram_id']!="" and $cookie_user_array[0]['telegram_line']==""){
			$cookie_user_array[0]['telegram_id']="";
			$cookie_user_array[0]['telegram_line']="";
			$cookie_user_array[0]['tokenpublica']=wp_generate_password(50, false, false);
		}
		else if($cookie_user_array[0]['tokenpublica']==""){
			$cookie_user_array[0]['tokenpublica']=wp_generate_password(50, false, false);
		}
		
		//se actualiza el cookie
		$toCookie = array(array('date'=>$cookie_user_array[0]['date'], 
										'session'=>$cookie_user_array[0]['session'], 
										'url'=>$cookie_user_array[0]['url'], 
										'ip'=>$cookie_user_array[0]['ip'], 
										'token'=>$cookie_user_array[0]['token'], 
										'tokenpublica'=>$cookie_user_array[0]['tokenpublica'],
										'os'=>$cookie_user_array[0]['os'], 
										'navegador'=>$cookie_user_array[0]['navegador'], 
										'plugin'=>$cookie_user_array[0]['plugin'], 
										'upload'=>$upload_dir['baseurl']."/telegram-chat/",
										
										'cliente_nombre'=>$cookie_user_array[0]['nombre'], 
										'cliente_email'=>$cookie_user_array[0]['email'], 
										'cliente_telefono'=>$cookie_user_array[0]['telefono'], 
										
										
										'colaborador_nombre'=>$resultsProfileInfo[0]['valor1'],
										'colaborador_puesto'=>$resultsProfileInfo[0]['valor2'],
										'colaborador_departamento'=>$resultsProfileInfo[0]['valor3'],
										'colaborador_photo'=>$resultsProfilePhotos[0]['valor1'],
										'colaborador_business'=>$resultsProfilePhotos[0]['valor2'],
										
										
										'telegram_id'=>$cookie_user_array[0]['telegram_id'], 
										'telegram_line'=>$cookie_user_array[0]['telegram_line'], 
										'telegram_grupo'=>$cookie_user_array[0]['telegram_grupo'], 
										'telegram_colaboradores'=>$telegram_colaboradores,
										'telegram_colaboradores_verificar'=>$telegram_colaboradores_verificar
						));
						
		//se guarda la cookie nuevamente
		$json = json_encode($toCookie);
		setcookie("telegram-chat", $json, time() + (86400 * 3000), "/", false, 0);
		$GLOBALS['varGlobal']=$toCookie;
	}
});


function setChatTelegramDiv() {
	
	$banderaDisplayTelegramDiv=true;
	global $wpdb;
	$table_name = $wpdb->prefix . 'telegram_chat_info';
	$resultsInfo = $wpdb->get_results("SELECT * FROM ".$table_name." WHERE tipo='profile_info'", ARRAY_A );
	$resultsConfig = $wpdb->get_results("SELECT * FROM ".$table_name." WHERE tipo='profile_config'", ARRAY_A );
	
	if(wp_is_mobile()==true and $resultsInfo[0]['valor4']=="0"){
		$banderaDisplayTelegramDiv=false;
	}
	
	if($banderaDisplayTelegramDiv){
			
		//se deshace el cookie
		$cookie_user_array=implode("",explode("\\",$_COOKIE['telegram-chat']));
		$cookie_user_array=stripslashes(trim($cookie_user_array));
		$cookie_user_array=json_decode($cookie_user_array, true);
		
		echo '<div id="chatTelegramTotalpat" class="defaulttable">
					
					<div id="tituloTelegramChat" onclick="openChatTelegram();" style="background-color:#1c6899; border-color:#1c6899;" >
						<table width="100%" border="0" cellspacing="0" cellpadding="0" class="defaulttable simple" id="tablaTituloTelegram" style="background-color:#1c6899; border-color:#1c6899;" >
						  <tr >
							<td><i class="fa fa-comment" style="font-size:20px;"></i></td>
							<td align="left" style="text-align:left; font-size:15px; letter-spacing: 0px; font-family:\'Lucida Sans Unicode\', \'Lucida Grande\', sans-serif;" >'. __( 'Contact us', 'telegram-chat'). '</td>
							<td><i class="fa fa-minus"></i></td>
						  </tr>
						</table>
					</div>
					<div id="mensajeTelegramChat">
						<img src="'.plugins_url( 'images/mensaje_telegram.png', __FILE__ ).'" >
					</div>
					<div id="loadingTelegramChat" style="height:0px; ">
						<br><br><img src="'.plugins_url( 'images/svg-loaders/puff.svg', __FILE__ ).'" width="100px" />
						<br><font style="color:#FFFFFF; font-size:16px;" id="menssageLoadingTelegramChat">'. __( 'Connecting...', 'telegram-chat'). '</font>
					</div>
					<div id="contenidoTelegramChat" style="z-index:999999; visibility:hidden; border-top:solid 5px #d4e6f1;" ></div>
					<div id="footerTelegramChat" onclick="openTelegramChatPowered();" style="background-color:#1c6899; border-color:#1c6899;">'. __( 'created by', 'telegram-chat'). ' TelegramWordpress.com</div>
				</div>
				<input type="button" id="botonMaestroWA" style="display:none;" >
				<input type="button" id="botonNuevoChatWA" style="display:none;" >
				<input type="button" id="botonEstatusChatWA" style="display:none;" >
				<input type="button" id="botonTypingChatWA" style="display:none;" >
				<input type="button" id="botonGetHistoryChatWA" style="display:none;" >
				<input type="hidden" id="textoEstatusChatWA" value="" >
				<input type="hidden" id="textoGetHistoryPageChatWA" value="0" >
				<input type="hidden" id="mensajeInicioTelegramChat" value="'.addslashes($resultsConfig[0]['valor2']).'" >
				
			
			<script language="javascript">
				//telegram
				var telegram_session="'.session_id().'";
				var telegram_line="'.$cookie_user_array[0]['telegram_line'].'";
				var telegram_id="'.addslashes($cookie_user_array[0]['telegram_id']).'";
				var telegram_grupo="'.addslashes($cookie_user_array[0]['telegram_grupo']).'";
				var telegram_colaboradores="'.encrypt($GLOBALS['telegram_colaboradores'], $GLOBALS['varGlobal'][0]['token']).'";
				var telegram_colaboradores_verificar="'.encrypt($GLOBALS['telegram_colaboradores_verificar'], $GLOBALS['varGlobal'][0]['token']).'";
				
				
				//cliente
				var url="'.addslashes($GLOBALS['varGlobal'][0]['url']).'";
				var token="'.addslashes($GLOBALS['varGlobal'][0]['token']).'";
				var tokenpublica="'.addslashes($GLOBALS['varGlobal'][0]['tokenpublica']).'";
				var os="'.addslashes($GLOBALS['varGlobal'][0]['os']).'";
				var ip="'.addslashes($GLOBALS['varGlobal'][0]['ip']).'";
				var navegador="'.addslashes($GLOBALS['varGlobal'][0]['navegador']).'";
				var cliente_nombre="'.addslashes($GLOBALS['varGlobal'][0]['cliente_nombre']).'";
				var cliente_email="'.addslashes($GLOBALS['varGlobal'][0]['cliente_email']).'";
				var cliente_telefono="'.addslashes($GLOBALS['varGlobal'][0]['cliente_telefono']).'";
				
				//mensajes
				var mensajeAbrirVentana="'. __( 'The user opens the chat on the website', 'telegram-chat'). '";
				var mensajeMaximizarVentana="'. __( 'The user maximizes the window', 'telegram-chat'). '";
				var mensajeMinimizarVentana="'. __( 'The user minimizes the window', 'telegram-chat'). '";
				var mensajeAvandonarVentanaTelgram="'. __( 'The user leaves the website', 'telegram-chat'). '";
				var mensajeAlertaNombreCorreoVentana="'. __( 'The name, email and comments are required', 'telegram-chat'). '";
				var mensajeErrorUsuariosVentana="'. __( '<strong>There are no users,</strong><br>please configure the plugin', 'telegram-chat'). '";
				var mensajeErrorExpriacionVentana="'. __( '<strong>The license has expired,</strong><br>please go to the website <a href=\'http://telegramwordpress.com\' target=\'_blank\'>http://telegramwordpress.com</a>', 'telegram-chat'). '";
				
				//ventana registro
				var mensajeBienvenidaVentana="'. __( 'How I can help today', 'telegram-chat'). '"
				var mensajeEscribeComentariosVentana="'. __( 'Write your comments here', 'telegram-chat'). '"
				var mensajeNombreVentana="'. __( 'Name', 'telegram-chat'). '"
				var mensajeTelefonoVentana="'. __( 'Telephone', 'telegram-chat'). '"
				var mensajeOpcionalVentana="'. __( 'optional', 'telegram-chat'). '"
				var mensajeEnviarVentana="'. __( 'Send', 'telegram-chat'). '";
				
				//ventana chat
				var mensajeMensajeChatVentana="'. __( 'Write here', 'telegram-chat'). '";
				var mensajeEscribiendoVentana="'. __( 'The client is writing...', 'telegram-chat'). '";
				var mensajeMensajeAsesorVentana="'. __( 'Consultor', 'telegram-chat'). '";
				var mensajeMensajeClienteVentana="'. __( 'Client', 'telegram-chat'). '";
				var mensajeMensajeEscribiendoVentana="'. __( 'Writing...', 'telegram-chat'). '";
				var mensajeMensajeCargandoVentana="'. __( 'Loading...', 'telegram-chat'). '";
				var mensajeMensajeCargandoImagenVentana="<img src=\''.plugins_url( 'images/svg-loaders/puff.svg', __FILE__ ).'\' width=\'50px\' />";
				var mensajeErrorTelegramEmpresarial="'. __( 'The plugin must be updated', 'telegram-chat'). '";
				';
				
				if($GLOBALS['telegram_colaboradores_verificar']==""){
					echo '
					ponerLoading();
					jQuery(\'#menssageLoadingTelegramChat\').html(mensajeErrorUsuariosVentana);';
					
				}
				
				echo '
				//audio en el chat
				audioElementTelegramChat = document.createElement("audio");
				audioElementTelegramChat.setAttribute("src", "'.plugins_url( 'images/hint_fG0z4yBS.mp3', __FILE__ ).'");
				
				';
				
				if(session_id()==$cookie_user_array[0]['session']){
					if($resultsConfig[0]['valor1']==1){
						echo 'setTimeout(function(){ openChatTelegram();},2000);';
					}
				}
				
		echo '
				//online
				//setTimeAvisoTelegramEmpresarial=setTimeout(function(){ mostrarAvisoTelegramEmpresarial(); }, 5000);
			</script>';
	}
}
add_action( 'wp_footer', 'setChatTelegramDiv' );


/*
*	CREACION DE LA BASE DE DATOS
*/
register_activation_hook( __FILE__, 'telegram_chat_install' );
function telegram_chat_install() {
	
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	$telegramChat_db_version = '1.1';
	global $wpdb;
	
	//tabla credenciales Telegram
	$table_info = $wpdb->prefix . 'telegram_chat_info';
	$table_history = $wpdb->prefix . 'telegram_chat_history';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_info (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		tipo varchar(200) NOT NULL,
		valor1 varchar(200) NOT NULL,
		valor2 varchar(200) NOT NULL,
		valor3 varchar(200) NOT NULL,
		valor4 varchar(200) NOT NULL,
		valor5 varchar(200) NOT NULL,
		valor6 varchar(200) NOT NULL,
		valor7 varchar(200) NOT NULL,
		valor9 varchar(200) NOT NULL,
		valor10 varchar(200) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";
	dbDelta( $sql );
	
	$sql = "CREATE TABLE $table_history (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		telegram_chat_info_id mediumint(9) NOT NULL,
		mensaje varchar(500) NOT NULL,
		colaborador_nombre varchar(200) NOT NULL,
		mensaje_in mediumint(9) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";
	dbDelta( $sql );
			
	add_option( 'telegramChat_db_version', $telegramChat_db_version );
}


$_POST_telegramEmpresarial = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);


/*
*	JQUERY
*/

function telegramChat_save_function() {
	
	if(isset($_POST['ssid_telegramChat_673']) && $_POST['ssid_telegramChat_673'] == "statusUserTelegram"){
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'telegram_chat_info';
	
		$wpdb->update($table_name, 
			array( 
				'valor2' => $_POST['dato1']
			), 
			array( 'id' => $_POST['dato2'] )
		);
		
		echo json_encode(array('success' => true, 'module'=>"statusUserTelegram"));
		exit;
	}
	else if(isset($_POST['ssid_telegramChat_673']) && $_POST['ssid_telegramChat_673'] == "statusTokenTelegram"){
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'telegram_chat_info';
	
		$wpdb->update($table_name, 
			array( 
				'valor1' => $_POST['dato1'],
				'valor3' => $_POST['dato2']
			), 
			array( 'id' => $_POST['dato3'] )
		);
	
		echo json_encode(array('success' => true, 'module'=>"statusTokenTelegram"));
		exit;
	
	}
	else{
		echo json_encode(array('fail' => $_POST));
		exit;
	}
	
}
add_action('wp_ajax_telegramChat_iajax_save', 'telegramChat_save_function');

/*
* FUNCIONES DEL WP
*/

if(isset($_POST_telegramEmpresarial['agregarNombreColaborador']) and $_POST_telegramEmpresarial['agregarNombreColaborador']!=""){
	
	$_POST_telegramEmpresarial['agregarNombreColaborador']=trim($_POST_telegramEmpresarial['agregarNombreColaborador']);
	
	if($_POST_telegramEmpresarial['agregarNombreColaborador']!="" and $_POST_telegramEmpresarial['agregarTelefonoColaborador']!=""){
		
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'telegram_chat_info';
		$wpdb->insert($table_name, 
			array( 
				'tipo' => 'colaborador', 
				'valor1' => $_POST_telegramEmpresarial['agregarNombreColaborador'],
				'valor2' => 2,
				'valor3' => $_POST_telegramEmpresarial['guardar_cliente_lada'].$_POST_telegramEmpresarial['agregarTelefonoColaborador'],
				'valor4' => $_POST_telegramEmpresarial['agregarCorreoColaborador']
			)
		);
	}
	else{
		$GLOBALS['error']="El nombre y celular son obligatorios";
	}
}
else if(isset($_POST_telegramEmpresarial['eliminarColaborador']) and is_numeric($_POST_telegramEmpresarial['eliminarColaborador']) and $_POST_telegramEmpresarial['eliminarColaborador']!=""){
	global $wpdb;
	$table_name = $wpdb->prefix . 'telegram_chat_info';
	$wpdb->delete( $table_name , array( 'id' =>  $_POST_telegramEmpresarial['eliminarColaborador'] ) );
}
else if(isset($_POST_telegramEmpresarial['profile_info_id']) and is_numeric($_POST_telegramEmpresarial['profile_info_id']) and $_POST_telegramEmpresarial['profile_info_id']!=""){
	
	global $wpdb;
	$table_name = $wpdb->prefix . 'telegram_chat_info';

	//se agrega
	if($_POST_telegramEmpresarial['profile_info_id']=="0"){
		
		$wpdb->insert($table_name, 
			array( 
				'tipo' => 'profile_info', 
				'valor1' => $_POST_telegramEmpresarial['profile_nombre'],
				'valor2' => $_POST_telegramEmpresarial['profile_puesto'],
				'valor3' => $_POST_telegramEmpresarial['profile_departamento'],
				'valor4' => $_POST_telegramEmpresarial['profile_movile']
			)
		);
	}
	//se edita
	else{
		$wpdb->update($table_name, 
			array( 
				'valor1' => $_POST_telegramEmpresarial['profile_nombre'],
				'valor2' => $_POST_telegramEmpresarial['profile_puesto'],
				'valor3' => $_POST_telegramEmpresarial['profile_departamento'],
				'valor4' => $_POST_telegramEmpresarial['profile_movile']
			), 
			array( 'id' => $_POST_telegramEmpresarial['profile_info_id'] )
		);
	}
	
	//se agrega
	if($_POST_telegramEmpresarial['profile_config_id']=="0"){
		
		$wpdb->insert($table_name, 
			array( 
				'tipo' => 'profile_config', 
				'valor1' => $_POST_telegramEmpresarial['profile_openChat'],
				'valor2' => $_POST_telegramEmpresarial['profile_autoMessage']
			)
		);
		
	}
	//se edita
	else{
		$wpdb->update($table_name, 
			array( 
				'valor1' => $_POST_telegramEmpresarial['profile_openChat'],
				'valor2' => $_POST_telegramEmpresarial['profile_autoMessage']
			), 
			array( 'id' => $_POST_telegramEmpresarial['profile_config_id'] )
		);
	}
	
	
}
else if(isset($_POST_telegramEmpresarial['profile_photo_id']) and is_numeric($_POST_telegramEmpresarial['profile_photo_id']) and $_POST_telegramEmpresarial['profile_photo_id']!=""){
	
	$upload_dir = wp_upload_dir();
	
	//se hace la carpeta
	$target_dir = $upload_dir['basedir'].'/telegram-chat/';
	mkdir($target_dir);
	chmod($target_dir, 0777);
	
	//el id de la photo
	$photo_id=0;
	
	//estancias para la base de datos
	global $wpdb;
	$table_name = $wpdb->prefix . 'telegram_chat_info';
	
	if($_FILES['fileFotoPerfil']['name']!=""){
		
		$target_file = $target_dir . basename($_FILES["fileFotoPerfil"]["name"]);
		$uploadOk = 1;
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));	
		
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
			$GLOBALS['error']="El archivo debe de ser imagen.";
			$uploadOk = 0;
		}
		
		//5megas maximo
		if ($_FILES["fileFotoPerfil"]["size"] > 5000000) {
			$GLOBALS['error']="El archivo debe de ser menor a 5MB";
			$uploadOk = 0;
		}
		
		if($uploadOk==1){
			if (move_uploaded_file($_FILES["fileFotoPerfil"]["tmp_name"], $target_file)) {
				
				if($_POST_telegramEmpresarial['profile_photo_id']=="0"){
					$wpdb->insert($table_name, 
						array( 
							'tipo' => 'profile_photos', 
							'valor1' => $_FILES["fileFotoPerfil"]["name"]
						)
					);
					$_POST_telegramEmpresarial['profile_photo_id']=$wpdb->insert_id;
				}
				else{
					$wpdb->update($table_name, 
						array( 
							'valor1' => $_FILES["fileFotoPerfil"]["name"]
						), 
						array( 'id' => $_POST_telegramEmpresarial['profile_photo_id'] )
					);
				}
				
			} else {
				$GLOBALS['error']="Error al subir la foto";
			}
		}
	}
	
	if($_FILES['fileLogoEmpresa']['name']!=""){
		
		$target_file = $target_dir . basename($_FILES["fileLogoEmpresa"]["name"]);
		$uploadOk = 1;
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));	
		
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
			$GLOBALS['error']="El archivo debe de ser imagen.";
			$uploadOk = 0;
		}
		
		//5megas maximo
		if ($_FILES["fileLogoEmpresa"]["size"] > 5000000) {
			$GLOBALS['error']="El archivo debe de ser menor a 5MB";
			$uploadOk = 0;
		}
		
		if($uploadOk==1){
			if (move_uploaded_file($_FILES["fileLogoEmpresa"]["tmp_name"], $target_file)) {
				
				if($_POST_telegramEmpresarial['profile_photo_id']=="0"){
					$wpdb->insert($table_name, 
						array( 
							'tipo' => 'profile_photos', 
							'valor2' => $_FILES["fileLogoEmpresa"]["name"]
						)
					);
					$photo_id=$wpdb->insert_id;
				}
				else{
					$wpdb->update($table_name, 
						array( 
							'valor2' => $_FILES["fileLogoEmpresa"]["name"]
						), 
						array( 'id' => $_POST_telegramEmpresarial['profile_photo_id'] )
					);
					$photo_id=$_POST_telegramEmpresarial['profile_photo_id'];
				}
			} else {
				$GLOBALS['error']="Error al subir la foto";
			}
		}
	}
}
else if(isset($_POST_telegramEmpresarial['tokenTelegramChatWP'])){
	unset($datosToken);
	$datosToken['token']=$_POST_telegramEmpresarial['tokenTelegramChatWP'];
	$datosToken['url']=$_SERVER['SERVER_NAME'];
	//$respuesta=serverCommunication('activarTokenServerTelegram', $datosToken);
	
	$arr_respuesta=explode('_', $respuesta);
	
	//se elima de la base
	global $wpdb;
	$table_name = $wpdb->prefix . 'telegram_chat_info';
	$wpdb->delete( $table_name , array( 'tipo' =>  'active_token' ) );
	
	//se guarda en la base de datos
	$wpdb->insert($table_name, 
		array( 
			'tipo' => 'active_token', 
			'valor1' => 4,
			'valor2' => $_POST_telegramEmpresarial['tokenTelegramChatWP'],
			'valor3' => ""
		)
	);
}
else if(isset($_POST_telegramEmpresarial['eliminarTokenTelegramChatWP']) and is_numeric($_POST_telegramEmpresarial['eliminarTokenTelegramChatWP'])){
	//se elima de la base
	global $wpdb;
	$table_name = $wpdb->prefix . 'telegram_chat_info';
	$wpdb->delete( $table_name , array( 'tipo' =>  'active_token' ) );
}

else if(isset($_POST['ssid']) and $_POST['ssid']=="statusUserTelegram" and 0){
		
	
}

else if(isset($_POST['ssid']) and $_POST['ssid']=="statusTokenTelegram" and 0){
		
	
}


/*
* CREACION DEL MENU
*/
function telegram_chat_plugin_setup_menu() {
	add_theme_page(
		'Telegram Chat Plugin Page', 					// The title to be displayed in the browser window for this page.
		'Telegram Chat',					// The text to be displayed for this menu item
		'administrator',					// Which type of users can see this menu item
		'telegram-chat-plugin',			// The unique ID - that is, the slug - for this menu item
		'telegram_chat_plugin_display'				// The name of the function to call when rendering this menu's page
	);
	
	add_menu_page(
		'Telegram Chat Plugin Page',					// The value used to populate the browser's title bar when the menu page is active
		'Telegram Chat',					// The text of the menu in the administrator's sidebar
		'administrator',					// What roles are able to access the menu
		'telegram-chat-plugin',				// The ID used to bind submenu items to this menu 
		'telegram_chat_plugin_display',				// The callback function used to render this menu
		plugin_dir_url( __FILE__ ).'telegram.ico',
		80
	);
	
} // end sandbox_example_theme_menu
add_action( 'admin_menu', 'telegram_chat_plugin_setup_menu' );


/*telegram_chat_theme_options
* CREACION DEL FORMULARIO WP
sandbox_theme_display
*/

function telegram_chat_plugin_display() {
?>
    <!-- Create a header in the default WordPress 'wrap' container -->
    <div class="wrap">
     
        <div id="icon-themes" class="icon32"></div>
        <h2>Telegram Chat</h2>
        <?php settings_errors(); ?>
         
         <?php
            if( isset( $_GET[ 'tab' ] ) ) {
                $active_tab = $_GET[ 'tab' ];
            } // end if
        ?>
        
        <h2 class="nav-tab-wrapper">
            <a href="?page=telegram-chat-plugin&tab=primer_paso_options" class="nav-tab <?php if($active_tab == 'primer_paso_options' or $active_tab == ''){ print('nav-tab-active'); } ?>"><?php print(__( 'Welcome', 'telegram-chat' )); ?></a>
            <a href="?page=telegram-chat-plugin&tab=configuracion_options" class="nav-tab <?php echo $active_tab == 'configuracion_options' ? 'nav-tab-active' : ''; ?>"><?php print(__( 'Settings', 'telegram-chat' )); ?></a>
            <a href="?page=telegram-chat-plugin&tab=active_options" class="nav-tab <?php echo $active_tab == 'active_options' ? 'nav-tab-active' : ''; ?>"><?php print(__( 'Activate', 'telegram-chat' )); ?></a>
        </h2>
        
 
            <?php 
				if( $active_tab == 'configuracion_options' ) {
					settings_fields( 'telegram_chat_plugin_configuracion_options' );
					do_settings_sections( 'telegram_chat_plugin_configuracion_options' );
				} 
				else if( $active_tab == 'active_options' ) {
					settings_fields( 'telegram_chat_plugin_active_options' );
					do_settings_sections( 'telegram_chat_plugin_active_options' );
				}
				else {
					settings_fields( 'telegram_chat_plugin_display_options' );
					do_settings_sections( 'telegram_chat_plugin_display_options' );
				} // end if/else
				 ?>
             
         
    </div><!-- /.wrap -->
<?php
} 
?>