<?php

// these options are displayed in the 'homestead-control' module

return [
	'theme' => [
		'type' => 'theme',
		'description' => 'you can add more themes in the <code>theme/</code> subfolder',
	],
	'theme-color-scheme' => [
		'type' => 'array',
		'description' => 'not all themes support (all) color schemes',
		'options' => ['default' => 'Default (blue)', 'green' => 'Green', 'red' => 'Red', 'lilac' => 'Lilac'],
	],
	'users' => [
		'type' => 'complex',
		'description' => 'these users are allowed to log in; you can use the <code>generate-password</code> endpoint to get hashed passwords. see <a href="https://github.com/maxhaesslein/einwohnermeldeamt#additional-identities" target="_blank" rel="noopener">Additional Identities</a> in the README for details.'
	]
];
