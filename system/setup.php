<?php

// this file creates some basic files and folderstructure and gets called, if important files are missing (like the config.php or .htaccess)


$output = false;
if( isset($_GET['debug']) ) $output = true;


$basefolder = str_replace( 'index.php', '', $_SERVER['PHP_SELF']);

if( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ) $baseurl = 'https://';
else $baseurl = 'http://';
$baseurl .= $_SERVER['HTTP_HOST'];
$baseurl .= $basefolder;


if( file_exists($abspath.'config.php') && file_exists($abspath.'.htaccess') ) {
	if( $output ) {
		?>
		<p>Setup already finished. Please delete <em>config.php</em> or <em>.htaccess</em> from the root directory to re-run the setup.</p>
		<?php
	}
	return;
}



$config_missing = false;

if( ! file_exists($abspath.'config.php') ) {
	$config_missing = true;
	$output = true;
}


if( $output ) {
	?>
	<p>Hi. This is the first-time setup of Einwohnermeldeamt.</p>
	<p>We create some files and folders to get everything up and running.</p>

	<hr>

	<h3>Environment:</h3>
	<ul>
		<li>ABSPATH: <em><?= $abspath ?></em></li>
		<li>BASEFOLDER: <em><?= $basefolder ?></em></li>
		<li>BASEURL: <em><?= $baseurl ?></em></li>
	</ul>
	<?php
}


if( $abspath == '' ) {

	if( $output ) {
		?>
		<p><strong>ERROR:</strong> ABSPATH is empty. we don't know what went wrong. we abort the setup here.</p>
		<?php
	}

	exit;
}
if( $basefolder == '' ) {

	if( $output ) {
		?>
		<p><strong>ERROR:</strong> BASEFOLDER is empty. we don't know what went wrong. we abort the setup here.</p>
		<?php
	}

	exit;
}
if( $baseurl == '' ) {

	if( $output ) {
		?>
		<p><strong>ERROR:</strong> BASEURL is empty. we don't know what went wrong. we abort the setup here.</p>
		<?php
	}

	exit;
}


if( $config_missing ) {
	
	if( empty($_REQUEST['password']) ) {
		?>
		<hr>
		<form action="<?= $baseurl ?>" method="POST">
			<p><label><strong>Me</strong><br><small>the url you identify as</small><br><input type="text" name="me" placeholder="https://www.example.com" required></label></p>
			<p><label><strong>Password</strong><br><small>the password you want to use when using this service to log in</small><br><input type="password" name="password" placeholder="your super secret password" value="" required></label></p>
			<p><button>start installation</button></p>
			<p><small>all fields are required</small></p>
		</form>
		<?php
		exit;
	}
}


if( $output ) {
	?>
	<hr>
	<h3>checking <em>.htaccess</em> file:</h3>
	<ul>
		<li>checking if <em>.htaccess</em> file exists</li>
	<?php
}

if( ! file_exists( $abspath.'.htaccess' ) ) {
	$rewrite_base = $basefolder;
	if( $rewrite_base == '' ) $rewrite_base = '/';

	if( $output ) {
		?>
		<li>file <em>.htaccess</em> does not exist, creating it with rewrite base <em><?= $rewrite_base ?></em></li>
		<?php
	}

	$content = "# BEGIN einwohnermeldeamt\r\n<IfModule mod_rewrite.c>\r\nRewriteEngine on\r\nRewriteBase ".$rewrite_base."\r\n\r\nRewriteRule ^theme/[^/]+/assets/(.*)$ - [L]\r\nRewriteRule ^system/site/assets/(.*)$ - [L]\r\nRewriteRule (^|/)\.(?!well-known\/) index.php [L]\r\nRewriteRule ^theme/(.*) index.php [L]\r\nRewriteRule ^system/(.*) index.php [L]\r\nRewriteRule ^log/(.*) index.php [L]\r\nRewriteRule ^cache/(.*) index.php [L]\r\n\r\nRewriteCond %{REQUEST_FILENAME} !-d\r\nRewriteCond %{REQUEST_FILENAME} !-f\r\nRewriteRule . index.php [L]\r\n</IfModule>\r\n# END einwohnermeldeamt\r\n";
	if( file_put_contents( $abspath.'.htaccess', $content ) === false ) {

		if( $output ) {
			?>
			<li><strong>ERROR:</strong> file <em>.htaccess</em> could not be created. Please check the permissions of the root folder and make sure we are allowed to write to it. we abort the setup here.</li>
			<?php
		}

		exit;

	} else {

		if( $output ) {
			?>
			<li>file <em>.htaccess</em> was successfully created</li>
			<?php
		}

	}
} else {

	if( $output ) {
		?>
		<li>file <em>.htaccess</em> exists; if you need to recreate it, delete it and rerun this setup.</li>
		<?php
	}

}


if( $output ) {
	?>
	</ul>
	<h3>checking <em>cache/</em> folder:</h3>
	<ul>
	<?php
}

if( ! is_dir( $abspath.'cache/') ) {

	if( $output ) {
		?>
		<li>folder <em>cache/</em> does not exist, trying to create it</li>
		<?php
	}

	if( mkdir( $abspath.'cache/', 0774, true ) === false ) {

		if( $debug_output ) {
			?>
			<li><strong>ERROR:</strong> folder <em>cache/</em> could not be created. Please check the permissions of the root folder and make sure we are allowed to write to it. we abort the setup here.</li>
			<?php
		}

		exit;
	} else {

		if( $output ) {
			?>
			<li>folder <em>cache/</em> was created successfully</li>
			<?php
		}

	}
} else {

	if( $output ) {
		?><li>folder <em>cache/</em> already exists, we do not need to create it</li><?php
	}

}


if( $output ) {
	?>
	</ul>

	<h3>creating the <em>config.php</em> file:</h3>
	<ul>
	<?php
}

if( $config_missing ) {

	$me = $_REQUEST['me'];
	$password = $_REQUEST['password'];
	
	include_once( $abspath.'system/functions/cryptography.php' );
	$password = hash_password($password);


	$content = "<?php\r\n\r\nreturn [\r\n	'users' => [\r\n		[\r\n			'me' => '".$me."',\r\n			'password' => '$password'\r\n		]\r\n	]\r\n];\r\n";
	if( file_put_contents( $abspath.'config.php', $content ) === false ) {

		if( $output ) {
			?>
			<li><strong>ERROR:</strong> could not create the file <em>config.php</em>. make sure the folder is writeable. we abort the setup here.</li>
			<?php
		}

		exit;
		
	} else {

		if( $output ) {
			?>
			<li>file <em>config.php</em> created successfully</li>
			<?php
		}

	}
} else {

	if( $output ) {
		?>
		<li>file <em>config.php</em> exists; if you need to recreate it, delete it and rerun this setup.</li>
		<?php
	}

}


if( $output ) {
	?>
	</ul>

	<hr>
	<h3>Setup finished!</h3>
	<p>please <a href="<?= $baseurl ?>">reload this page</a>.</p>
	<hr>
	<?php
	exit;
}

header( 'location: '.$baseurl );
