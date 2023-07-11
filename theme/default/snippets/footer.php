<?php

// Version: 0.1.1

if( ! $core ) exit;

?>
	</div>
</main>

<footer>
	<a href="https://github.com/maxhaesslein/einwohnermeldeamt" target="_blank" rel="noopener">Einwohnermeldeamt</a> v.<?= $core->version() ?> / <?= $core->theme->get('name') ?> v.<?= $core->theme->get('version') ?>
</footer>

<?php
foot_html();
