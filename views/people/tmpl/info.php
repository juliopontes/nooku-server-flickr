<?php defined('KOOWA') or die('Restricted access'); ?>
<a target="_blank" href="<?php echo $response->person->profileurl->_content; ?>"><?php echo $response->person->realname->_content; ?></a>
<?php echo $response->person->location->_content; ?>
<br />
<?php echo $response->person->photos->count->_content; ?> photos