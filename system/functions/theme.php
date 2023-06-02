<?php

function snippet( $path, $args = array(), $return = false ) {
	
	global $core;

	$snippet_path = 'snippets/'.$path.'.php';

	$include_path = 'system/site/'.$snippet_path;

	if( ! file_exists( $core->abspath.$include_path) ) return;

	ob_start();

	$core->include( $include_path, $args );

	$snippet = ob_get_contents();
	ob_end_clean();

	if( $return === true ) {
		return $snippet;
	}

	echo $snippet;

}
