<?php


function hash_password( $password ) {

	return password_hash( $password, PASSWORD_BCRYPT );
}


function validate_password( $submitted_me, $submitted_password ) {
	global $core;

	$users = $core->config->get('users');

	if( ! is_array($users) || empty($users) ) return false;

	foreach( $users as $user ) {
		if( normalize_url($user['me']) != normalize_url($submitted_me) ) continue;

		$hashed_password = $user['password'];
		return password_verify( $submitted_password, $hashed_password );
	}

	return false;
}
