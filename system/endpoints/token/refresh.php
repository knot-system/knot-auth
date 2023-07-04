<?php

if( ! $core ) exit;

// https://indieauth.spec.indieweb.org/#refresh-tokens

$query = $core->route->get('query');

$grant_type = false;
if( ! empty($query['grant_type']) ) $grant_type = $query['grant_type'];

$client_id = false;
if( ! empty($query['client_id']) ) $client_id = $query['client_id'];

$refresh_token = false;
if( isset($query['refresh_token']) ) $refresh_token = $query['refresh_token'];

$scope = false;
if( isset($query['scope']) ) $scope = $query['scope'];


if( ! $grant_type ) {
	$core->error( 'missing_grant_type', 'the grant_type is missing' );
	exit;
}

if( ! $refresh_token ) {
	$core->error( 'missing_refresh_token', 'the refresh_token is missing' );
	exit;
}

if( ! $client_id ) {
	$core->error( 'missing_client_id', 'the client_id is missing' );
	exit;
}

if( $grant_type != 'refresh_token' ) {
	$core->error( 'wrong_grant_type', 'grant_type must be "refresh_token"' );
	exit;	
}

$hashed_token = get_hash($refresh_token);

$refresh_token_cache = new Cache( 'refresh_token', $hashed_token, true );
$data = $refresh_token_cache->get_data();

if( ! $data ) {
	// refresh token not found
	echo json_encode( [ 'active' => false ] );
	exit;
}

$data = json_decode( $data, true );

if( $data['refresh_token'] != $refresh_token ) {
	// wrong refresh token
	echo json_encode( [ 'active' => false ] );
	exit;
}

$client_id_send_cleaned = un_trailing_slash_it(normalize_url($client_id));
$client_id_data_cleaned = un_trailing_slash_it(normalize_url($data['client_id']));

if( $client_id_send_cleaned != $client_id_data_cleaned ) {
	// client_id does not match
	echo json_encode( [ 'active' => false ] );
	exit;
}

$new_scope = $data['scope'];
if( $scope ) {

	// new scope provided, make sure that it has no scope attribute that is not present in the original scope

	$original_scope = $data['scope'];
	$original_scope = explode( ' ', $original_scope );
	$original_scope = array_map('trim', $original_scope);
	$original_scope = array_map('strtolower', $original_scope);

	$provided_scope = explode( ' ', $scope );
	$provided_scope = array_map('trim', $provided_scope);
	$provided_scope = array_map('strtolower', $provided_scope);

	foreach( $provided_scope as $scope_check ) {
		if( ! in_array($scope_check, $original_scope) ) {
			// requested new scope attribute that is not in original scope
			echo json_encode( [ 'active' => false ] );
			exit;
		}
	}

	$new_scope = $scope;

}

// invalidate refresh_token:
$refresh_token_cache->remove();

// invalidate original access_token (if it still exists):
$hashed_original_access_token = get_hash( $data['access_token'] );
$original_access_token_cache = new Cache( 'access_token', $hashed_original_access_token, true );
$original_access_token_cache->remove();


$data['scope'] = $new_scope;


// create and send new access & refresh token:
include( $core->abspath.'system/endpoints/token/send_token.php' );
exit;
