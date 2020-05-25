<?php
// Call script: php userGetPosts.php myusername mypassword
declare(strict_types=1);

namespace writeas;

require_once( "../lib/writeas.php" );

$context = new \writeas\Context();
$user = $context->authenticate( $argv[1], $argv[2] );
$posts = $user->getChannels();
var_dump($posts);

$context->logout();
?>
