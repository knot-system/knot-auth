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

	if( $request_type == 'post' && ! empty($query['hash']) ) {

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

		$code = get_hash( uniqid() ); // TODO: The code MUST expire shortly after it is issued to mitigate the risk of leaks, and MUST be valid for only one use. A maximum lifetime of 10 minutes is recommended. See OAuth 2.0 Section 4.1.2 for additional requirements on the authorization code.

		$iss = url(); // TODO: check, what we want to have here

		$redirect['query'] = array(
			'code' => $code,
			'state' => $state,
			'iss' => $iss
		);

		$data['code'] = $code;
		$tenminutesinseconds = 10*60;
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

		snippet( 'header' );

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

		$hash = get_hash( json_encode($data) );

		$cache = new Cache( 'auth', $hash, true );

		$cache->add_data( json_encode($data) );


		// TODO: display this information for the user
		echo '<pre>';
		var_dump($response_type);
		var_dump($client_id);
		var_dump($redirect_uri);
		var_dump($state);
		var_dump($code_challenge);
		var_dump($code_challenge_method);
		var_dump($scope);
		var_dump($me);
		echo '</pre>';

		// TODO: use $client_id to fetch and display more information
		// see https://indieauth.spec.indieweb.org/#client-information-discovery

		// TODO: use provided $me to show more information, see https://indieauth.spec.indieweb.org/#authorization-request


		?>
		<form action="<?= url('auth') ?>" method="POST">

			<label>Password: <input name="password" type="password" autofocus></label>

			<input type="hidden" name="hash" value="<?= $hash ?>">
			<input type="hidden" name="response_type" value="code">

			<button>authorize</button>

		</form>
		<?php

		snippet( 'footer' );

	}

} elseif( $grant_type == 'authorization_code' && $request_type == 'post' ) {

	// https://indieauth.spec.indieweb.org/#request

	$code = false;
	if( isset($query['code']) ) $code = $query['code'];

	$code_verifier = false;
	if( isset($query['code_verifier']) ) $code_verifier = $query['code_verifier'];


	if( ! $code ) {
		snippet('header');
		$core->error( 'missing_code', 'the code is missing', NULL, false );
		snippet('footer');
		exit;
	}


	$cache = new Cache( 'code', $code, true );
	$data = $cache->get_data();
	$cache->remove(); // every code is only valid for one-time use

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

	// TODO: $code_verifier: The original plaintext random string generated before starting the authorization request.
	// TODO: Note that for backwards compatibility, the authorization endpoint MAY allow requests without the code_verifier. If an authorization code was issued with no code_challenge present, then the authorization code exchange MUST NOT include a code_verifier, and similarly, if an authorization code was issued with a code_challenge present, then the authorization code exchange MUST include a code_verifier.


	header("Content-type: application/json");

	$return = [
		'me' => $data['me'], // TODO: this should be from the config, depending on the password provided; TODO: we need to add the 'me' parameter to the config.php, and possible allow multiple me/password combinations.
	];

	echo json_encode( $return );


}
