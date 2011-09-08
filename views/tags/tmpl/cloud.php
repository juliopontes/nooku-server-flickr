<?php defined('KOOWA') or die('Restricted access'); ?>
<ul>
<?php foreach($tags->hottags->tag as $tag): ?>
	<li><?php echo $tag->_content; ?></li>
<?php endforeach; ?>
</ul>