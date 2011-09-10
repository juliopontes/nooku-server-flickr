<?php defined('KOOWA') or die('Restricted access'); ?>
<?php if(isset($photo)): ?>
	<h3><?= $photo->title->_content; ?></h3>
	<br />
	<?= @helper('image.photo', $photo); ?>
	<?php if(!empty($photo->description->_content)): ?>
		<p><?= $photo->description->_content; ?></p>
	<?php endif; ?>
	<br />
	<strong>Tags:</strong> <?= @helper('tags.photo', array('photo' => $photo,'separator' => ',', 'html' => '<span>{tag}</span>')); ?>
<?php endif; ?>