<?php

// NOTE: you can overwrite these options via the config.php in the root folder

return [
	'debug' => false, // show additional information when an error occurs
	'logging' => true, // write logfiles into the /log directory
	'cache_lifetime' =>  60*30, // 30 minutes, in seconds
	'access_token_lifetime' => 60*60*24*31, // 1 month, in seconds
];
