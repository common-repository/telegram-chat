// JavaScript Document
var loadChatTelegram=0;
var loadingChannel=0;
var banderaNuevoGrupo=0;
var banderaAvisoTelegramEmpresarial=1;
var setTimeAvisoTelegramEmpresarial;

function mostrarAvisoTelegramEmpresarial(){
	jQuery('#mensajeTelegramChat').css('display', 'block');
	jQuery('#mensajeTelegramChat').transition({ height: '181px', width:'240px' });
	jQuery('#mensajeTelegramChat img').transition({ opacity: '100'});
}

function ocultarAvisoTelegramEmpresarial(){
	
	if(banderaAvisoTelegramEmpresarial==1){
		//se quita el timeout
		clearTimeout(setTimeAvisoTelegramEmpresarial);
		//se quita el aviso
		jQuery('#mensajeTelegramChat').transition({ height: '0', width:'240px' });
		jQuery('#mensajeTelegramChat img').transition({ opacity: '0'});
		banderaAvisoTelegramEmpresarial=0;
		jQuery('#mensajeTelegramChat').css('display', 'none');
	}
}

function openChatTelegram(tamanio){
	
	//se quita el aviso del chat
	ocultarAvisoTelegramEmpresarial();
	
	if(tamanio=="grande"){
		jQuery('#chatTelegramTotalpat').transition({ height: '500px', width:'370px' });
		jQuery('#tituloTelegramChat td:last-child i').css("display", "block");
		
		if(loadChatTelegram==0 && loadingChannel==0){
			formChatTelegram(0);
			loadChatTelegram=1;
			jQuery('#contenidoTelegramChat').css("visibility", "visible");
		}
		else if(loadingChannel==1){
			jQuery('#loadingTelegramChat').css("visibility", "visible");
		}
		else{
			jQuery('#contenidoTelegramChat').css("visibility", "visible");
			jQuery('#loadingTelegramChat').css("visibility", "hidden");
		}
		
	}else if(jQuery('#chatTelegramTotalpat').css("width")=="250px"){
		
		jQuery('#chatTelegramTotalpat').transition({ height: '500px', width:'370px' });
		jQuery('#tituloTelegramChat td:last-child i').css("display", "block");
		
		if(loadChatTelegram==0 && loadingChannel==0){
			formChatTelegram(1);
			loadChatTelegram=1;
			jQuery('#contenidoTelegramChat').css("visibility", "visible");
			
		}
		else if(loadingChannel==1){
			jQuery('#loadingTelegramChat').css("visibility", "visible");
			
		}
		else{
			jQuery('#contenidoTelegramChat').css("visibility", "visible");
			jQuery('#loadingTelegramChat').css("visibility", "hidden");
			
			//mensaje al telegram
			jQuery('#textoEstatusChatWA').val(mensajeMaximizarVentana+"\n\n[TELEGRAM][WP-CHAT][MINIMIZES]");
			jQuery('#botonEstatusChatWA').click();
		}
		
	}
	else{
		jQuery('#chatTelegramTotalpat').transition({ height: '40px', width:'250px' });
		jQuery('#tituloTelegramChat td:last-child i').css("display", "none");
		
		if(loadingChannel==0){
			setTimeout(function(){
				jQuery('#contenidoTelegramChat').css("visibility", "hidden");
				
				//mensaje al telegram
				jQuery('#textoEstatusChatWA').val(mensajeMinimizarVentana+"\n\n[TELEGRAM][WP-CHAT][MANIMIZES]");
				jQuery('#botonEstatusChatWA').click();
				
			}, 350);
		}
		else{
			jQuery('#loadingTelegramChat').css("visibility", "hidden");
		}
		
	}
	
}

function enviarFormulario(){
	if(jQuery('#telegram_nombre').val()!="" && jQuery('#telegram_email').val()!="" && jQuery('#message').val()!=""){
		//se actualiza la cookie
		arr_cookie=Cookies.get('telegram-chat');
		var msg_cookie = JSON.parse(arr_cookie);
		msg_cookie[0]['cliente_nombre']=jQuery('#telegram_nombre').val();
		msg_cookie[0]['cliente_email']=jQuery('#telegram_email').val();
		msg_cookie[0]['cliente_telefono']=jQuery('#telegram_telefono').val();
		Cookies.set('telegram-chat', msg_cookie);
		
		ponerLoading();
		//se conecta al socket y se crea todo el pedo
		conectarWebSocket(1);
	}
	else{
		alert(mensajeAlertaNombreCorreoVentana);
	}
}


function enviarMensaje(){
	jQuery('#botonMaestroWA').click();
}

function formChatTelegram(cargarNodo){
	arr_cookie=Cookies.get('telegram-chat');
	var msg_cookie = JSON.parse(arr_cookie);
	
	jQuery('#contenidoTelegramChat').html('');
	
	colaborador_photo=msg_cookie[0]['plugin']+'images/Icon-user.png';
	if(msg_cookie[0]['colaborador_photo']!="" && msg_cookie[0]['colaborador_photo']!=null){
		colaborador_photo=msg_cookie[0]['upload']+'/'+msg_cookie[0]['colaborador_photo'];
	}
	
	empresa_photo=msg_cookie[0]['plugin']+'images/telegram.png';
	if(msg_cookie[0]['colaborador_business']!="" && msg_cookie[0]['colaborador_business']!=null){
		empresa_photo=msg_cookie[0]['upload']+'/'+msg_cookie[0]['colaborador_business'];
	}
	
	
	if(msg_cookie[0]['telegram_id']==""){
	
		formulario='<table width="100%" border="0" cellspacing="0" cellpadding="0" class="defaulttable simple" id="tablaGeneralContenido" ><tr>					<td style="border-color:#FFF; background:#FFF;" class="resetTD">						<table width="100%" border="0" cellspacing="0" cellpadding="0" class="defaulttable" id="profileAsesor" >						  <tr>							<td style="text-align:center; padding-bottom:0px; border-color:#FFF; background:#FFF;" class="resetTD"><img src="'+colaborador_photo+'" style="margin-right:10px;"></td>							<td style="font-family:\'Lucida Sans Unicode\', \'Lucida Grande\', sans-serif; text-align:left; vertical-align:top; padding:0px; margin:0px; color:#000000; font-size:13px; "><strong style="font-size:17px;">'+msg_cookie[0]['colaborador_nombre'].replace(/[+]/gi,' ')+'</strong><br>'+msg_cookie[0]['colaborador_puesto'].replace(/[+]/gi,' ')+'<br>'+msg_cookie[0]['colaborador_departamento'].replace(/[+]/gi,' ')+'</td>							<td><img src="'+empresa_photo+'" ></td>						  </tr>						</table>					</td>				  </tr>				  <tr>					<td style="padding:10px; padding-top:5px; font-size:12px; font-family:\'Lucida Sans Unicode\', \'Lucida Grande\', sans-serif; border-color:#FFF; background:#FFF;"><div id="mensajeAsesor">'+mensajeBienvenidaVentana+'</div></td>				  </tr>				  <tr>					<td>		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="defaulttable">						  <tr>							<td style="padding-bottom:5px; border-color:#FFF; background:#FFF;" class="resetTD"><textarea style="padding:3px; margin:0; color:#666;" class="clearForms textareaTelegramChatRegistro" id="message" placeholder="'+mensajeEscribeComentariosVentana+'"></textarea></td>						  </tr>						  <tr>							<td style="padding-bottom:10px; border-color:#FFF; background:#FFF;" class="resetTD">								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="defaulttable profileCliente simple">								  <tr >									<td class="labelDatos" >'+mensajeNombreVentana+'</td>									<td ><input class="clearForms inputTelegramChat" type="text" id="telegram_nombre" style="height:20px; padding:3px; margin:0;  color:#666;"  /></td>								  </tr>								</table>							</td>						  </tr>						  <tr>							<td style="padding-bottom:10px; border-color:#FFF; background:#FFF;" class="resetTD">								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="simple defaulttable profileCliente resetTD">								  <tr>									<td class="labelDatos resetTD">E-mail</td>									<td><input class="inputTelegramChat" type="text" id="telegram_email" style="height:20px; padding:3px; margin:0; color:#666;" /></td>								  </tr>								</table>							</td>						  </tr>						  <tr>							<td style="padding-bottom:10px; border-color:#FFF; background:#FFF;" class="resetTD">								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="defaulttable profileCliente">								  <tr>									<td class="labelDatos">'+mensajeTelefonoVentana+'</td>									<td><input class="inputTelegramChat" type="text" id="telegram_telefono" placeholder="('+mensajeOpcionalVentana+')" style="height:20px; padding:3px; margin:0; color:#666;" /></td>								  </tr>								</table>							</td>						  </tr>						  <tr>							<td style="text-align:center; padding-bottom:0px; border-color:#FFF; background:#FFF;" class="resetTD">								<button onclick="enviarFormulario();" class="buttonTelegramChat" style="padding:7px; color:#ffffff;">'+mensajeEnviarVentana+'</button>							</td>						  </tr>						</table>					</td>				  </tr>				</table>';
	}
	else{
		//se evita el formulario inicial al minimizar
		loadChatTelegram=1;
		if(cargarNodo==1){
			conectarWebSocket(0);
		}
		
		cliente_nombre="Cliente";
		if(msg_cookie[0]['cliente_nombre']!=null){
			cliente_nombre=msg_cookie[0]['cliente_nombre'].replace(/[+]/gi,' ');
		}
		
		telegram_id=msg_cookie[0]['telegram_id'];
				
		formulario='<table width="100%" border="0" cellspacing="0" cellpadding="0" class="defaulttable simple"><tr>					<td style="border:none;">						<table width="100%" border="0" cellspacing="0" cellpadding="0" class="defaulttable simple" id="profileAsesor" >						  <tr>							<td><img src="'+colaborador_photo+'" style="margin-right:10px;" ></td>							<td style="text-align:left; vertical-align:top; color:#000000; font-size:13px; font-family:\'Lucida Sans Unicode\', \'Lucida Grande\', sans-serif;"><strong style="font-size:17px;">'+msg_cookie[0]['colaborador_nombre'].replace(/[+]/gi,' ')+'</strong><br>'+msg_cookie[0]['colaborador_puesto'].replace(/[+]/gi,' ')+'<br>'+msg_cookie[0]['colaborador_departamento'].replace(/[+]/gi,' ')+'<br><span id="statusTelegramChat_'+telegram_id.replace("$", "")+'"></span></td>							<td><img src="'+empresa_photo+'" ></td>						  </tr>						</table>					</td>				  </tr>				  <tr>					<td style="padding:3px; border:none;" id="tableMensajeTelegramChat"><div id="mensajeCompletoWa_'+telegram_id.replace("$", "")+'" class="historialChat scrollbar style-1" style="border:solid 1px #EEEEEE; background-color:#f6f6f6;" ></div></td>				  </tr>				  <tr>					<td style="border-color:#FFF; background:#FFF;">		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="defaulttable simple" id="">						  <tr>							<td  style="border-color:#FFF; background:#FFF;"><textarea class="textareaTelegramChat message_chat" style="margin:0px; margin-left:5px; width:95%; padding:3px;" id="message_chat" placeholder="'+mensajeMensajeChatVentana+'" onkeypress="return enviarMensajeWAEnter(event, this);"></textarea></td>						  							<td style="vertical-align:middle; text-align:center; border-color:#FFF; background:#FFF;">								<button onclick="enviarMensaje();" class="buttonTelegramChat" style="padding:5px; color:#FFFFFF; border-color:#f44336; background:#f44336;">'+mensajeEnviarVentana+'</button>							</td>						  </tr>						</table>					</td>				  </tr>				</table>';
	}
	
	jQuery('#contenidoTelegramChat').append(formulario);
}


function enviarMensajeWAEnter(e, textarea){
	//se manda el escribiendo
	jQuery('#botonTypingChatWA').click();
	
	//si es enter, se manda el texto
	var code = (e.keyCode ? e.keyCode : e.which);
	if(code == 13) { //Enter keycode
		jQuery('#botonMaestroWA').click();
		return false;
	}
}

function openTelegramChatPowered(){
	var win = window.open("http://TelegramWordpress.com", '_blank');
	win.focus();
}