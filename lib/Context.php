<?php
declare(strict_types=1);

namespace writeas;

class Context
{
	protected $endpoint;
	protected $ch;

	function __construct( string $endpoint = null ) {
		$this->endpoint = $endpoint;

		if ( is_null( $this->endpoint ) || empty( $this->endpoint ) ) {
			$this->endpoint = DEFAULT_ENDPOINT;
		}
	}

	public function request( string $url, ?string $postdata = null ) {

		$this->ch = curl_init( $this->endpoint . $url );

		curl_setopt( $this->ch, CURLOPT_RETURNTRANSFER, 1 );

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