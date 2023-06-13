<?php

if( ! $core ) exit;

// https://indieauth.spec.indieweb.org/#authorization-request

$query = $core->route->get('query');

$response_type = false;
if( ! empty($query['response_type']) ) $response_type = $query['response_type'];

$client_id = false;
if( ! empty($query['client_id']) ) $client_id = $query['client_id'];

$redirect_uri = false;
if( ! empty($query['redirect_uri']) ) $redirect_uri = $query['redirect_uri'];

// TODO: check $redirect_uri, see https://indieauth.spec.indieweb.org/#redirect-url

// TODO: If the URL scheme, host or port of the redirect_uri in the request do not match that of the client_id, then the authorization endpoint SHOULD verify that the requested redirect_uri matches one of the redirect URLs published by the client, and SHOULD block the request from proceeding if not.
// If a client wishes to use a redirect URL that has a different host than their client_id, or if the redirect URL uses a custom scheme (such as when the client is a native application), then the client will need to explicitly list those redirect URLs so that authorization endpoints can be sure it is safe to redirect users there. The client SHOULD publish one or more <link> tags or Link HTTP headers with a rel attribute of redirect_uri at the client_id URL.
// Authorization endpoints verifying that a redirect_uri is allowed for use by a client MUST look for an exact match of the given redirect_uri in the request against the list of redirect_uris discovered after resolving any relative URLs.

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

// TODO: use provided $me to show more information, see https://indieauth.spec.indieweb.org/#authorization-request
// add information to $data and use inside login-information snippet

$hash = get_hash( json_encode($data) );

$cache = new Cache( 'auth', $hash, true );
$cache->add_data( json_encode($data) );


snippet( 'header' );

snippet( 'login-information', [ 'data' => $data ] );

snippet( 'login-form', [ 'hash' => $hash ] );

snippet( 'footer' );
