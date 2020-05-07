<?php
declare(strict_types=1);

namespace writeas;

class Collection
{
	public $alias;
	public $title;
	public $description;
	public $styleSheet;
	public $email;
	public $totalPosts;

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

}
?>
