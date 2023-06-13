<?php

if( ! $core ) exit;

// https://indieauth.spec.indieweb.org/#request

$query = $core->route->get('query');

if( empty($query['token']) ) {
	// generate token

	include( $core->abspath.'system/endpoints/token/generate.php' );
	exit;

} else {
	// verify token

	include( $core->abspath.'system/endpoints/token/verify.php' );
	exit;	

}
