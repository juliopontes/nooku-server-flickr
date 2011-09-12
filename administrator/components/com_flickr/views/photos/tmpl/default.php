<?php defined('KOOWA') or die('Restricted access'); ?>
<?php foreach($photos as $photo): ?>
	<?= $photo->title; ?>
	<?= $photo->img; ?>
	<br />
<?php endforeach; ?>