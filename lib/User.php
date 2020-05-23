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
}
?>
