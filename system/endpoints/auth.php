<?php

if( ! $core ) exit;

// https://indieauth.spec.indieweb.org/#authorization-request

$query = $core->route->get('query');
$request_type = $core->route->get('request_type');


$response_type = false;
if( ! empty($query['response_type']) ) $response_type = $query['response_type'];

$grant_type = false;
if( ! empty($query['grant_type']) ) $grant_type = $query['grant_type'];


$client_id = false;
if( ! empty($query['client_id']) ) $client_id = $query['client_id'];

$redirect_uri = false;
if( ! empty($query['redirect_uri']) ) $redirect_uri = $query['redirect_uri'];


// TODO: check $redirect_uri, see https://indieauth.spec.indieweb.org/#redirect-url

// TODO: If the URL scheme, host or port of the redirect_uri in the request do not match that of the client_id, then the authorization endpoint SHOULD verify that the requested redirect_uri matches one of the redirect URLs published by the client, and SHOULD block the request from proceeding if not.

if( $response_type == 'code' ) {

	if( ! empty($query['hash']) ) {

		// user entered their password, check it and redirect to $redirect_uri

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

		if( ! validate_password( $entered_password ) ) {
			snippet( 'header' );
			$core->error( 'unauthorized', 'this is not the correct password', NULL, false );
			snippet( 'footer' );
		}

		unset($entered_password);

		// https://indieauth.spec.indieweb.org/#authorization-response

		$redirect = parse_url($redirect_uri);

		$code = get_hash( uniqid() );
		// NOTE: The code MUST expire shortly after it is issued to mitigate the risk of leaks, and MUST be valid for only one use. A maximum lifetime of 10 minutes is recommended. See OAuth 2.0 Section 4.1.2 for additional requirements on the authorization code.
		$tenminutesinseconds = 10*60; // so we cache the token for a maximum of 10 minutes
		// the cache file is also deleted as soon as we use the token, so it can't be used more than once


		$iss = url(); // TODO: check, what we want to use here -- the client will check $iss like this (from https://indieauth.spec.indieweb.org/#authorization-response ): [check] That the iss parameter in the request is valid and matches the issuer parameter provided by the Server Metadata endpoint during Discovery as outlined in OAuth 2.0 Authorization Server Issuer Identification. Clients MUST compare the parameters using simple string comparison. If the value does not match the expected issuer identifier, clients MUST reject the authorization response and MUST NOT proceed with the authorization grant. For error responses, clients MUST NOT assume that the error originates from the intended authorization server. 

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

	} else {

		// show password field to user

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

		echo '<div class="box">';

			snippet( 'login-information', [ 'data' => $data ] );

			snippet( 'login-form', [ 'hash' => $hash ] );

		echo '</div>';

		snippet( 'footer' );


	}

} elseif( $grant_type == 'authorization_code' ) {

	// https://indieauth.spec.indieweb.org/#request

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

	if( ! $data ) {
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


	header("Content-type: application/json");

	$return = [
		'me' => $core->config->get('me')
	];

	echo json_encode( $return );


}
