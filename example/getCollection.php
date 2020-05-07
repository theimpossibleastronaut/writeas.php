<?php
declare(strict_types=1);

namespace writeas;

require_once( "../lib/writeas.php" );

$context = new \writeas\Context();
$collection = new Collection( $context );
$collection->get( "gijsgobje" );

$posts = $collection->getPosts();

foreach ( $posts as $post ) {
	echo "Post " . $post->id. ": " . ( $post->title ?: $post->slug ) . " (" . $post->created->format('Y-m-d') . ")" . PHP_EOL;
}

?>
