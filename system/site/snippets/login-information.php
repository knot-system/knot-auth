<?php

// Version: 0.0.1

if( ! $core ) exit;

$data = $args['data'];

$client = $data['client_id'];
$me = $data['me'];

$scopes = explode(' ', $data['scope']);

$redirect_url = $data['redirect_uri'];

?>
<h1>Allow access to your site?</h1>
<p>The app <strong><?= $client ?></strong> would like to access your site, <strong><?= $me ?></strong>.</p>
<p>The app is requesting the following scopes:</p>
<ul>
	<?php
	foreach( $scopes as $scope ) {
		?>
		<li><?= $scope ?></li>
		<?php
	}
	?>
</ul>
<p>You will be redirected to <strong><?= $redirect_url ?></strong> after authorizing this application.</p>
