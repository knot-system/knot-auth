<?php

if( ! $core ) exit;

// https://indieauth.spec.indieweb.org/#access-token-verification-request

$query = $core->route->get('query');

header("Content-type: application/json");

$introspect_token = $query['token'];
$hashed_introspect_token = get_hash($introspect_token);

$access_token_cache = new Cache( 'access_token', $hashed_introspect_token, true );
$data = $access_token_cache->get_data();

if( ! $data ) {
	echo json_encode( [ 'active' => false ] );
	exit;
}

$data = json_decode($data, true);

if( $data['access_token'] != $introspect_token ) {
	echo json_encode( [ 'active' => false ] );
	exit;
}

$expire_timestamp = $data['expire_timestamp'];

if( $expire_timestamp-time() < 0 ) {
	echo json_encode( [ 'active' => false ] );
	exit;
}

$me = $data['returned_me'];

$response = [
	'active' => true,
	'me' => $me,
	'client_id' => $data['client_id'],
	'scope' => $data['scope'],
	'exp' => $data['expire_timestamp'],
	'iat' => $data['issued_timestamp'],
];

echo json_encode( $response );
exit;
