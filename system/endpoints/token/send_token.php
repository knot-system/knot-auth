<?php

// NOTE: this is used by token/generate.php & token/refresh.php to create and send a new access_token

if( ! $core ) exit;

if( ! $data ) exit;


$current_timestamp = time();

$access_token_lifetime = $core->config->get( 'access_token_lifetime' );
$access_token = generate_token();

$refresh_token_lifetime = $core->config->get( 'refresh_token_lifetime' );
$refresh_token = generate_token();

$token_type = 'Bearer'; // NOTE: we currently only support the 'Bearer' token_type
$scope = $data['scope'];

$profile = false; // TODO, see https://indieauth.spec.indieweb.org/#profile-information

$data['access_token'] = $access_token;
$data['refresh_token'] = $refresh_token;
$data['token_type'] = $token_type;
$data['issued_timestamp'] = $current_timestamp;
$data['expire_timestamp'] = $current_timestamp+$access_token_lifetime;


$access_token_cache = new Cache( 'access_token', $access_token, false, $access_token_lifetime );
$access_token_cache->add_data( json_encode($data) );

$refresh_token_cache = new Cache( 'refresh_token', $refresh_token, false, $refresh_token_lifetime );
$refresh_token_cache->add_data( json_encode($data) );


$me = $data['returned_me'];


header("Content-type: application/json");

$return = [
	'access_token' => $access_token,
	'token_type' => $token_type,
	'scope' => $scope,
	'me' => $me,
	'expires_in' => $access_token_lifetime,
	//'profile' => $profile,
	'refresh_token' => $refresh_token,
];

echo json_encode( $return );
exit;
