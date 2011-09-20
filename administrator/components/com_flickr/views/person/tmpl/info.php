<?php defined('KOOWA') or die('Restricted access'); ?>
<?php if(isset($person)): ?>
	<a target="_blank" href="<?= $person->profileurl; ?>"><?= $person->realname; ?></a>
	<?= $person->location; ?>
	<br />
	<?= $person->photos->count; ?> photos
<?php endif; ?>