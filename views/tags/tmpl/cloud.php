<?php defined('KOOWA') or die('Restricted access'); ?>
<?php if(!empty($tags)): ?>
	<ul>
	<?php foreach($tags as $tag): ?>
		<li><?php echo $tag->_content; ?></li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>