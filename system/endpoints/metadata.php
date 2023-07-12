<?php

if( ! $core ) exit;

// https://indieauth.spec.indieweb.org/#indieauth-server-metadata

$indieauth_metadata = array(
	'issuer' => url(),
	'authorization_endpoint' => url('auth'),
	'token_endpoint' => url('token'),
	//'introspection_endpoint' => '', // TODO
	//'introspection_endpoint_auth_methods_supported' => [], // TODO
	'revocation_endpoint' => url('revocation'),
	'revocation_endpoint_auth_methods_supported' => [ 'none' ],
	//'scopes_supported' => [], // TODO
	'response_types_supported' => [ 'code' ],
	'grant_types_supported' => [ 'authorization_code' ],
	'service_documentation' => 'https://indieauth.spec.indieweb.org/',
	'code_challenge_methods_supported' => ['S256'],
	//'authorization_response_iss_parameter_supported' => false, // TODO
	//'userinfo_endpoint' => '' // TODO
);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($indieauth_metadata);
