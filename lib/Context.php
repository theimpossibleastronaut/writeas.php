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
	protected $endpoint = null;
	protected $ch = null;
	protected $accessToken = null;
	protected $currentUser = null;

	public function getAccessToken():?string { return $this->accessToken; }
	public function getCurrentUser():?User { return $this->currentUser; }

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
	public function request( string $url, ?string $postdata = null, ?string $customRequest = null ) {

		$this->ch = curl_init( $this->endpoint . $url );

		$headers = array('Content-Type: application/json');
		if ( isset( $this->accessToken ) && !empty( $this->accessToken ) ) {
			$headers[] = 'Authorization: Token ' . $this->accessToken;
		}

		curl_setopt( $this->ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $this->ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $this->ch, CURLOPT_AUTOREFERER, 1 );
		curl_setopt( $this->ch, CURLOPT_HTTPHEADER, $headers );

		if ( !empty( $postdata ) ) {
			curl_setopt( $this->ch, CURLOPT_POST, 1 );
			curl_setopt( $this->ch, CURLOPT_POSTFIELDS, $postdata );
		}

		if ( !empty( $customRequest ) ) {
			curl_setopt( $this->ch, CURLOPT_CUSTOMREQUEST, $customRequest );
		}

		$output = curl_exec( $this->ch );
		$statusCode = curl_getinfo( $this->ch, CURLINFO_HTTP_CODE );

		curl_close( $this->ch );

		if ( (int) $statusCode >= 400 ) {
			throw new WAException( null, $statusCode );
		}

		if ( $output !== false && !empty( $output ) ) {
			$js = @json_decode( $output );
			if ( !empty( $js ) ) {
				if ( isset( $js->code ) && isset( $js->error_msg ) ) {
					if ( (int) $js->code >= 400 ) {
						throw new WAException( null, (int) $js->code );
					}
				}
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
	 * @var mixed $arguments	List of strings that contain keynames on the object
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
		if ( !is_null( $json ) ) {
			$target = $json;
			if ( isset( $json->data ) ) {
				$target = $json->data;
			}

			foreach ( $target as $keyName => $keyValue ) {
				if ( $keyName === "created" || $keyName === "updated" ) {
					if ( !empty( $keyValue ) ) {
						$keyValue = \DateTime::createFromFormat( "Y-m-d\TH:i:sT", $keyValue );
					}
				}

				if ( $keyName === "collection" ) {
					$collection = new Collection( $this );
					$this->updateObject( $collection, $keyValue );
					$keyValue = $collection;
				}

				$obj->$keyName = $keyValue;
			}
		}
	}

	/**
	 * Authenticate the user and provide access tokens.
	 * @param  string $alias    login name
	 * @param  string $password password
	 */
	public function authenticate( string $alias, string $password ):?User {
		$url = "/auth/login";
		$req = new \stdClass;
		$req->alias = $alias;
		$req->pass = $password;

		$response = $this->request( $url, json_encode( $req ) );

		if ( isset( $response ) && isset( $response->data ) ) {
			if ( isset( $response->data->access_token ) ) {
				$this->accessToken = $response->data->access_token;
			}

			if ( isset( $response->data->user ) ) {
				$user = new User( $this );
				$this->updateObject( $user, $response->data->user );

				$this->currentUser = $user;

				return $user;
			}
		}

		return null;
	}

	/**
	 * Authenticates with a previous token.
	 * This call only returns the username, so if you need email be sure to
	 * store it somewhere when calling authenticate.
	 * @param  string $token the access token you retrieved earlier
	 */
	public function authenticateWithToken( string $token ):?User {
		$this->accessToken = $token;

		$url = "/me";
		$response = $this->request( $url );

		if ( isset( $response ) && isset( $response->data ) ) {
			$user = new User( $this );
			$this->updateObject( $user, $response->data );

			$this->currentUser = $user;
			return $user;
		}

		return null;
	}

	/**
	 * Logout the current user if set
	 */
	public function logout():void {
		if ( isset( $this->accessToken ) && !empty( $this->accessToken ) ) {
			$url = "/auth/me";
			$response = $this->request( $url, null, "DELETE" );
		}
	}
}
?>
