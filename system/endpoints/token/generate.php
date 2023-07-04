<?php

if( ! $core ) exit;

// https://indieauth.spec.indieweb.org/#access-token-response

$query = $core->route->get('query');

$response_type = false;
if( ! empty($query['response_type']) ) $response_type = $query['response_type'];

$grant_type = false;
if( ! empty($query['grant_type']) ) $grant_type = $query['grant_type'];

$client_id = false;
if( ! empty($query['client_id']) ) $client_id = $query['client_id'];

$redirect_uri = false;
if( ! empty($query['redirect_uri']) ) $redirect_uri = $query['redirect_uri'];


$code = false;
if( isset($query['code']) ) $code = $query['code'];

$code_verifier = false;
if( isset($query['code_verifier']) ) $code_verifier = $query['code_verifier'];
// The original plaintext random string generated before starting the authorization request.


if( ! $code ) {
	snippet('header');
	$core->error( 'missing_code', 'the code is missing', NULL, false );
	snippet('footer');
	exit;
}


$cache = new Cache( 'code', $code, true );
$data = $cache->get_data();
$cache->remove(); // every code is only valid for one-time use, see https://indieauth.spec.indieweb.org/#authorization-response


if( $data ) {
	$data = json_decode( $data, true );
}


if( ! $data || empty($data['code']) || $data['code'] != $code ) {
	snippet('header');
	$core->error( 'unauthorized', 'code not valid', NULL, false );
	snippet('footer');
	exit;
}

if( ! $client_id || $client_id != $data['client_id'] ) {
	snippet('header');
	$core->error( 'unauthorized', 'client_id does not match', NULL, false );
	snippet('footer');
	exit;
}

if( ! $redirect_uri || $redirect_uri != $data['redirect_uri'] ) {
	snippet('header');
	$core->error( 'unauthorized', 'redirect_uri does not match', NULL, false );
	snippet('footer');
	exit;
}

if( empty($data['scope']) ) {
	snippet('header');
	$core->error( 'no_scope', 'no scope found', NULL, false );
	snippet('footer');
	exit;
}


if( ! $code_verifier ) {
	// NOTE: we require a code_challenge and code_verifier, and thus don't support older clients.
	// (from https://indieauth.spec.indieweb.org/#request : Note that for backwards compatibility, the authorization endpoint MAY allow requests without the code_verifier. If an authorization code was issued with no code_challenge present, then the authorization code exchange MUST NOT include a code_verifier, and similarly, if an authorization code was issued with a code_challenge present, then the authorization code exchange MUST include a code_verifier.)
	snippet('header');
	$core->error( 'missing_code_verifier', 'the code_verifier is missing', NULL, false );
	snippet('footer');
	exit;
}


// validate code_verifier / code_challenge
$algo = false;
if( $data['code_challenge_method'] == 'S256' ) {
	$algo = 'sha256';
} else {
	// NOTE: we currently only support S256 as a code_challenge_method
	snippet('header');
	$core->error( 'unknown_code_challenge_method', 'the code_challenge_method is not supported', NULL, false );
	snippet('footer');
	exit;
}

$code_verifier_challenge = generate_pkce_code_challenge( $code_verifier, $algo );

if( $code_verifier_challenge != $data['code_challenge'] ) {
	snippet('header');
	$core->error( 'invalid_code_verifier', 'the code_verifier is invalid', NULL, false );
	snippet('footer');
	exit;
}


unset($data['code']);


// create and send new access & refresh token:
include( $core->abspath.'system/endpoints/token/send_token.php' );
exit;
