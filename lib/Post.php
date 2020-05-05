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

	public function get( string $id ) {
		$url = "/posts/" . $id;
		$response = $this->context->request( $url );
		$this->context->updateObject( $this, $response );
	}

	public function save() {
		$req = $this->context->buildRequest(
			$this,
			"body", "title", "font", "lang", "rtl", "token"
		);

		$url = "/posts";
		if ( !empty( $this->id ) && !empty( $this->token ) ) {
			$url = "/posts/" . $this->id;
		}

		$response = $this->context->request( $url, $req );
		$this->context->updateObject( $this, $response );
	}

	public function delete( ) {
		if ( !empty( $this->id ) && !empty( $this->token ) ) {
			$url = "/posts/" . $this->id . "?token=" . $this->token;
			$response = $this->context->request( $url );
		}
	}

}
?>