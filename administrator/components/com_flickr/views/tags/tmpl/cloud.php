<?php defined('KOOWA') or die('Restricted access'); ?>
<?php if(is_object($tags)): ?>
	<ul>
	<?php foreach($tags as $tag): ?>
		<li><?php echo $tag; ?></li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>