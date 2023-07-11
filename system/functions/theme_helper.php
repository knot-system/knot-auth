<?php


function head_html(){

	global $core;

	$core->theme->print_headers();

	$body_classes = array();

	$color_scheme = get_config('theme-color-scheme');
	if( $color_scheme ) $body_classes[] = 'theme-color-scheme-'.$color_scheme;

	$template = $core->route->get( 'template' );
	if( $template ) $body_classes[] = 'template-'.$template;

?><!DOCTYPE html>
<!--
___________.__                    .__                                       .__       .___                     __   
\_   _____/|__| ______  _  ______ |  |__   ____   ___________  _____   ____ |  |    __| _/____ _____    ______/  |_ 
 |    __)_ |  |/    \ \/ \/ /  _ \|  |  \ /    \_/ __ \_  __ \/     \_/ __ \|  |   / __ |/ __ \\__  \  /     \   __\
 |        \|  |   |  \     (  <_> )   Y  \   |  \  ___/|  | \/  Y Y  \  ___/|  |__/ /_/ \  ___/ / __ \|  Y Y  \  |  
/_______  /|__|___|  /\/\_/ \____/|___|  /___|  /\___  >__|  |__|_|  /\___  >____/\____ |\___  >____  /__|_|  /__|  
	\/         \/                  \/     \/     \/            \/     \/           \/    \/     \/      \/   
-->
<html lang="en">
<head>
<?php
	$core->theme->print_metatags( 'header' );
?>


<?php
	$core->theme->print_stylesheets();
?>

<?php
	$core->theme->print_scripts();

	?>
	
</head>
<body<?= get_class_attribute($body_classes) ?>><?php

}

function foot_html(){

	global $core;

	$core->theme->print_metatags( 'footer' );
?>

<?php
	$core->theme->print_scripts( 'footer' );

?>


</body>
</html>
<?php
}
