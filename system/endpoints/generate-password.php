<?php

if( ! $core ) exit;

$query = $core->route->get('query');

$endpoint = $core->route->get('endpoint');

?>
<h2>Hash a new password:</h2>
<form action="<?= url($endpoint) ?>" method="POST">
	<p><label>Password: <input name="pw" type="password" autofocus></label></p>
	<p><button>show hashed password</button></p>
</form>
<?php

if( ! empty( $query['pw'] ) ) {

	?>
	<h2>hashed password:</h2>
	<input type="text" value="<?= hash_password($query['pw']) ?>" style="width: 500px;" onClick="this.setSelectionRange(0, this.value.length)">
	<?php

}
