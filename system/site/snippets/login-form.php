<?php

// Version: 0.0.1

if( ! $core ) exit;

$hash = $args['hash'];

?>
<form action="<?= url('auth') ?>" method="POST">

	<label>Password: <input name="password" type="password" autofocus></label>

	<input type="hidden" name="hash" value="<?= $hash ?>">
	<input type="hidden" name="response_type" value="code">

	<button>authorize</button>

</form>