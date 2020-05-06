<?php
declare(strict_types=1);

namespace writeas;

require_once( "../lib/writeas.php" );

$context = new \writeas\Context( "https://localhost:8080/api" );
$post = new Post( $context );
$post->body = "Hello from Writeas.php";
$post->save();

?>