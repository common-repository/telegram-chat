<?php

/**
 * Initializes the theme's display options page by registering the Sections,
 * Fields, and Settings.
 *
 * This function is registered with the 'admin_init' hook.
 */ 
function telegram_chat_theme_options() {
	/*
		BIENVENIDA
	*/
	
	// First, we register a section. This is necessary since all future options must belong to a 
	add_settings_section(
		'general_settings_section',			// ID used to identify this section and with which to register options
		__( 'Telegram chat', 'telegram-chat' ),		// Title to be displayed on the administration page
		'telegram_chat_information_callback',	// Callback used to render the description of the section
		'telegram_chat_plugin_display_options'		// Page on which to add this section of options
	);
	
	// Next, we'll introduce the fields for toggling the visibility of content elements.
	add_settings_field(	
		'show_header',						// ID used to identify the field throughout the theme
		__( 'How the Telegram-chat plugin work?', 'telegram-chat' ),							// The label to the left of the option interface element
		'telegram_chat_header_plugin_callback',	// The name of the function responsible for rendering the option interface
		'telegram_chat_plugin_display_options',	// The page on which this option will be displayed
		'general_settings_section'		// The name of the section to which this field belongs
	);
	
	add_settings_field(	
		'show_content',						
		__( 'Install Telegram on your cell phone', 'telegram-chat' ),				
		'telegram_chat_content_plugin_callback',	
		'telegram_chat_plugin_display_options',					
		'general_settings_section'
	);
	
	// Finally, we register the fields with WordPress
	register_setting(
		'telegram_chat_plugin_display_options',
		'telegram_chat_plugin_display_options'
	);
	
} // end sandbox_initialize_theme_options
add_action( 'admin_init', 'telegram_chat_theme_options' );


function telegram_chat_information_callback() {
	
	$client  = @$_SERVER['HTTP_CLIENT_IP'];
	$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	$remote  = $_SERVER['REMOTE_ADDR'];
	if(filter_var($client, FILTER_VALIDATE_IP))				$ip = $client;
	elseif(filter_var($forward, FILTER_VALIDATE_IP))		$ip = $forward;
	else													$ip = $remote;
	
	$http_prefix="";
	if(strpos($_SERVER['SERVER_NAME'], 'http')===false){
		$http_prefix="http://";
	}
	
	
	echo '<script language="javascript">
		conectarWebSocketXtras("inscripcion", "'.$http_prefix.$_SERVER['SERVER_NAME'].'", "'.$ip.'", "'.$GLOBALS['telegram_version'].'", "'.get_locale().'");
	</script>';
	
	echo '<p>'. __( 'Do not lose communication with your customers, take all your conversations to your phone by Telegram. Just configure your phone number and ready! Now supports any visitor or information from your phone at any time. It is the easiest way to support and maintain contact with your customers.', 'telegram-chat');
	
	echo '<p>'. __( '<li>If you add more than one phone, you can answer from several cell phone at a time to the same customer, this makes communication more efficient and timely.</li><li>If your customers do not want to be connected to your website to have support, invite your client Telegram group and that they also enjoy the ease of communication. All with a single click.</li>', 'telegram-chat');
	
	echo '<p>'. __( '<div style="padding:10px; margin:10px; text-align:center; background-color:#1c6899; color:#FFFFFF;">Follow us on our channel, we will inform you of updates, news and promotions we have for you.<br><a href="https://telegram.me/Chat4BusinessEN" target="_blank"><strong style="font-size:17px; color:#FFFFFF;">@Chat4BusinessEN</strong></a></div>', 'telegram-chat');
	
	
} // end sandbox_general_options_callback


function telegram_chat_header_plugin_callback(){
	echo __( 'When a customer wants to contact you, the client must first fill out the initial form, at the time the customer submits the form, the system automatically creates a group on Telegram, the group consists of a phone cell that manages communication and phone cell collaborator that you added in the "<a href="?page=telegram-chat-plugin&tab=configuracion_options">Settings</a>“. Once the group was created, the system sends the initial information the customer filled in the first form, when the partner sends a message through his cell from Telegram, the message will automatically reach the website.', 'telegram-chat');
	
	echo '<br><br><center><img src="'.plugins_url( '../images/diagrama.png', __FILE__).'" width="500px" ></center>';
	
}

function telegram_chat_content_plugin_callback(){
	echo '<p><a href="https://www.youtube.com/watch?v=tGVKx8WR-7w" target="_blank">https://www.youtube.com/watch?v=tGVKx8WR-7w</a></p>';
}

?>