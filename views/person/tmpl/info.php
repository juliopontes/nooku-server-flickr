<?php defined('KOOWA') or die('Restricted access'); ?>
<a target="_blank" href="<?= $person->profileurl->_content; ?>"><?= $person->realname->_content; ?></a>
<?= $person->location->_content; ?>
<br />
<?= $person->photos->count->_content; ?> photos