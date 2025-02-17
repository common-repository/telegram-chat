<?php

	if($_POST['accion']=="decode"){
		
		//mensajes normales
		if(isset($_POST['telegram_id_enc']) and isset($_POST['msg'])){
			$data['telegram_id_enc']=decrypt($_POST['telegram_id_enc'],$_POST['token']);
			$data['telegram_grupo_enc']=decrypt($_POST['telegram_grupo_enc'],$_POST['token']);
			$data['msg']=$_POST['msg'];
			$data['mensaje_enc']=decrypt($_POST['mensaje'],$_POST['token']);
			
		}
		//historiales
		if(isset($_POST['msg'])){
			$data['msg']=$_POST['msg'];
			$data['i']=$_POST['i'];
			$data['total']=$_POST['total'];
			
			$mensajeFinal="";
			$arr_mensaje=explode('_$*$_', $_POST['mensaje']);
			for($i=0; $i<count($arr_mensaje); $i++){
				if($arr_mensaje[$i]!=""){
					if($i==0){
						if(decrypt($arr_mensaje[$i],$_POST['token'])){
							$mensajeFinal.=decrypt($arr_mensaje[$i],$_POST['token']);
						}
					}
					else{
						if(decrypt($arr_mensaje[$i],$_POST['token'])){
							$mensajeFinal.='_$*$_'.decrypt($arr_mensaje[$i],$_POST['token']);
						}
					}
				}
			}
			$data['mensaje_enc']=$mensajeFinal;
		}
		
		print(json_encode($data));
	}
	else if($_POST['accion']=="encode"){
		$data['mensaje_enc']=encrypt($_POST['mensaje'],$_POST['token']);
		print(json_encode($data));
	}
	
	function encrypt($decrypted, $password, $salt='!kQm*fF3pXe1Kbm%9') { 
		// Build a 256-bit $key which is a SHA256 hash of $salt and $password.
		$key = hash('SHA256', $salt . $password, true);
		// Build $iv and $iv_base64.  We use a block size of 128 bits (AES compliant) and CBC mode.  (Note: ECB mode is inadequate as IV is not used.)
		srand(); $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
		if (strlen($iv_base64 = rtrim(base64_encode($iv), '=')) != 22) return false;
		// Encrypt $decrypted and an MD5 of $decrypted using $key.  MD5 is fine to use here because it's just to verify successful decryption.
		$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $decrypted . md5($decrypted), MCRYPT_MODE_CBC, $iv));
		// We're done!
		return $iv_base64 . $encrypted;
	} 

	function decrypt($encrypted, $password, $salt='!kQm*fF3pXe1Kbm%9') {
		// Build a 256-bit $key which is a SHA256 hash of $salt and $password.
		$key = hash('SHA256', $salt . $password, true);
		// Retrieve $iv which is the first 22 characters plus ==, base64_decoded.
		$iv = base64_decode(substr($encrypted, 0, 22) . '==');
		// Remove $iv from $encrypted.
		$encrypted = substr($encrypted, 22);
		// Decrypt the data.  rtrim won't corrupt the data because the last 32 characters are the md5 hash; thus any \0 character has to be padding.
		$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($encrypted), MCRYPT_MODE_CBC, $iv), "\0\4");
		// Retrieve $hash which is the last 32 characters of $decrypted.
		$hash = substr($decrypted, -32);
		// Remove the last 32 characters from $decrypted.
		$decrypted = substr($decrypted, 0, -32);
		// Integrity check.  If this fails, either the data is corrupted, or the password/salt was incorrect.
		if (md5($decrypted) != $hash) return false;
		// Yay!
		return $decrypted;
	}
?>