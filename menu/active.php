<?php

/**
 * Initializes the theme's social options by registering the Sections,
 * Fields, and Settings.
 *
 * This function is registered with the 'admin_init' hook.
 */ 
function sandbox_theme_initialize_activate_options() {
	
	add_settings_section(
		'activate_settings_section',			// ID used to identify this section and with which to register options
		__( 'Telegram-Chat Activate', 'telegram-chat' ),		// Title to be displayed on the administration page
		'sandbox_social_activate_callback',	// Callback used to render the description of the section
		'telegram_chat_plugin_active_options'		// Page on which to add this section of options
	);
	
} // end sandbox_theme_initialize_social_options
add_action( 'admin_init', 'sandbox_theme_initialize_activate_options' );


function sandbox_social_activate_callback() {
	echo '<p>' . __( 'From the date of download and activation, you will have three days as a demo to test the operation of the plugin, from the third day the plugin will no longer connect. <br>To obtain an activation code please consult the following link:<br><a href="http://telegramwordpress.com" target="_blank">http://telegramwordpress.com</a>', 'telegram-chat' ) . '</p>';
	
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	//nombre de la tabla
	global $wpdb;
	$table_name = $wpdb->prefix . 'telegram_chat_info';
	
	//se busca en la tabla
	$resultsToken = $wpdb->get_results("SELECT * FROM ".$table_name." WHERE tipo='active_token'", ARRAY_A );
	
	
	if($resultsToken[0]['valor1']!=1 and $resultsToken[0]['valor1']!=4){
		echo '<center>
				<table width="100%" border="0">
				  <tr>
					<td colspan="3" align="center">
					<span style="color:#f44336; font-size:17px;">';
					
					if(count($resultsToken)==0){
						
					}
					else if($resultsToken[0]['valor1']==0){
						echo __( 'Activation code incorrect', 'telegram-chat');
					}
					else if($resultsToken[0]['valor1']==2){
						echo __( 'You can not activate more websites with this token', 'telegram-chat');
					}
					else if($resultsToken[0]['valor1']==3){
						echo __( 'Activation code has expired', 'telegram-chat');
					}
					
					
				echo '</span>
					</td>
				  </tr>
				  <tr>
					<td colspan="3" align="center"><br>
					
					<form action="'.admin_url().'?page=telegram-chat-plugin&tab=active_options" method="post">
						
						<input type="text" name="tokenTelegramChatWP" placeholder="'. __( 'Write the token here...', 'telegram-chat'). '" value="" style="width:400px;" />
						
						<input type="submit" value="'. __( 'Activate', 'telegram-chat'). '">
						
					</form>
					</td>
				  </tr>
				</table>
			</center>';
	}
	else{
		
		echo '<center>
				<table width="100%" border="0">
				  
				  <tr>
					<td colspan="3" align="center"><span style="color:#f44336; font-size:17px;" class="tablaTokenChatTelegramAlertas">'.$GLOBALS['error'];
					
					if($resultsToken[0]['valor1']==4){
						echo __( 'Reviewing activation, wait a moment ...', 'telegram-chat');
						$style="display:none;";
						
						$http_prefix="";
						if(strpos($_SERVER['SERVER_NAME'], 'http')===false){
							$http_prefix="http://";
						}
						
						echo '<script language="javascript">
								conectarWebSocketXtras("activarTokenServerTelegram", "'.$http_prefix.$_SERVER['SERVER_NAME'].'", "'.$resultsToken[0]['valor2'].'", "'.$resultsToken[0]['id'].'");
							</script>';
						
					}
					
					echo '</span></td>
				  </tr>
				  <tr>
					<td colspan="3" align="center" style="display:none;" id="tablaIngresarTockenNuevo"><br>
					
					<form action="'.admin_url().'?page=telegram-chat-plugin&tab=active_options" method="post">
						
						<input type="text" name="tokenTelegramChatWP" placeholder="'. __( 'Write the token here...', 'telegram-chat'). '" value="" style="width:400px;" />
						
						<input type="submit" value="'. __( 'Activate', 'telegram-chat'). '">
						
					</form>
					</td>
				  </tr>
				  <tr>
					<td colspan="3" align="center" style="'.$style.'" class="tablaTokenChatTelegramCorrecto"><br>
					
						<h1 style="color:#5b9563;">'. __( 'Active token', 'telegram-chat'). '<br>XXXXXXXXXXXXXXX'.substr($resultsToken[0]['valor2'],25).'<br><small><small><small>'. __( 'Expiration day', 'telegram-chat'). ' '.$resultsToken[0]['valor3'].'</small></small></small></h1>
						
					<form action="'.admin_url().'?page=telegram-chat-plugin&tab=active_options" method="post">
						<input type="hidden" value="1" id="eliminarTokenTelegramChatWP" name="eliminarTokenTelegramChatWP">
						<input type="submit" value="'. __( 'Delete', 'telegram-chat'). '">
						
					</form>
					</td>
				  </tr>
				  
				</table>
			</center>';
		
	}
	
	$post_url = admin_url('admin-ajax.php'); #In case we're on post-new.php
	
}

?>