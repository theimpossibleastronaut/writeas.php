![PHP Composer](https://github.com/theimpossibleastronaut/writeas.php/workflows/PHP%20Composer/badge.svg)
![PHP Stan](https://github.com/theimpossibleastronaut/writeas.php/workflows/PHP%20Stan/badge.svg)
[![License](https://poser.pugx.org/theimpossibleastronaut/writeas.php/license)](https://packagist.org/packages/theimpossibleastronaut/write.as)
[![Latest Stable Version](https://poser.pugx.org/theimpossibleastronaut/writeas.php/v/stable)](https://packagist.org/packages/theimpossibleastronaut/write.as)
[![Latest Unstable Version](https://poser.pugx.org/theimpossibleastronaut/writeas.php/v/unstable)](https://packagist.org/packages/theimpossibleastronaut/write.as)

# writeas.php
Implementation of the write.as API in PHP.

It's very basic, and PHP 7.x oriented, but should work on most versions as it's
only clear dependency is curl.

```php
<?php
require_once( "lib/writeas.php" );
```

Or use composer:

```bash
composer require theimpossibleastronaut/writeas.php
```

```php
<?php
require __DIR__ . '/vendor/autoload.php';
```

Then check one of the examples!

```php
require_once( "../lib/writeas.php" );

$context = new \writeas\Context();
$post = new Post( $context );
$post->body = "Hello from Writeas.php";
$post->save();
```

The Context is your livelyhood for communicating with the instance you specify in its constructor. It handles the building of requests and updating of object instances.

If you work with Anonymous data like Posts, it's important that you save the returned token that you get after initially saving your Post (otherwise you'll be unable to update the post).

Objects like Post or Collection will update automagically after calling a save/get function. For instance, if you ->save a Post, it's token will appear in ->token.

Authentication is done on the Context (use multiple contexts if you want to authenticate multiple users). Upon logging in you should store the access token for future sessions. Logout when needed, and don't keep tokens lying around.

All errors are transformed to catchable WAException objects.

# Supports the following api endpoints (near) completely

- [x] Authentication
- [x] Posts
- [x] Collections
- [x] Users
- [x] Channels
