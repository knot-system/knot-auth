<?php

function generate_pkce_code_challenge( $plaintext, $algo = 'sha256' ) {
	$hash = hash( $algo, $plaintext, true );
	return base64_urlencode( $hash );
}

function base64_urlencode( $string ) {
	$string = base64_encode( $string );
	$string = str_replace( '+/', '-_', $string );
	return rtrim( $string, '=' );
}

function generate_access_token( $length = 256 ) {

	$bytes = random_bytes( $length );
	$access_token = bin2hex( $bytes );

	return $access_token;
}

function verify_redirect_uri( $redirect_uri, $client_id ) {

	// https://indieauth.spec.indieweb.org/#redirect-url	
	
	$redirect_uri_url = parse_url( $redirect_uri );
	$client_id_url = parse_url( $client_id );

	if( ! $redirect_uri_url || ! $client_id_url ) return false;

	$needs_validation = false;


	// NOTE: If a client wishes to use a redirect URL that has a different host than their client_id, or if the redirect URL uses a custom scheme (such as when the client is a native application), then the client will need to explicitly list those redirect URLs so that authorization endpoints can be sure it is safe to redirect users there. The client SHOULD publish one or more <link> tags or Link HTTP headers with a rel attribute of redirect_uri at the client_id URL. Authorization endpoints verifying that a redirect_uri is allowed for use by a client MUST look for an exact match of the given redirect_uri in the request against the list of redirect_uris discovered after resolving any relative URLs.

	if( $redirect_uri_url['scheme'] != 'http' && $redirect_uri_url['scheme'] != 'https' ) {
		$needs_validation = true;
	}

	if( $redirect_uri_url['host'] != $client_id_url['host'] ) {
		$needs_validation = true;
	}

	if( ! $needs_validation ) return true;

	$indieauth = new IndieAuth();
	$indieauth->set_absolute_url( trailing_slash_it($client_id) );
	$endpoints = $indieauth->discover_endpoint( 'redirect_uri', $client_id, true );

	if( empty($endpoints) ) return false;

	return array_key_exists($redirect_uri, $endpoints);

}
