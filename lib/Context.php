<?php
declare(strict_types=1);

namespace writeas;

/**
 * Communication layer. Has basic responsability for communicating with the instance.
 * Allows to easily build request and handle responses. Contexts are passed to objects,
 * this allows you to work with different contexts.
 */
class Context
{
	protected $endpoint;
	protected $ch;

	/**
	 * @var string $endpoint	Optional endpoint, defaults to DEFAULT_ENDPOINT
	 */
	function __construct( string $endpoint = null ) {
		$this->endpoint = $endpoint;

		if ( is_null( $this->endpoint ) || empty( $this->endpoint ) ) {
			$this->endpoint = DEFAULT_ENDPOINT;
		}
	}

	/**
	 * Tries to request communication with the instance defined in this Context.
	 * If $postdata is omitted, a GET request will be issued. If $postdata is specified
	 * then it will transform into POST.
	 *
	 * @var string $url	The appended url to communicate with. F.e. /posts/<id>
	 * @var string $postdata	Postdata as string to communicate, most likely this is
	 *							a string created by json_encode
	 * @return mixed	null if nothing to report. json object if valid response.
	 */
	public function request( string $url, ?string $postdata = null ) {

		$this->ch = curl_init( $this->endpoint . $url );

		curl_setopt( $this->ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $this->ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $this->ch, CURLOPT_AUTOREFERER, 1 );

		if ( !empty( $postdata ) ) {
			curl_setopt( $this->ch, CURLOPT_POST, 1 );
			curl_setopt( $this->ch, CURLOPT_POSTFIELDS, $postdata );
			curl_setopt( $this->ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		}

		$output = curl_exec( $this->ch );

		curl_close( $this->ch );

		if ( $output !== false && !empty( $output ) ) {
			$js = @json_decode( $output );
			if ( !empty( $js ) ) {
				return $js;
			}

			throw new \Exception( "Failed to decode response" );
		}

		return null;
	}

	/**
	 * Builds a request out of an object with the specified arguments.
	 * It will create a json request of non empty values and does some type
	 * casting for you.
	 *
	 * @var stdClass $obj	Object to check arguments on
	 * @var $arguments	List of strings that contain keynames on the object
	 *					that you'd possibly want to include in the request.
	 * @return string	json encoded string
	 */
	public function buildRequest( $obj, ...$arguments ):string {
		$out = "";
		$json = new \stdClass;

		foreach ( $arguments as $argument ) {
			if ( !empty( $obj->$argument ) ) {
				if ( $obj instanceof \DateTime) {
					$json->$argument = $obj->$argument->format( "Y-m-d\TH:i:s\Z" );
				} else {
					$json->$argument = $obj->$argument;
				}
			}
		}

		$out = json_encode( $json );

		return $out;
	}

	/**
	 * Updates an object with properties from a received json object.
	 * This is useful for synchronising state with the result you just
	 * received from the server.
	 *
	 * @var stdClass $obj	Object to work on
	 * @var stdClass $json	Response object with data property containing
	 *						possible new values.
	 */
	public function updateObject( $obj, $json ):void {
		if ( !is_null( $json ) && isset( $json->data ) ) {
			foreach ( $json->data as $keyName => $keyValue ) {
				if ( $keyName === "created" || $keyName === "updated" ) {
					if ( !empty( $keyValue ) ) {
						$keyValue = \DateTime::createFromFormat( "Y-m-d\TH:i:sT", $keyValue );
					}
				}

				$obj->$keyName = $keyValue;
			}
		}
	}
}
?>
