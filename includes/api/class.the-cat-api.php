<?php

namespace AV\TheCatApi;

// Exit if accessed directly
defined("ABSPATH") or die();

use WP_Error;

class API {

	/**
	 * API key
	 *
	 * @var string
	 */
	private $api_key;

	/**
	 * Base URL
	 *
	 * @var string
	 */
	private $url = 'https://api.thecatapi.com/v1/';

	/**
	 * API constructor.
	 *
	 * @param $api_key
	 */
	public function __construct( $api_key ) {
		$this->api_key = $api_key;
	}

	/**
	 *
	 * @return array|WP_Error|null
	 */
	public function get_breeds() {
		$response = $this->request( 'GET', 'breeds' );

		return $response;
	}

	/**
	 * @param $id
	 *
	 * @return array|WP_Error|null
	 */
	public function get_breed( $id ) {
		$response = $this->request( 'GET', 'images/search', [ 'breed_ids' => $id ] );

		return $response;
	}

	/**
	 * @param string $method
	 * @param string $path
	 * @param array $params
	 *
	 * @return array|WP_Error|null
	 */
	public function request( $method, $path, $params = [] ) {
		if ( ! $method ) {
			return null;
		}

		$url = $this->url . $path;
		if ( ! empty( $params ) ) {
			$url = add_query_arg( $params, $url );
		}

		$response = wp_remote_request( $url, [
			'method'  => strtoupper( $method ),
			'headers' => [ 'x-api-key' => $this->api_key ],
		] );

		if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
			$return = json_decode( wp_remote_retrieve_body( $response ), true );
		} else {
			$return = new WP_Error( wp_remote_retrieve_response_code( $response ), wp_remote_retrieve_response_message( $response ) );
		}

		return $return;
	}
}