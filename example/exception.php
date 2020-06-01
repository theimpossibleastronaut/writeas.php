<?php
// Query non existing post so you can see how errors are handled.
declare(strict_types=1);

namespace writeas;

require_once( "../lib/writeas.php" );

$context = new \writeas\Context();
$post = new Post( $context );
$post->get( "nonexisting" );

?>
