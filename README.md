![PHP Composer](https://github.com/theimpossibleastronaut/writeas.php/workflows/PHP%20Composer/badge.svg)
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

# Work in progress

- [ ] Authentication
- [x] Posts
- [ ] Collections
- [ ] Users
