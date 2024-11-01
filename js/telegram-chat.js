// JavaScript Document
var websocket_telegramChat;
var banderaConfirmarEventoTelegram=0;
var procesoConfirmarEventoTelegram;
var desconecatdoTelegramChat=1;

function conectarWebSocket(registrar){
		
	//se pone el loading
	ponerLoading();
	
	//SE HABRE EL SOCKET
	var setTimeWebSocket;
	var refreshSocket=0;
	var typingClientTelegramEmpresarial=1;
	var typingColaboradorTelegramEmpresarial;
	var historialTelegramEmpresrial=1;
	var wsUri = "ws://chat4business.com:60900/";
	
 	var connect = function(){
				
		//create a new WebSocket object.
		websocket_telegramChat = new WebSocket(wsUri); 
		
		websocket_telegramChat.onopen = function(ev) { 
			
			
			if(tokenpublica==""){
				arr_cookie=Cookies.get('telegram-chat');
				var msg_cookie = JSON.parse(arr_cookie);
				tokenpublica=msg_cookie[0]['tokenpublica'];
				
				desconecatdoTelegramChat=0;
			}
			
			//se manda la notiricacion de conexión al servidor
			var msg = {
				type:"login",
				tokenpublica:tokenpublica 
			};
			//convert and send data to server
			websocket_telegramChat.send(JSON.stringify(msg));
						
		}
				
		//estatus del typing
		jQuery('#botonGetHistoryChatWA').click(function(){ //use clicks message send button	
			
			if(historialTelegramEmpresrial==1){
				//se cambia la bandera general
				historialTelegramEmpresrial=0;
				
				//se pone el loading
				jQuery("#mensajeCompletoWa_"+telegram_id.replace("$", "")+"").html('<br><br><center>'+mensajeMensajeCargandoImagenVentana+'<br>'+mensajeMensajeCargandoVentana+'</center>');
				
				//prepare json data
				var msg = {
					type:"getHistoryChatWA",
					message: mensajeEscribiendoVentana,
					
					//telegram
					telegram_id:telegram_id, 
					telegram_grupo:telegram_grupo, 
					telegram_colaboradores: telegram_colaboradores, 
					telegram_page: jQuery('#textoGetHistoryPageChatWA').val(), 
					//cliente
					url: url, 
					tokenpublica:tokenpublica, 
					os:os, 
					navegador: navegador, 
					cliente_nombre: cliente_nombre, 
					cliente_email: cliente_email, 
					cliente_telefono: cliente_telefono 
					
				};
				//convert and send data to server
				websocket_telegramChat.send(JSON.stringify(msg));
			}
		});
		
		//estatus del typing
		jQuery('#botonTypingChatWA').click(function(){ //use clicks message send button	
			
			if(typingClientTelegramEmpresarial==1){
				
				typingClientTelegramEmpresarial=0;
				//prepare json data
				var msg = {
					type:"typingChatWA",
					message: mensajeEscribiendoVentana,
					
					//telegram
					telegram_id:telegram_id, 
					telegram_grupo:telegram_grupo, 
					telegram_colaboradores: telegram_colaboradores, 
					telegram_line:telegram_line, 
					
					//cliente
					url: url, 
					tokenpublica:tokenpublica, 
					os:os, 
					navegador: navegador, 
					cliente_nombre: cliente_nombre, 
					cliente_email: cliente_email, 
					cliente_telefono: cliente_telefono 
					
				};
				//convert and send data to server
				websocket_telegramChat.send(JSON.stringify(msg));
			}
		
		});
		
		//estatus de la ventana
		jQuery('#botonEstatusChatWA').click(function(){ //use clicks message send button	
			
			//prepare json data
			var msg = {
				type:"estatusChatWA",
				message: jQuery('#textoEstatusChatWA').val(),
				
				//telegram
				telegram_id:telegram_id, 
				telegram_grupo:telegram_grupo, 
				telegram_colaboradores: telegram_colaboradores, 
				telegram_line:telegram_line, 
				
				//cliente
				url: url, 
				tokenpublica:tokenpublica, 
				os:os, 
				navegador: navegador, 
				cliente_nombre: cliente_nombre, 
				cliente_email: cliente_email, 
				cliente_telefono: cliente_telefono 
				
			};
			//convert and send data to server
			websocket_telegramChat.send(JSON.stringify(msg));
		
		});
		
		//envia texto
		jQuery('#botonMaestroWA').click(function(){ //use clicks message send button	
						
			if(jQuery('#message_chat').val()!="" ){
				
				if(desconecatdoTelegramChat==1){
					//connect();
				}
				
				typingClientTelegramEmpresarial=1;
				//jQuery("#botonMaestroWA,#message_chat").attr("disabled","disabled"); 
				//jQuery('#mensajeAjertaConversacion').remove();
				
				//prepare json data
				var msg = {
					type:"messageCliente",
					message: jQuery('#message_chat').val(),
					
					//telegram
					telegram_id:telegram_id, 
					telegram_grupo:telegram_grupo, 
					telegram_colaboradores: telegram_colaboradores, 
					telegram_line:telegram_line, 
					
					//cliente
					url: url, 
					tokenpublica:tokenpublica,
					os:os, 
					navegador: navegador, 
					cliente_nombre: cliente_nombre, 
					cliente_email: cliente_email, 
					cliente_telefono: cliente_telefono 
					
				};
				
				jQuery('#message_chat').prop( "disabled", true );
				
				//convert and send data to server				
				websocket_telegramChat.send(JSON.stringify(msg));
				
				//proceso de envio seguro
				banderaConfirmarEventoTelegram=1;
				procesoConfirmarEventoTelegram=setTimeout(confirmarEnvioTelegram, 7000);
			}
		});
		
		//envia texto
		jQuery('#botonNuevoChatWA').click(function(){ //use clicks message send button	
			
			if(jQuery('#message').val()!="" && banderaNuevoGrupo==0 && registrar==1){
				
				banderaNuevoGrupo=1; 
								
				arr_cookie=Cookies.get('telegram-chat');
				var msg_cookie = JSON.parse(arr_cookie);
				
					
				//prepare json data
				var msg = {
					type:"nuevoChat",
					message: jQuery('#message').val(),
					
					//telegram
					telegram_id:telegram_id, 
					telegram_grupo:telegram_grupo, 
					telegram_colaboradores: telegram_colaboradores, 
					telegram_colaboradores_verificar: telegram_colaboradores_verificar, 
					
					//cliente
					url: msg_cookie[0]['url'], 
					token: msg_cookie[0]['token'],
					tokenpublica:tokenpublica, 
					os: msg_cookie[0]['os'],
					ip: msg_cookie[0]['ip'],
					navegador: msg_cookie[0]['navegador'],
					
					cliente_nombre: jQuery('#telegram_nombre').val(), 
					cliente_email: jQuery('#telegram_email').val(), 
					cliente_telefono: jQuery('#telegram_telefono').val() ,
					
					mensaje_bienvenida_chat: jQuery('#mensajeInicioTelegramChat').val()
					
				};
				
				//convert and send data to server
				websocket_telegramChat.send(JSON.stringify(msg));
			}
		});
		
		
		//#### Message received from server?
		websocket_telegramChat.onmessage = function(ev) {
			
			var msg = JSON.parse(ev.data); //PHP sends Json data
			var type = msg.type; //message type
			banderaMensaje=0;
			
			if(type=="login"){
				if(tokenpublica==msg.tokenpublica && msg.tokenpublica!=""){
					
					//si es nuevo lo registro
					if(registrar==1){
						jQuery('#botonNuevoChatWA').click();
					}
					else{
						
						//se quita el loading
						loadingChannel=1;
						quitarLoading();
						
						//mensaje al telegram
						jQuery('#textoEstatusChatWA').val(mensajeAbrirVentana+"\n"+window.location.href+"\n\n<TELEGRAM><WP-CHAT><OPEN "+window.location.href+">");
						jQuery('#botonEstatusChatWA').click();
						
						setTimeout(function(){
							//como se abre, se actualiza el cookie con la sesion
							arr_cookie=Cookies.get('telegram-chat');
							var msg_cookie = JSON.parse(arr_cookie);
							msg_cookie[0]['session']=telegram_session;
							//se guarda la cookie
							Cookies.set('telegram-chat', msg_cookie);
						},1000);
						
						//se carga el historial
						setTimeout(function(){
							jQuery('#botonGetHistoryChatWA').click();
						},2000);
					}
				}
				else if(tokenpublica==""){
					//alert(mensajeErrorTelegramEmpresarial);
				}
			}
			else if(type=="serverCommunication"){
				if(tokenpublica==msg.tokenpublica){
					jQuery('#menssageLoadingTelegramChat').html(msg.mensajeServer);
					
					if(msg.mensajeCodeServer==0){
						quitarLoading();
						alert(msg.mensajeServer);
					}
				}
			}
			else if(type=="sendSuccessMensaje"){
				if(tokenpublica==msg.tokenpublica){
					
					//se quita el loop
					banderaConfirmarEventoTelegram=0;
					clearTimeout(procesoConfirmarEventoTelegram);
					
					//se habilita el texto
					jQuery('#message_chat').prop( "disabled", false );

					//se agrega a la conversacion
					cliente_nombre_chat=mensajeMensajeClienteVentana;
				
					mensaje_final='<div class="mensajeCliente" style="padding:5px; margin:5px; text-align:left; background-color:#e8fff3;"><strong style="color:#52d590; background-color:#e8fff3;">'+cliente_nombre_chat+'<br></strong>'+jQuery('#message_chat').val()+'</div>';
					jQuery('#mensajeCompletoWa_'+telegram_id.replace("$", "")).append(mensaje_final);		
					var objDiv = document.getElementById("mensajeCompletoWa_"+telegram_id.replace("$", "")+"");
					objDiv.scrollTop = objDiv.scrollHeight;
					jQuery('#message_chat').val('')
					//audio de mensaje
					audioElementTelegramChat.play();
					
					jQuery('#message_chat').focus();
					
				}
			}
			else if(type=="getHistoryChatWA"){
				
				if(tokenpublica==msg.tokenpublica){
					
					arr_cookie=Cookies.get('telegram-chat');
					var msg_cookie = JSON.parse(arr_cookie);
					
					jQuery('#mensajeCompletoWa_'+msg.telegram_id.replace("$", "")).html('');
					
					//se agrga el historial al chat
					decodeMensajeHistorial(msg_cookie[0]['plugin'], token, msg, msg.history.length-1, msg.history.length);
				}
			}
			else if(type=="typingCliente"){
				
				//se agrega a la conversacion
				jQuery('#statusTelegramChat_'+msg.telegram_id.replace("$", "")).text(mensajeMensajeEscribiendoVentana);
				typingColaboradorTelegramEmpresarial=setTimeout(function(){
					jQuery('#statusTelegramChat_'+msg.telegram_id.replace("$", "")).text('');
				}, 10000);
				
			}
			else if(type=="mensajeCliente"){
				
				if(tokenpublica==msg.tokenpublica){
					//cookie
					arr_cookie=Cookies.get('telegram-chat');
					var msg_cookie = JSON.parse(arr_cookie);
					var mensaje_enc, telegram_id_enc, telegram_grupo_enc;
					
					//se hace el decode
					decodeMensaje(msg_cookie[0]['plugin'], msg.mensaje_enc, token, msg.telegram_id_enc, msg.telegram_grupo_enc, typingColaboradorTelegramEmpresarial, "");
				}
				
			}
			else if(type=="nuevoChat"){
				
				if(tokenpublica==msg.tokenpublica){
					//cookie
					arr_cookie=Cookies.get('telegram-chat');
					var msg_cookie = JSON.parse(arr_cookie);
					//se pone el grupo en las variables y en el cookie
					decodeMensaje(msg_cookie[0]['plugin'], msg.mensaje_enc, token, msg.telegram_id_enc, msg.telegram_grupo_enc, 0, msg);
					
					if(msg.mensaje_bienvenida_enc!=""){
						decodeMensaje(msg_cookie[0]['plugin'], msg.mensaje_bienvenida_enc, token, msg.telegram_id_enc, msg.telegram_grupo_enc, 1, msg);
					}
					
				}
			}
		}
		
		websocket_telegramChat.onclose = function(){
			//se vuelve a conectar
			desconecatdoTelegramChat=1;
			
			websocket_telegramChat="";
			
			console.log("on close");
			
			setTimeout(connect, 2000)
			
		}
		websocket_telegramChat.onerror = function(evt){
			console.log("on error");
			console.log(evt);
		}
	}
	
	//se inicia nuevamente
	connect();
}

//si se desconecta el usuario
jQuery(window).on('beforeunload', function(e) {
		
	var msg = {
		type:"cerrarConexion",
		
		//telegram
		telegram_id:telegram_id, 
		telegram_grupo:telegram_grupo, 
		telegram_colaboradores: telegram_colaboradores, 
		telegram_line:telegram_line, 
		
		//cliente
		url: window.location.href,
		tokenpublica:tokenpublica,
		message:mensajeAvandonarVentanaTelgram+"\n"+window.location.href+"\n\n<TELEGRAM><WP-CHAT><CLOSE "+window.location.href+">"
		
	}
	websocket_telegramChat.send(JSON.stringify(msg));
	
});
	
	

/*
*		FUNCIONES
*/
function confirmarEnvioTelegram(){
	if(banderaConfirmarEventoTelegram==1){
		jQuery('#botonMaestroWA').click();
	}
}

function ponerLoading(){
	loadingChannel=1;
	jQuery('#loadingTelegramChat').css("visibility", "visible");
	jQuery('#loadingTelegramChat').transition({ height: '450px', duration: 500 });
	jQuery('#contenidoTelegramChat').css("visibility","hidden");
}
function quitarLoading(){
	
	loadingChannel=0;
	jQuery('#loadingTelegramChat').transition({ height: '0px', duration: 500 });
	
	setTimeout(function(){
		jQuery('#contenidoTelegramChat').css("visibility","visible");
		jQuery('#loadingTelegramChat').css("visibility", "hidden");
	}, 400);
}

function decodeMensajeHistorial(url, token, msg, i, total){
		
	textoHistorial="";
	for(j=0; j<total; j++){
		textoHistorial+=msg.history[j].wp_chat_historial_mensaje+"_$*$_"
	}
		
	//se desencripta
	jQuery.ajax({
		async: true,
		method: "POST",
		dataType: "json",
		url: url + "lib/hash.php", 
		data:{ accion:"decode", token: token, mensaje: textoHistorial, msg: msg, i:i, total:total }
		
	})
	.fail(function(){
		//alert('erorr');
		decodeMensajeHistorial(url, token, msg, i, total);
	})
	.done(function(data) {
		
		//se deshace el arregle del mensaje ya listo
		arr_mensaje=data.mensaje_enc.split('_$*$_');
		
		
		for(j=arr_mensaje.length-1; j>=0; j--){
			
			if(arr_mensaje[j]!=""){
				
				if(data.msg.history[j].wp_chat_historial_consultor==1){
					
					mensaje_final='<div class="mensajeAsesorChat" style="padding:5px; margin:5px; text-align:left; background-color:#f2feff;"><strong style="color:#769497; background-color:#f2feff;">'+mensajeMensajeAsesorVentana+'<br></strong>'+arr_mensaje[j]+'</div>';
					jQuery('#mensajeCompletoWa_'+data.msg.telegram_id.replace("$", "")).append(mensaje_final);	
				}
				else{
					mensaje_final='<div class="mensajeCliente" style="padding:5px; margin:5px; text-align:left; background-color:#e8fff3;"><strong style="color:#52d590; background-color:#e8fff3;">'+mensajeMensajeClienteVentana+'<br></strong>'+arr_mensaje[j]+'</div>';
					jQuery('#mensajeCompletoWa_'+data.msg.telegram_id.replace("$", "")).append(mensaje_final);
				}
			}
		}
		
		var objDiv = document.getElementById("mensajeCompletoWa_"+data.msg.telegram_id.replace("$", ""));
		objDiv.scrollTop = objDiv.scrollHeight;
	});
}


function decodeMensaje(url, mensaje_enc_final, token, telegram_id_enc, telegram_grupo_enc, typingColaboradorTelegramEmpresarial, msg){
	
	//se desencripta
	jQuery.ajax({
		async: true,
		method: "POST",
		dataType: "json",
		url: url + "lib/hash.php", 
		data:{ accion:"decode", token: token, mensaje: mensaje_enc_final, telegram_id_enc: telegram_id_enc, telegram_grupo_enc: telegram_grupo_enc, msg:msg }
	})
	.success(function(data) {
		
		if(typingColaboradorTelegramEmpresarial==0){
			
			//VARIABLES
			jQuery('#telegram_id').val(data.telegram_id_enc);
			jQuery('#telegram_grupo').val(data.telegram_grupo_enc);
			//COOKIE
			arr_cookie=Cookies.get('telegram-chat');
			var msg_cookie = JSON.parse(arr_cookie);
			msg_cookie[0]['telegram_line']=data.msg.telegram_line;
			msg_cookie[0]['telegram_id']=data.telegram_id_enc;
			msg_cookie[0]['telegram_grupo']=data.telegram_grupo_enc;
			msg_cookie[0]['cliente_nombre']=data.msg.nombre;
			msg_cookie[0]['cliente_email']=data.msg.correo;
			msg_cookie[0]['cliente_telefono']=data.msg.telefono;
			
			//se ponene las variables globales
			telegram_id=msg.telegram_id_enc;
			telegram_grupo=msg.telegram_grupo_enc;
			telegram_line=data.msg.telegram_line;
			cliente_nombre= data.msg.cliente_nombre; 
			cliente_email= data.msg.cliente_email;
			cliente_telefono= data.msg.cliente_telefono;
			
			//se guarda la cookie
			Cookies.set('telegram-chat', msg_cookie);
						
			//se pone en grande
			openChatTelegram('grande');
			
			//se actualiza el formulario
			formChatTelegram(0);
		
			//se quita el loading
			quitarLoading();
			
			//nombre cliente
			cliente_nombre=mensajeMensajeClienteVentana;
			if(cliente_nombre!=""){
				cliente_nombre_chat=cliente_nombre;
			}
			
			mensaje_enc= data.mensaje_enc;
			telegram_id_enc= data.telegram_id_enc;
			telegram_grupo_enc= data.telegram_grupo_enc;
			
			//se manda el mensaje
			mensaje_final='<div class="mensajeCliente" style="padding:5px; margin:5px; text-align:left; background-color:#e8fff3;"><strong style="color:#52d590; background-color:#e8fff3;">'+cliente_nombre_chat+'<br></strong>'+mensaje_enc+'</div>';
			jQuery('#mensajeCompletoWa_'+data.telegram_id_enc.replace("$", "")).append(mensaje_final);		
			var objDiv = document.getElementById("mensajeCompletoWa_"+data.telegram_id_enc.replace("$", "")+"");
			objDiv.scrollTop = objDiv.scrollHeight;
					
		}
		else{
			
			mensaje_enc= data.mensaje_enc;
			telegram_id_enc= data.telegram_id_enc;
			telegram_grupo_enc= data.telegram_grupo_enc;
			//telegram_line=data.msg.telegram_line;
			
			//typing status colaborador
			clearTimeout(typingColaboradorTelegramEmpresarial);
			jQuery('#statusTelegramChat_'+telegram_id_enc.toString().replace("$", "")).text('');
			
			//se agrega a la conversacion
			mensaje_final='<div class="mensajeAsesorChat" style="padding:5px; margin:5px; text-align:left; background-color:#f2feff;"><strong style="color:#769497; background-color:#f2feff;">'+mensajeMensajeAsesorVentana+'<br></strong>'+mensaje_enc+'</div>';
			jQuery('#mensajeCompletoWa_'+data.telegram_id_enc.toString().replace("$", "")).append(mensaje_final);		
			//se baja el scroll
			var objDiv = document.getElementById("mensajeCompletoWa_"+data.telegram_id_enc.toString().replace("$", "")+"");
			objDiv.scrollTop = objDiv.scrollHeight;
			
			//se hace el sonido
			audioElementTelegramChat.play();
			
			//se abre la ventana
			openChatTelegram("grande");
		}
	
	}).error(function() {
		decodeMensaje(url, mensaje_enc_final, token, telegram_id_enc, telegram_grupo_enc, typingColaboradorTelegramEmpresarial, msg);
		
	});
	
}