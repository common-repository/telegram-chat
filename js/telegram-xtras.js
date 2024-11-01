// JavaScript Document
var websocket_telegramChat;
var banderaConfirmarEventoTelegram=0;
var procesoConfirmarEventoTelegram;
var tokenpublica_admin=Math.floor((Math.random() * 100) + 1);

function conectarWebSocketXtras(tipo, dato1, dato2, dato3, dato4, dato5, dato6, dato7, dato8){
	
	//SE HABRE EL SOCKET
	var setTimeWebSocket;
	var refreshSocket=0;
	var typingClientTelegramEmpresarial=1;
	var typingColaboradorTelegramEmpresarial;
	var historialTelegramEmpresrial=1;
	var wsUri = "ws://chat4business.com:60900/";
	
 	var connect_xtra = function(url, ip){
		
		//create a new WebSocket object.
		websocket_telegramChat = new WebSocket(wsUri); 
		
		websocket_telegramChat.onopen = function(ev) { 
			
			if(tipo=="inscripcion"){
				//se manda la notiricacion de conexión al servidor
				var msg = {
					server:"inscripcion",
					url:dato1,
					ip:dato2,
					lan:dato4,
					version:dato3,
					tokenpublica:tokenpublica_admin 
				};
			}
			else if(tipo=="serverVerificarNumeroTelegram"){
				var msg = {
					server:"serverVerificarNumeroTelegram",
					celular:dato1,
					nombre:dato2,
					id: dato3,
					url: dato4, 
					ip:dato5,
					lan:dato7,
					version:dato6,
					correo: dato8,
					tokenpublica:tokenpublica_admin 
				};
			}
			else if(tipo=="activarTokenServerTelegram"){
				
				var msg = {
					server:"activarTokenServerTelegram",
					url:dato1,
					token:dato2,
					id:dato3,
					tokenpublica:tokenpublica_admin 
				};
			}
			
			//convert and send data to server
			websocket_telegramChat.send(JSON.stringify(msg));
					
		}
		
		
		//#### Message received from server?
		websocket_telegramChat.onmessage = function(ev) {
			
			var msg = JSON.parse(ev.data); //PHP sends Json data
			var type = msg.type; //message type
			banderaMensaje=0;
			
			if(type=="verificarCelularRespuesta"){
				if(tokenpublica_admin==msg.tokenpublica && msg.tokenpublica!=""){
					if(msg.respuesta==1){
						jQuery('.tablaColaborador_'+msg.id).css('background-color', '#e3fbe0');
						jQuery('.imagenColaborador_'+msg.id).remove();
					}
					else{
						jQuery('.tablaColaborador_'+msg.id).css('background-color', '#fbe0e0');
						jQuery('.imagenColaborador_'+msg.id).remove();
					}
					
					
					actionTelegramChatAJAX("statusUserTelegram", msg.respuesta, msg.id, msg.url);
					
				}
			}
			else if(type=="verificarTokenRespuesta"){
				
				if(tokenpublica_admin==msg.tokenpublica && msg.tokenpublica!=""){
					
					
					if(msg.respuesta==1){
						jQuery('.tablaTokenChatTelegramAlertas').css('display', 'none');
						jQuery('.tablaTokenChatTelegramCorrecto').css('display', 'table');
						
					}
					else{
						if(msg.respuesta==2){
							jQuery('.tablaTokenChatTelegramAlertas').html('You can not activate more websites with this token');
						}
						else if(msg.respuesta==3){
							jQuery('.tablaTokenChatTelegramAlertas').html('Activation code has expired');
						}
						else if(msg.respuesta==0){
							jQuery('.tablaTokenChatTelegramAlertas').html('Activation code incorrect');
						}
					}
					
					
					actionTelegramChatAJAX("statusTokenTelegram", msg.respuesta, msg.fecha, msg.id);
					
				}
			}
			
			
		}
		
		websocket_telegramChat.onclose = function(){
			//se vuelve a conectar
			console.log("on close");
			
		}
		websocket_telegramChat.onerror = function(evt){
			console.log("on error");
			console.log(evt);
		}
	}
	
	//se inicia nuevamente
	connect_xtra();
}

function actionTelegramChatAJAX(tipo, dato1, dato2, dato3, dato4, dato5) {
					
	var data= new Array();
	var data = {  type: 'save', action: 'telegramChat_iajax_save', "ssid_telegramChat_673": tipo, dato1: dato1, dato2: dato2, dato3:dato3, dato4:dato4, dato5:dato5 }
		
	jQuery.post(ajaxurl, data, function(response) {
		var obj = jQuery.parseJSON(response);
				
		if(obj.success){
			if(obj.module=="statusUserTelegram"){
				
			}
			else if(obj.module=="statusTokenTelegram"){
				if(dato1==0){
					jQuery('#tablaIngresarTockenNuevo').css('display','table');
					
				}
			}
		}
		else{
			alert("Something went wrong. " + response);
		}
	})
	.fail(function(){
		
	});               

}
