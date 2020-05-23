<?php
// Call script: php authenticate.php myusername mypassword
declare(strict_types=1);

namespace writeas;

require_once( "../lib/writeas.php" );

$context = new \writeas\Context();
$user = $context->authenticate( $argv[1], $argv[2] );
var_dump( $context->getAccessToken() );

// Authenticate with a previously saved token:
// $user = $context->authenticateWithToken( 'xxxxxxxx-xxx-xxxx-xxxx-xxxxxxxxxxxx' );

// Logging out permanently destroys your access token.
$context->logout();
?>
