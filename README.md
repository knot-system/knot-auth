# Einwohnermeldeamt

A small IndieAuth authorization & token pndpoint, which is part of a larger system called [Homestead](https://github.com/maxhaesslein/homestead). You can install it as a standalone service, or use the Homestead installer, which also installs other modules alongside it.

For now, this allows logging in via password. More methods will follow.

This is currently in very early alpha stage. **Things will break, and are likely to change in the future!**

## Requirements

A default webserver install / shared hosting service _should_ meet all requirements.

- PHP, at least version 8.0
- support for .htaccess files, with mod_rewrite
- write-access to the folder where this service is installed

## Initial Setup

Your server needs to run at least PHP 8.0 or later.

Copy all the files into a directory on your webserver, then open the url to this path in a webbrowser. Follow the installation instructions.

## Additional Identities

you can add additional Identities in the `config.php` file in the root folder. Use the `generate-password` endpoint (for example, https://www.example.com/generate-password/ ) to generate a new password hash. Then add additional identities like this:

```php
<?php

return [
	'users' => [
		[
			'me' => 'https://www.example.com',
			'password' => '$2y$10$...'
		],
		[
			'me' => 'https://other-identity.example.com/',
			'password' => '$2y$10$...'
		]
	]
];

```

When you log in, the `me` URL is set automatically and you need to provide the corresponding password, to log in.
