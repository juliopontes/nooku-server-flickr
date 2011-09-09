<?php defined('KOOWA') or die('Restricted access'); ?>
<form action="<?= @route() ?>" method="post" id="task-form" class="-koowa-form">
<div class="grid_8">
	<div class="border-radius-4 title clearfix">
		<input class="inputbox border-radius-4 required" type="text" name="api_key" id="api_key" size="40" maxlength="255" value="<?= ComFlickrModelDefault::getConfig('api_key'); ?>" placeholder="<?= @text( 'API KEY' ); ?>" />
	</div>
</div>
</form>