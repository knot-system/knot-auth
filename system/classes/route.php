<?php


class Route {

	public $route;

	function __construct() {

		global $core;

		if( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$request_type = 'post';
		} else if( $_SERVER['REQUEST_METHOD'] === 'GET' ) {
			$request_type = 'get';
		} else {
			$core->error( 'invalid_request', 'unknown request method', null, null, $_SERVER['REQUEST_METHOD'] );
		}


		$request = $_SERVER['REQUEST_URI'];
		$request = preg_replace( '/^'.preg_quote($core->basefolder, '/').'/', '', $request );

		$query_string = false;

		$request = explode( '?', $request );
		if( count($request) > 1 ) $query_string = $request[1];
		$request = $request[0];

		$request = explode( '/', $request );

		$query = $_REQUEST;

		$endpoint = '404';

		if( ! empty($request[0]) && file_exists( $core->abspath.'system/endpoints/'.$request[0].'.php' ) ){
			$endpoint = $request[0];
		}


		$this->route = array(
			'endpoint' => $endpoint,
			'request_type' => $request_type,
			'request' => $request,
			'query' => $query
		);
		
		return $this;
	}

	function get( $name = false ) {

		if( $name ) {

			if( ! is_array($this->route) ) return false;

			if( ! array_key_exists($name, $this->route) ) return false;

			return $this->route[$name];
		}

		return $this->route;
	}
	
}
