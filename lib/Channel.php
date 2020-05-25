<?php
declare(strict_types=1);

namespace writeas;

class Channel
{
	public $id;
	public $url;
	public $name;
	public $username;

	protected $context;

	function __construct( Context $context ) {
		$this->context = $context;
	}
}
?>
