<?php defined('KOOWA') or die('Restricted access'); ?>
<?php if(is_object($person)): ?>
	<a target="_blank" href="<?= $person->profileurl->_content; ?>"><?= $person->realname->_content; ?></a>
	<?= $person->location->_content; ?>
	<br />
	<?= $person->photos->count->_content; ?> photos
<?php endif; ?>