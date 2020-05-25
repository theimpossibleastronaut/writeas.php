<?php
declare(strict_types=1);

namespace writeas;

class Post
{
	public $id;
	public $slug;
	public $appearance;
	public $language;
	public $rtl;
	public $created;
	public $updated;
	public $title;
	public $body;
	public $tags;
	public $views;
	public $token;

	protected $context;

	function __construct( Context $context ) {
		$this->context = $context;
	}

	/**
	 * @param  string $id 	the post id to retrieve.
	 */
	public function get( string $id ) {
		$url = "/posts/" . $id;
		$response = $this->context->request( $url );
		$this->context->updateObject( $this, $response );
	}

	/**
	 * Saves a post. If a token is present then it updates the post.
	 * You are responsible to store the token returned by this call
	 * to be able to update the post.
	 */
	public function save( ?Collection $collection = null ) {
		$req = $this->context->buildRequest(
			$this,
			"body", "title", "font", "lang", "rtl", "token"
		);

		$url = "/posts";

		if ( !is_null( $collection ) ) {
			$url = "/collections/" . $collection->alias . "/posts";
		}

		if ( !empty( $this->id ) && !empty( $this->token ) ) {
			$url .= "/" . $this->id;
		}

		$response = $this->context->request( $url, $req );
		$this->context->updateObject( $this, $response );
	}

	/**
	 * Delete the given post. The token must be provided.
	 */
	public function delete( ) {
		if ( !empty( $this->id ) && !empty( $this->token ) ) {
			$url = "/posts/" . $this->id . "?token=" . $this->token;
			$response = $this->context->request( $url );
		}
	}

}
?>
