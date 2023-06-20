<?php

if( ! $core ) exit;

// https://indieauth.spec.indieweb.org/#authorization-request

$query = $core->route->get('query');

$hash = $query['hash'];

$cache = new Cache( 'auth', $hash, true );

$data = $cache->get_data();

if( ! $data ) {
	snippet( 'header' );
	$core->error( 'internal_error', 'could not retreive data', NULL, false );
	snippet( 'footer' );
}

$data = json_decode($data, true);

$response_type = false;
if( ! empty($data['response_type']) ) $response_type = $data['response_type'];

$client_id = false;
if( ! empty($data['client_id']) ) $client_id = $data['client_id'];

$redirect_uri = false;
if( ! empty($data['redirect_uri']) ) $redirect_uri = $data['redirect_uri'];

$state = false;
if( ! empty($data['state']) ) $state = $data['state'];

$code_challenge = false;
if( ! empty($data['code_challenge']) ) $code_challenge = $data['code_challenge'];

$code_challenge_method = false;
if( ! empty($data['code_challenge_method']) ) $code_challenge_method = $data['code_challenge_method'];

$scope = false;
if( ! empty($data['scope']) ) $scope = $data['scope'];

$me = false;
if( ! empty($data['me']) ) $me = $data['me'];


if( $response_type != 'code' || ! $redirect_uri || ! $state ) {
	snippet( 'header' );
	$core->error( 'internal_error', 'could not retreive data', NULL, false );
	snippet( 'footer' );	
}


$entered_password = $query['password'];
unset($query['password']);

$returned_me = validate_password( $me, $entered_password );
if( ! $returned_me ) {
	snippet( 'header' );
	$core->error( 'unauthorized', 'this is not the correct user or password', NULL, false );
	snippet( 'footer' );
}

$data['returned_me'] = $returned_me;

unset($entered_password);

// https://indieauth.spec.indieweb.org/#authorization-response

$redirect = parse_url($redirect_uri);

$code = get_hash( uniqid() );
// NOTE: The code MUST expire shortly after it is issued to mitigate the risk of leaks, and MUST be valid for only one use. A maximum lifetime of 10 minutes is recommended. See OAuth 2.0 Section 4.1.2 for additional requirements on the authorization code.
$tenminutesinseconds = 10*60; // so we cache the token for a maximum of 10 minutes
// the cache file is also deleted as soon as we use the token, so it can't be used more than once


$iss = url();

$redirect['query'] = array(
	'code' => $code,
	'state' => $state,
	'iss' => $iss
);

$data['code'] = $code;
$code_cache = new Cache( 'code', $code, true, $tenminutesinseconds );
$code_cache->add_data(json_encode($data));

$redirect = build_url($redirect);

header( 'Location: '.$redirect );
exit;
