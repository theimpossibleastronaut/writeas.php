<?php
declare(strict_types=1);

namespace writeas;

class Collection
{
	public $alias;
	public $title;
	public $description;
	public $style_sheet;
	public $email;
	public $totalPosts;
	public $views;

	protected $context;

	function __construct( Context $context ) {
		$this->context = $context;
	}

	public function get( string $alias ) {
		$url = "/collections/" . $alias;
		$response = $this->context->request( $url );
		$this->context->updateObject( $this, $response );
	}

	/**
	 * @param  string $format Default returns an raw text, but if you specify "html" you will get formatted HTML.
	 * @return array Array of Posts
	 */
	public function getPosts( string $format = "" ):array {
		$out = array();

		$url = "/collections/" . $this->alias . "/posts";

		if ( !empty( $format ) && $format === "html" ) {
			$url .= "?body=" . $format;
		}

		$response = $this->context->request( $url );

		if ( isset( $response ) && isset( $response->data ) &&
			 isset( $response->data->posts ) && is_array( $response->data->posts ) )
		{
			foreach ( $response->data->posts as $postdata ) {
				$post = new Post( $this->context );
				$this->context->updateObject( $post, $postdata );
				$out[] = $post;
			}
		}

		return $out;
	}

	public function create( string $title, ?string $alias ){
		if ( !empty( $this->context->getAccessToken() ) ) {
			$url = "/collections";

			$this->title = $title;
			$this->alias = $alias;

			$req = $this->context->buildRequest(
				$this,
				"title", "alias"
			);

			if ( !empty( $this->id ) && !empty( $this->token ) ) {
				$url = "/posts/" . $this->id;
			}

			$response = $this->context->request( $url, $req );
			$this->context->updateObject( $this, $response );
		}
	}

	public function movePost( Post $post ) {
		if ( !empty( $this->context->getAccessToken() ) &&
			 !empty( $post->id ) && !empty( $post->token ) ) {
			$url = "/collections/" . $this->alias . "/collect";

			$obj = new stdClass;
			$obj->id = $post->id;
			$obj->token = $post->token;

			$req = $this->context->buildRequest(
				$obj,
				"id", "token"
			);

			$response = $this->context->request( $url, $req );
			$this->context->updateObject( $this, $response );
		}
	}

}
?>
