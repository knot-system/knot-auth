<?php

function generate_pkce_code_challenge( $plaintext, $algo = 'sha256' ) {
	$hash = hash( $algo, $plaintext, true );
	return base64_urlencode( $hash );
}

function base64_urlencode( $string ) {
	$string = base64_encode($string);
	$string = str_replace('+/', '-_', $string);
	return rtrim($string, '=' );
}

function generate_access_token( $length = 256 ) {

	$bytes = random_bytes( $length );
	$access_token = bin2hex($bytes);

	return $access_token;
}