<?php
declare(strict_types=1);

namespace writeas;

class User
{
	public $username;
	public $has_pass;
	public $email;
	public $created;
	public $subscription;

	protected $context;

	function __construct( Context $context ) {
		$this->context = $context;
	}

	public function getPosts( string $format = "" ):?array {
		if ( !empty( $this->context->getAccessToken() ) ) {
			$out = array();

			$url = "/me/posts";

			if ( !empty( $format ) && $format === "html" ) {
				$url .= "?body=" . $format;
			}

			$response = $this->context->request( $url );

			if ( isset( $response ) && isset( $response->data ) )
			{
				foreach ( $response->data as $postdata ) {
					$post = new Post( $this->context );
					$this->context->updateObject( $post, $postdata );
					$out[] = $post;
				}
			}

			return $out;
		}

		return null;
	}

	public function getCollections( ):?array {
		if ( !empty( $this->context->getAccessToken() ) ) {
			$out = array();

			$url = "/me/collections";

			$response = $this->context->request( $url );

			if ( isset( $response ) && isset( $response->data ) )
			{
				foreach ( $response->data as $collectiondata ) {
					$collection = new Collection( $this->context );
					$this->context->updateObject( $collection, $collectiondata );
					$out[] = $collection;
				}
			}

			return $out;
		}

		return null;
	}

	public function getChannels( ):?array {
		if ( !empty( $this->context->getAccessToken() ) ) {
			$out = array();

			$url = "/me/channels";

			$response = $this->context->request( $url );

			if ( isset( $response ) && isset( $response->data ) )
			{
				foreach ( $response->data as $channeldata ) {
					$channel = new Channel( $this->context );
					$this->context->updateObject( $channel, $channeldata );
					$out[] = $channel;
				}
			}

			return $out;
		}

		return null;
	}

}
?>
