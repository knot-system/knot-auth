<?php

if( ! $core ) exit;

// https://indieauth.spec.indieweb.org/#token-revocation

$query = $core->route->get('query');

// NOTE: this always sends a status_code 200 without content

header("Content-type: application/json");

if( empty($query['token']) ) exit;

$token = $query['token'];

$hashed_token = get_hash($token);

$token_cache = new Cache( 'access_token', $hashed_token, true );
$data = $token_cache->get_data();

if( ! $data ) {

	// NOTE: token could also be a refresh_token

	$token_cache = new Cache( 'refresh_token', $hashed_token, true );
	$data = $token_cache->get_data();

	if( ! $data ) exit;

}

$data = json_decode( $data, true );

if( ! $data || ! is_array($data) ) exit;

$access_token = $data['access_token'];
$refresh_token = $data['refresh_token'];

$access_token_cache = new Cache( 'access_token', get_hash($access_token), true );
$access_token_cache->remove();

$refresh_token_cache = new Cache( 'refresh_token', get_hash($refresh_token), true );
$refresh_token_cache->remove();

exit;
