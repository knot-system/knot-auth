<?php

if( ! $core ) exit;

// https://indieauth.spec.indieweb.org/#authorization-request

$query = $core->route->get('query');

$response_type = false;
if( ! empty($query['response_type']) ) $response_type = $query['response_type'];

$grant_type = false;
if( ! empty($query['grant_type']) ) $grant_type = $query['grant_type'];


if( $response_type == 'code' ) {

	if( empty($query['hash']) ) {
		// show password field to user

		include( $core->abspath.'system/endpoints/auth/login_form.php' );
		exit;

	} else {
		// user entered their password, check it and redirect to $redirect_uri

		include( $core->abspath.'system/endpoints/auth/redirect.php' );
		exit;

	}

} elseif( $grant_type == 'authorization_code' ) {
	// verify authorization code, return $me on success

	include( $core->abspath.'system/endpoints/auth/authorization_code.php' );
	exit;

} else {

	snippet( 'header' );
	$core->error( 'no_auth_info', 'no response_type or grant_type provided', NULL, false );
	snippet( 'footer' );
	exit;

}
