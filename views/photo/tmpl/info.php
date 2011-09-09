<?php defined('KOOWA') or die('Restricted access'); ?>

<h3><?= $photo->title->_content; ?></h3>
<?= @helper('image.photo', $photo); ?>
<?php if(!empty($photo->description->_content)): ?>
<p><?= $photo->description->_content; ?></p>
<?php endif; ?>