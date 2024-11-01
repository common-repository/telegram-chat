<?php

/**
 * Initializes the theme's social options by registering the Sections,
 * Fields, and Settings.
 *
 * This function is registered with the 'admin_init' hook.
 */ 
function sandbox_theme_initialize_social_options() {
	
	add_settings_section(
		'social_settings_section',			// ID used to identify this section and with which to register options
		__( 'Telegram-Chat Settings', 'telegram-chat' ),		// Title to be displayed on the administration page
		'sandbox_social_options_callback',	// Callback used to render the description of the section
		'telegram_chat_plugin_configuracion_options'		// Page on which to add this section of options
	);
	
} // end sandbox_theme_initialize_social_options
add_action( 'admin_init', 'sandbox_theme_initialize_social_options' );


function sandbox_social_options_callback() {
	echo '<p>' . __( 'With this plugin you can chat directly from your website to your Telegram, forget sitting waiting for your prospects to contact you, now you can do from your phone.<br><small><a href="http://www.telegramwordpress.com/faqs" target="_blank">Check out our FAQs if you have questions</a></small>', 'telegram-chat' ) . '</p>';
	
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
	//nombre de la tabla
	global $wpdb;
	$table_name = $wpdb->prefix . 'telegram_chat_info';
	
	//se busca en la tabla
	$resultsColaborador = $wpdb->get_results("SELECT * FROM ".$table_name." WHERE tipo='colaborador'", ARRAY_A );
	$resultsPhotos = $wpdb->get_results("SELECT * FROM ".$table_name." WHERE tipo='profile_photos'", ARRAY_A );
	$resultsInfo = $wpdb->get_results("SELECT * FROM ".$table_name." WHERE tipo='profile_info'", ARRAY_A );
	$resultsConfig = $wpdb->get_results("SELECT * FROM ".$table_name." WHERE tipo='profile_config'", ARRAY_A );
	
	//se hace la carpeta
	$upload_dir = wp_upload_dir();
	$target_dir = $upload_dir['basedir'].'/telegram-chat/';
	
	if(count($resultsInfo)==0){
		$resultsInfo[0]['id']=0;
	}
	if(count($resultsConfig)==0){
		$resultsConfig[0]['id']=0;
	}
	
	//se imprime el panel de administracion de la base de datos
	
	echo '<center>
			<table width="100%" border="0">
			  
			  <tr>
				<td colspan="3" align="center"><span style="color:#f44336; font-size:17px;">'.$GLOBALS['error'].'</span></td>
			  </tr>
			  
			  <tr>
				<td colspan="3"><br><span style="font-size:19px"><strong><span style="22px;">1.</span> '. __( 'Registered collaborators</span></strong><br><small>These users will receive conversations in the Telegram when a customer wants to chat.</small>', 'telegram-chat'). '
				</td>
			  </tr>';
			  
			if(count($resultsColaborador)!=0){
				$colorVerificado="#e3fbe0";
				$colorErroneo="#fbe0e0";
				$colorConfirmando="#DDDDDD";
				
				for($i=0; $i<count($resultsColaborador); $i++){
					
					if($resultsColaborador[$i]['valor2']==1) $color=$colorVerificado;
					else if($resultsColaborador[$i]['valor2']==2 or $resultsColaborador[$i]['valor2']=="") $color=$colorConfirmando;
					else $color=$colorErroneo;
					
					echo '<tr>
							<td class="tablaColaborador_'.$resultsColaborador[$i]['id'].'" style="background-color:'.$color.';">';
							
					if($resultsColaborador[$i]['valor2']==2 or $resultsColaborador[$i]['valor2']==""){
						echo '<img src="'.content_url('/plugins/telegram-chat/images/progress-circle.gif').'" class="imagenColaborador_'.$resultsColaborador[$i]['id'].'" width="15px;" /> ';
					}
							
					echo $resultsColaborador[$i]['valor1'].'<br>'.$resultsColaborador[$i]['valor4'].'</td>
							<td class="tablaColaborador_'.$resultsColaborador[$i]['id'].'" style="background-color:'.$color.';">';
							
								echo ''.$resultsColaborador[$i]['valor3'].'';
							
							echo '</td>
								<td style="">
									<form action="'.admin_url().'?page=telegram-chat-plugin&tab=configuracion_options" method="post">
										<input type="hidden" value="'.$resultsColaborador[$i]['id'].'" id="eliminarColaborador" name="eliminarColaborador">
										<input type="submit" value="'. __( 'Delete', 'telegram-chat'). '">
									</form>
								</td>
							</tr>';
							
					if($resultsColaborador[$i]['valor2']==2 or $resultsColaborador[$i]['valor2']==""){
						
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
							conectarWebSocketXtras("serverVerificarNumeroTelegram", "'.$resultsColaborador[$i]['valor3'].'", "'.$resultsColaborador[$i]['valor1'].'", "'.$resultsColaborador[$i]['id'].'", "'.$http_prefix.$_SERVER['SERVER_NAME'].'", "'.$ip.'", "'.$GLOBALS['telegram_version'].'", "'.get_locale().'", "'.$resultsColaborador[$i]['valor4'].'");
						</script>';
						
					}
				}
				echo '<tr><td colspan="3" align="center"><br><span style="font-size:12px; padding-top:10px;">'. __( '<span style="width:10px; height:10px; background-color:#e3fbe0; padding:5px; margin:5px;">Active user on Telegram</span> <span style="width:10px; height:10px; background-color:#fbe0e0; padding:5px; margin:5px;">User not active on Telegram</span> <span style="width:10px; height:10px; background-color:#DDDDDD; padding:5px; margin:5px;">Verifying user on Telegram</span><br>Do you know how to download Telegram on your mobile? <a href="https://www.youtube.com/watch?v=tGVKx8WR-7w" target="_blank">https://www.youtube.com/watch?v=tGVKx8WR-7w</a>', 'telegram-chat').'</span></td></tr>';
				
			}
			else{
				echo '<tr><td colspan="3" align="center"><span style="font-size:16px;">'. __( 'No collaborators', 'telegram-chat').'</span></td></tr>';
			}
			
			
			/*
			*  SE PREPARA LAS IMAGENES
			*/
			$upload_dir = wp_upload_dir();
			if($resultsPhotos[0]['valor1']==""){
				$resultsPhotos[0]['valor1']=plugins_url( '../images/Icon-user.png', __FILE__);
			}
			else{
				$resultsPhotos[0]['valor1']=content_url('/uploads/telegram-chat/'.$resultsPhotos[0]['valor1']);
			}
			
			if($resultsPhotos[0]['valor2']==""){
				$resultsPhotos[0]['valor2']=plugins_url( '../images/telegram.png', __FILE__);
			}
			else{
				$resultsPhotos[0]['valor2']=content_url('/uploads/telegram-chat/'.$resultsPhotos[0]['valor2']);
			}
			
			if(!isset($resultsPhotos[0]['id'])){
				$resultsPhotos[0]['id']=0;
			}
			
			//se agregan a los colaboradores
			echo '<tr>
						<td colspan="3"><hr></td>
					</tr>
					<tr>
						<td colspan="3">
							<form action="'.admin_url().'?page=telegram-chat-plugin&tab=configuracion_options" method="post">
								 <table width="100%" border="0" cellspacing="5" cellpadding="5">
								  <tr>
									<td>'. __( 'New collaborator', 'telegram-chat'). '</td>
									<td><input type="text" name="agregarNombreColaborador" placeholder="'. __( 'Name', 'telegram-chat'). '" value="" style="width:250px;" /></td>
									
									<td>
									
										<select name="guardar_cliente_lada" class="selectpicker" id="guardar_cliente_lada">
		
											<option disabled > '. __( 'Code', 'telegram-chat'). '</option>
											<option value="93" >Afghanistan (93)</option>
											<option value="355" >Albania (355)</option>
											<option value="1403" >Alberta (1403)</option>
											<option value="1780" >Alberta (1780)</option>
											<option value="213" >Algeria (213)</option>
											<option value="376" >Andorra (376)</option>
											<option value="244" >Angola (244)</option>
											<option value="1264" >Anguilla (1264)</option>
											<option value="6721" >Antarctica (Australian bases) (6721)</option>
											<option value="1268" >Antigua and Barbuda (1268)</option>
											<option value="54" >Argentina (54)</option>
											<option value="374" >Armenia (374)</option>
											<option value="297" >Aruba (297)</option>
											<option value="247" >Ascension (247)</option>
											<option value="61" >Australia (61)</option>
											<option value="43" >Austria (43)</option>
											<option value="994" >Azerbaijan (994)</option>
											<option value="1242" >Bahamas (1242)</option>
											<option value="973" >Bahrain (973)</option>
											<option value="880" >Bangladesh (880)</option>
											<option value="1246" >Barbados (1246)</option>
											<option value="375">Belarus (375)</option>
											<option value="32" >Belgium (32)</option>
											<option value="501" >Belize (501)</option>
											<option value="229" >Benin (229)</option>
											<option value="1441" >Bermuda (1441)</option>
											<option value="975" >Bhutan (975)</option>
											<option value="591" >Bolivia (591)</option>
											<option value="387" >Bosnia and Herzegovina (387)</option>
											<option value="267" >Botswana (267)</option>
											<option value="55" >Brazil (55)</option>
											<option value="1250" >British Columbia (1250)</option>
											<option value="1604" >British Columbia (1604)</option>
											<option value="1778" >British Columbia (1778)</option>
											<option value="246" >British Indian Ocean Territory (246)</option>
											<option value="1284" >British Virgin Islands (1284)</option>
											<option value="673" >Brunei (673)</option>
											<option value="359" >Bulgaria (359)</option>
											<option value="226" >Burkina Faso (226)</option>
											<option value="257" >Burundi (257)</option>
											<option value="855" >Cambodia (855)</option>
											<option value="237" >Cameroon (237)</option>
											<option value="238" >Cape Verde (238)</option>
											<option value="1345" >Cayman Islands (1345)</option>
											<option value="236" >Central African Republic (236)</option>
											<option value="235" >Chad (235)</option>
											<option value="56">Chile (56)</option>
											<option value="86" >China (86)</option>
											<option value="57" >Colombia (57)</option>
											<option value="269" >Comoros (269)</option>
											<option value="243" >Democratic Republic of the Congo (243)</option>
											<option value="242" >Republic of the Congo (242)</option>
											<option value="682" >Cook Islands (682)</option>
											<option value="506" >Costa Rica (506)</option>
											<option value="712" >Cote dIvoire (712)</option>
											<option value="385" >Croatia (385)</option>
											<option value="53" >Cuba (53)</option>
											<option value="357" >Cyprus (357)</option>
											<option value="420" >Czech Republic (420)</option>
											<option value="45" >Denmark (45)</option>
											<option value="253">Djibouti (253)</option>
											<option value="1767" >Dominica (1767)</option>
											<option value="1809" >Dominican Republic (1809)</option>
											<option value="1829" >Dominican Republic (1829)</option>
											<option value="670" >East Timor (670)</option>
											<option value="593" >Ecuador (593)</option>
											<option value="20" >Egypt (20)</option>
											<option value="503" >El Salvador (503)</option>
											<option value="240" >Equatorial Guinea (240)</option>
											<option value="291" >Eritrea (291)</option>
											<option value="372" >Estonia (372)</option>
											<option value="251" >Ethiopia (251)</option>
											<option value="500" >Falkland Islands (500)</option>
											<option value="298" >Faroe Islands (298)</option>
											<option value="679" >Fiji (679)</option>
											<option value="358" >Finland (358)</option>
											<option value="33" >France (33)</option>
											<option value="594" >French Guiana (594)</option>
											<option value="689" >French Polynesia (689)</option>
											<option value="241" >Gabon (241)</option>
											<option value="220" >Gambia (220)</option>
											<option value="970" >Gaza Strip (970)</option>
											<option value="995" >Georgia (995)</option>
											<option value="49" >Germany (49)</option>
											<option value="233" >Ghana (233)</option>
											<option value="350" >Gibraltar (350)</option>
											<option value="30" >Greece (30)</option>
											<option value="299" >Greenland (299)</option>
											<option value="1473" >Grenada (1473)</option>
											<option value="590" >Guadeloupe (590)</option>
											<option value="1671" >Guam (1671)</option>
											<option value="502" >Guatemala (502)</option>
											<option value="224" >Guinea (224)</option>
											<option value="245" >Guinea-Bissau (245)</option>
											<option value="592" >Guyana (592)</option>
											<option value="509" >Haiti (509)</option>
											<option value="504" >Honduras (504)</option>
											<option value="852" >Hong Kong (852)</option>
											<option value="36" >Hungary (36)</option>
											<option value="354" >Iceland (354)</option>
											<option value="91" >India (91)</option>
											<option value="62" >Indonesia (62)</option>
											<option value="964" >Iraq (964)</option>
											<option value="98" >Iran (98)</option>
											<option value="353" >Ireland (Eire) (353)</option>
											<option value="972" >Israel (972)</option>
											<option value="39" >Italy (39)</option>
											<option value="1876" >Jamaica (1876)</option>
											<option value="81" >Japan (81)</option>
											<option value="962" >Jordan (962)</option>
											<option value="77" >Kazakhstan (77)</option>
											<option value="254" >Kenya (254)</option>
											<option value="686" >Kiribati (686)</option>
											<option value="965" >Kuwait (965)</option>
											<option value="996" >Kyrgyzstan (996)</option>
											<option value="856" >Laos (856)</option>
											<option value="371" >Latvia (371)</option>
											<option value="961" >Lebanon (961)</option>
											<option value="266" >Lesotho (266)</option>
											<option value="231" >Liberia (231)</option>
											<option value="218" >Libya (218)</option>
											<option value="423" >Liechtenstein (423)</option>
											<option value="370" >Lithuania (370)</option>
											<option value="352" >Luxembourg (352)</option>
											<option value="853" >Macau (853)</option>
											<option value="389" >Republic of Macedonia (389)</option>
											<option value="261" >Madagascar (261)</option>
											<option value="265" >Malawi (265)</option>
											<option value="60" >Malaysia (60)</option>
											<option value="960">Maldives (960)</option>
											<option value="223" >Mali (223)</option>
											<option value="356" >Malta (356)</option>
											<option value="1204" >Manitoba (1204)</option>
											<option value="692" >Marshall Islands (692)</option>
											<option value="596" >Martinique (596)</option>
											<option value="222" >Mauritania (222)</option>
											<option value="230" >Mauritius (230)</option>
											<option value="262" >Mayotte (262)</option>
											<option value="52" >México (52)</option>
											<option value="691" >Federated States of Micronesia (691)</option>
											<option value="373" >Moldova (373)</option>
											<option value="377" >Monaco (377)</option>
											<option value="976" >Mongolia (976)</option>
											<option value="382" >Montenegro (382)</option>
											<option value="1664" >Montserrat (1664)</option>
											<option value="212" >Morocco (212)</option>
											<option value="258" >Mozambique (258)</option>
											<option value="95" >Myanmar (95)</option>
											<option value="264" >Namibia (264)</option>
											<option value="674" >Nauru (674)</option>
											<option value="31" >Netherlands (31)</option>
											<option value="599" >Netherlands Antilles (599)</option>
											<option value="977" >Nepal (977)</option>
											<option value="1506" >New Brunswick (1506)</option>
											<option value="687">New Caledonia (687)</option>
											<option value="64" >New Zealand (64)</option>
											<option value="1709" >Newfoundland (1709)</option>
											<option value="505" >Nicaragua (505)</option>
											<option value="227" >Niger (227)</option>
											<option value="234" >Nigeria (234)</option>
											<option value="683" >Niue (683)</option>
											<option value="6723" >Norfolk Island (6723)</option>
											<option value="850" >North Korea (850)</option>
											<option value="1670" >Northern Mariana Islands (1670)</option>
											<option value="1867" >Northwest Territories (1867)</option>
											<option value="47" >Norway (47)</option>
											<option value="1902" >Nova Scotia (1902)</option>
											<option value="968" >Oman (968)</option>
											<option value="1416" >Ontario (1416)</option>
											<option value="1519" >Ontario (1519)</option>
											<option value="1613" >Ontario (1613)</option>
											<option value="1647" >Ontario (1647)</option>
											<option value="1705" >Ontario (1705)</option>
											<option value="1807" >Ontario (1807)</option>
											<option value="1905" >Ontario (1905)</option>
											<option value="92" >Pakistan (92)</option>
											<option value="680" >Palau (680)</option>
											<option value="970" >Palestine (970)</option>
											<option value="507" >Panama (507)</option>
											<option value="675" >Papua New Guinea (675)</option>
											<option value="595" >Paraguay (595)</option>
											<option value="51">Peru (51)</option>
											<option value="63" >Philippines (63)</option>
											<option value="48" >Poland (48)</option>
											<option value="351" >Portugal (351)</option>
											<option value="974" >Qatar (974)</option>
											<option value="1418" >Quebec (1418)</option>
											<option value="1450" >Quebec (1450)</option>
											<option value="1514" >Quebec (1514)</option>
											<option value="1819" >Quebec (1819)</option>
											<option value="262" >Reunion (262)</option>
											<option value="40" >Romania (40)</option>
											<option value="79" >Russia (79)</option>
											<option value="250" >Rwanda (250)</option>
											<option value="590" >Saint-Barthelemy (590)</option>
											<option value="290" >Saint Helena (290)</option>
											<option value="1869" >Saint Kitts and Nevis (1869)</option>
											<option value="1758" >Saint Lucia (1758)</option>
											<option value="590" >Saint Martin (French side) (590)</option>
											<option value="508" >Saint Pierre and Miquelon (508)</option>
											<option value="1670" >Saint Vincent and the Grenadines (1670)</option>
											<option value="685" >Samoa (685)</option>
											<option value="239" >Sao Tome and Principe (239)</option>
											<option value="1306" >Saskatchewan (1306)</option>
											<option value="966" >Saudi Arabia (966)</option>
											<option value="221" >Senegal (221)</option>
											<option value="381" >Serbia (381)</option>
											<option value="248" >Seychelles (248)</option>
											<option value="232" >Sierra Leone (232)</option>
											<option value="65" >Singapore (65)</option>
											<option value="421" >Slovakia (421)</option>
											<option value="386" >Slovenia (386)</option>
											<option value="677" >Solomon Islands (677)</option>
											<option value="252" >Somalia (252)</option>
											<option value="27" >South Africa (27)</option>
											<option value="82">South Korea (82)</option>
											<option value="211" >South Sudan (211)</option>
											<option value="34" >Spain (34)</option>
											<option value="94" >Sri Lanka (94)</option>
											<option value="249">Sudan (249)</option>
											<option value="597" >Suriname (597)</option>
											<option value="268" >Swaziland (268)</option>
											<option value="46" >Sweden (46)</option>
											<option value="41" >Switzerland (41)</option>
											<option value="963" >Syria (963)</option>
											<option value="886" >Taiwan (886)</option>
											<option value="992" >Tajikistan (992)</option>
											<option value="255" >Tanzania (255)</option>
											<option value="66" >Thailand (66)</option>
											<option value="228" >Togo (228)</option>
											<option value="690" >Tokelau (690)</option>
											<option value="676" >Tonga (676)</option>
											<option value="1868" >Trinidad and Tobago (1868)</option>
											<option value="216" >Tunisia (216)</option>
											<option value="90" >Turkey (90)</option>
											<option value="993" >Turkmenistan (993)</option>
											<option value="1649" >Turks and Caicos Islands (1649)</option>
											<option value="688">Tuvalu (688)</option>
											<option value="256" >Uganda (256)</option>
											<option value="380">Ukraine (380)</option>
											<option value="971" >United Arab Emirates (971)</option>
											<option value="44" >United Kingdom (44)</option>
											<option value="1" >United States of America (1)</option>
											<option value="598" >Uruguay (598)</option>
											<option value="998" >Uzbekistan (998)</option>
											<option value="678" >Vanuatu (678)</option>
											<option value="58" >Venezuela (58)</option>
											<option value="84" >Vietnam (84)</option>
											<option value="1340" >U.S. Virgin Islands (1340)</option>
											<option value="681" >Wallis and Futuna (681)</option>
											<option value="970" >West Bank (970)</option>
											<option value="967" >Yemen (967)</option>
											<option value="260" >Zambia (260)</option>
											<option value="263" >Zimbabwe (263)</option>
										</select>
									</td>
									<td><input type="text" name="agregarTelefonoColaborador" placeholder="'. __( 'Phone (cellphone number)', 'telegram-chat'). '" value="" style="width:250px;" /></td>
								  </tr>
								  <tr>
								  	<td >'. __( 'Alert new conversation', 'telegram-chat'). '</td>
									<td colspan="3">
										<input type="text" name="agregarCorreoColaborador" placeholder="'. __( 'Email', 'telegram-chat'). '" value="" style="width:250px;" />
									</td>
								  </tr>
								</table>
							  <input type="submit" value="'. __( 'Save', 'telegram-chat'). '">
							</form>
						</td>
					</tr>
					<tr>
						<td colspan="3"><hr></td>
					</tr>
					
					<tr>
					   	<td style="width:100%;" colspan="3">'. __( '<span style="font-size:19px">Chat Information</span><br><small>Information and images that were used in the chat window, do not need to be the same as collaborators.</small>', 'telegram-chat'). '</td>
					</tr>
					<tr>
						<td colspan="2">
							<form action="'.admin_url().'?page=telegram-chat-plugin&tab=configuracion_options" method="post" >
								<input type="hidden" value="'.$resultsInfo[0]['id'].'" name="profile_info_id" id="profile_info_id" >
								<input type="hidden" value="'.$resultsConfig[0]['id'].'" name="profile_config_id" id="profile_config_id" >
								
								
								 <table width="100%" border="0" cellspacing="5" cellpadding="5">
								  <tr>
									<td>'. __( 'Visible on mobile', 'telegram-chat'). '</td>
									<td>
										<input type="radio" name="profile_movile" id="profile_movile" value="1" '; if($resultsInfo[0]['valor4']=="1" or $resultsInfo[0]['valor4']==""){ echo 'checked="checked"'; } echo '>'. __( 'Yes', 'telegram-chat'). '</input>
										<input type="radio" name="profile_movile" id="profile_movile" value="0" '; if($resultsInfo[0]['valor4']=="0"){ echo 'checked="checked"'; } echo '>'. __( 'No', 'telegram-chat'). '</input>
									</td>
								  </tr>
								  <tr>
									<td>'. __( 'Open chat on opening', 'telegram-chat'). '</td>
									<td>
										<input type="radio" name="profile_openChat" id="profile_openChat" value="1" '; if($resultsConfig[0]['valor1']=="1" or $resultsConfig[0]['valor1']==""){ echo 'checked="checked"'; } echo '>'. __( 'Yes', 'telegram-chat'). '</input>
										<input type="radio" name="profile_openChat" id="profile_openChat" value="0" '; if($resultsConfig[0]['valor1']=="0"){ echo 'checked="checked"'; } echo '>'. __( 'No', 'telegram-chat'). '</input>
									</td>
								  </tr>
								  <tr>
									<td>'. __( 'Profile Name', 'telegram-chat'). '</td>
									<td><input type="text" name="profile_nombre" placeholder="" value="'.$resultsInfo[0]['valor1'].'" style="width:350px;" /></td>
								  </tr>
								  <tr>
									<td>'. __( 'Profile photo', 'telegram-chat'). '</td>
									<td><input type="text" name="profile_puesto" placeholder="" value="'.$resultsInfo[0]['valor2'].'" style="width:350px;" /></td>
								  </tr>
								  <tr>
									<td>'. __( 'Department', 'telegram-chat'). '</td>
									<td><input type="text" name="profile_departamento" placeholder="" value="'.$resultsInfo[0]['valor3'].'" style="width:350px;" /></td>
								  </tr>
								  <tr>
									<td>'. __( 'Welcome message', 'telegram-chat'). '</td>
									<td><textarea id="profile_autoMessage" name="profile_autoMessage" style="width:350px;">'.$resultsConfig[0]['valor2'].'</textarea></td>
								  </tr>
								</table>
							 <input type="submit" value="'. __( 'Save', 'telegram-chat'). '">
							</form>
						</td>
						<td rowspan="2" align="center" valign="middle">
							<img src="'.plugins_url('../images/ayudaInfo.png', __FILE__).'" width="100%" style="max-width:550px;">
						</td>
					</tr>
					
					<tr>
						<td colspan="2">
							<form action="'.admin_url().'?page=telegram-chat-plugin&tab=configuracion_options" method="post" enctype="multipart/form-data">
								<input type="hidden" value="'.$resultsPhotos[0]['id'].'" name="profile_photo_id" id="profile_photo_id" >
								 <table width="100%" border="0" cellspacing="5" cellpadding="5">
								  <tr>
									<td align="center" width="35%;"><span style="font-size:19px">'. __( 'Profile photo', 'telegram-chat'). '</span></td>
									<td align="center" width="30%;">&nbsp;</td>
									<td align="center" width="35%;"><span style="font-size:19px">'. __( 'Company photo', 'telegram-chat'). '</span></td>
								  </tr>
								  <tr>
									<td align="center"><img src="'.$resultsPhotos[0]['valor1'].'" style="max-width:200px; max-height:200px;">
										<br><small>'. __( 'Recommended 80x80px', 'telegram-chat'). '</small>
									</td>
									<td>&nbsp;</td>
									<td align="center"><img src="'.$resultsPhotos[0]['valor2'].'" style="max-width:200px; max-height:200px;">
										<br><small>'. __( 'Recommended 60x60px', 'telegram-chat'). '</small>
									</td>
								  </tr>
								  <tr>
									<td align="center"><input type="file" name="fileFotoPerfil" id="fileFotoPerfil" multiple="false" /></td>
									<td>&nbsp;</td>
									<td align="center"><input type="file" name="fileLogoEmpresa" id="fileLogoEmpresa" multiple="false" /></td>
								  </tr>
								</table>
							  <input type="submit" value="'. __( 'Save', 'telegram-chat'). '">
							</form>
						</td>
					</tr>
			</table>
		</center>';
	
}

?>