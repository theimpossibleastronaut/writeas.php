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
		var_dump($this);
	}

}
?>