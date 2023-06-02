<?php

// NOTE: for now, this is a simple wrapper around password_hash() and password_veriy(), but this may change later.


function hash_password( $password ) {

	return password_hash( $password, PASSWORD_BCRYPT );
}


function validate_password( $password, $hashed_password ) {

	return password_verify( $password, $hashed_password );
}
