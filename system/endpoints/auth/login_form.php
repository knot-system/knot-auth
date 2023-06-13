<?php

if( ! $core ) exit;

// https://indieauth.spec.indieweb.org/#authorization-request

$query = $core->route->get('query');


$client_id = false;
if( ! empty($query['client_id']) ) $client_id = $query['client_id'];

$redirect_uri = false;
if( ! empty($query['redirect_uri']) ) $redirect_uri = $query['redirect_uri'];

if( ! verify_redirect_uri( $redirect_uri, $client_id ) ){
	snippet('header');
	$core->error( 'unauthorized_redirect_uri', 'this redirect_uri is not allowed (host must match client_id, or redirect_uri must be published by client_id)', NULL, false );
	snippet('footer');
	exit;
}


$response_type = false;
if( ! empty($query['response_type']) ) $response_type = $query['response_type'];

$state = false;
if( ! empty($query['state']) ) $state = $query['state'];

$code_challenge = false;
if( ! empty($query['code_challenge']) ) $code_challenge = $query['code_challenge'];

$code_challenge_method = false;
if( ! empty($query['code_challenge_method']) ) $code_challenge_method = $query['code_challenge_method'];

$scope = false;
if( ! empty($query['scope']) ) $scope = $query['scope'];

$me = false;
if( ! empty($query['me']) ) $me = $query['me'];

if( $me ) $me = normalize_url($me);


if( ! $redirect_uri ) {
	snippet('header');
	$core->error( 'missing_redirect_uri', 'the redirect_uri is missing', NULL, false );
	snippet('footer');
	exit;
}

if( ! $state ) {
	snippet('header');
	$core->error( 'missing_state', 'the state is missing', NULL, false );
	snippet('footer');
	exit;
}

if( ! $code_challenge ) {
	// NOTE: we require a code_challenge (and later a code_verifier), and thus don't support older clients.
	// (from https://indieauth.spec.indieweb.org/#authorization-request : For backwards compatibility, authorization endpoints MAY accept authorization requests without a code challenge if the authorization server wishes to support older clients.)
	snippet('header');
	$core->error( 'missing_code_challenge', 'the code_challenge is missing', NULL, false );
	snippet('footer');
	exit;
}

if( $code_challenge_method != 'S256' ) {
	// NOTE: we currently only support S256 as a code_challenge_method
	snippet('header');
	$core->error( 'unknown_code_challenge_method', 'this code_challenge_method is not supported (we only support S256 at the moment)', NULL, false );
	snippet('footer');
	exit;
}

$data = [
	'response_type' => $response_type,
	'client_id' => $client_id,
	'redirect_uri' => $redirect_uri,
	'state' => $state,
	'code_challenge' => $code_challenge,
	'code_challenge_method' => $code_challenge_method,
	'scope' => $scope,
	'me' => $me
];

// TODO: use $client_id to fetch and display more information
// see https://indieauth.spec.indieweb.org/#client-information-discovery
// add information to $data and use inside login-information snippet
// If the client_id contains the permitted IPv4 and IPv6 addresses 127.0.0.1 or [::1], or if the domain name resolves to these addresses, the authorization endpoint MUST NOT fetch the client_id.

// TODO: use provided $me to show more information, see https://indieauth.spec.indieweb.org/#authorization-request
// add information to $data and use inside login-information snippet

$hash = get_hash( json_encode($data) );

$cache = new Cache( 'auth', $hash, true );
$cache->add_data( json_encode($data) );


snippet( 'header' );

snippet( 'login-information', [ 'data' => $data ] );

snippet( 'login-form', [ 'hash' => $hash ] );

snippet( 'footer' );
