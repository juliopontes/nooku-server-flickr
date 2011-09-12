<?php defined('KOOWA') or die('Restricted access'); ?>

<div id="sidebar">
	<h3><?= @text('owner'); ?></h3>
	<ul>
		<li><?= @text('user'); ?>: <?= $photo->owner['username']; ?></li>
		<?php if(!empty($photo->owner['realname'])): ?>
			<li><?= @text('realname'); ?>: <?= $photo->owner['realname']; ?></li>
		<?php endif; ?>
		<?php if(!empty($photo->owner['location'])): ?>
			<li><?= @text('location'); ?>: <?= $photo->owner['location']; ?></li>
		<?php endif; ?>
	</ul>
	<h3><?= @text('tags'); ?></h3>
	<ul>
	<?php foreach($photo->tags as $tag): ?>
		<li><?= $tag ?></li>
	<?php endforeach;?>
	</ul>
</div>
<div class="-koowa-box-flex">
	<?= $photo->img; ?>
	<p><?= $photo->description; ?></p>
</div>