<?php


function hash_password( $password ) {

	return password_hash( $password, PASSWORD_BCRYPT );
}


// returns false on failure, or 'me' value on success
function validate_password( $submitted_me, $submitted_password ) {
	global $core;

	$users = $core->config->get('users');

	if( ! is_array($users) || empty($users) ) return false;

	foreach( $users as $user ) {

		if( un_trailing_slash_it(normalize_url($user['me'])) != un_trailing_slash_it(normalize_url($submitted_me)) ) continue;

		$hashed_password = $user['password'];
		if( password_verify( $submitted_password, $hashed_password ) ) {
			return $user['me'];
		}
	}

	return false;
}
