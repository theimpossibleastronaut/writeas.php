<?php
declare(strict_types=1);

namespace writeas;

class WAException extends \Exception
{
	protected $exceptions = array(
		400 => "Bad Request",		// The request didn't provide the correct parameters, or JSON / form data was improperly formatted.
		401 => "Unauthorized",		// No valid user token was given.
		403 => "Forbidden",			// You attempted an action that you're not allowed to perform.
		404 => "Not Found",			// The requested resource doesn't exist.
		405 => "Method Not Allowed",// The attempted method isn't supported.
		410 => "Gone",				// The entity was unpublished, but may be back.
		429 => "Too Many Requests", // You're making too many requests, especially to the same resource.
		500 => "Server Error",
		502 => "Server Error",
		503 => "Server Error"
	);

	function __construct( $message = null, $code = 0, Exception $previous = null )
	{
		if ( isset( $this->exceptions[ $code ] ) ) {
			$message = $this->exceptions[ $code ];
		}

		parent::__construct($message, $code, $previous);
	}
}
?>
