<?php

if( ! $core ) exit;

// https://indieauth.spec.indieweb.org/#request

$query = $core->route->get('query');

if( ! empty($query['action']) && $query['action'] == 'revoke' ) {
	// NOTE: backwards compatibility for token revocation

	include( $core->abspath.'system/endpoints/revocation.php' );
	exit;

} elseif( ! empty($query['grant_type']) && $query['grant_type'] == 'refresh_token' ) {
	// refresh token

	include( $core->abspath.'system/endpoints/token/refresh.php' );
	exit;

} elseif( empty($query['token']) ) {
	// generate token

	include( $core->abspath.'system/endpoints/token/generate.php' );
	exit;

} else {
	// verify token

	include( $core->abspath.'system/endpoints/token/verify.php' );
	exit;	

}
