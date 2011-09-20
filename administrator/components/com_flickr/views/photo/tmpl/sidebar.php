<?php defined('KOOWA') or die('Restricted access'); ?>
<style src="media://com_flickr/css/flickr.css" />
<div class="column_info">
	<h3><?= @text('source'); ?></h3>
	<div class="box">
	<p>
		<a href="<?= $photo->url; ?>"><?= str_replace('http://www.flickr.com/photos/','',substr($photo->url,0,-1)); ?></a>
	</p>
	<p>
		<a href="<?= $photo->short_url; ?>">via (flickr.com)</a>
	</p>
	</div>
	
	<h3><?= @text('owner'); ?></h3>
	<div class="box">
		<p><?= $photo->owner['username']; ?></p>
		<?php if(!empty($photo->owner['realname'])): ?>
			<p>(<?= $photo->owner['realname']; ?>)</p>
		<?php endif; ?>
		<?php if(!empty($photo->owner['location'])): ?>
			<p><?= $photo->owner['location']; ?></p>
		<?php endif; ?>
	</div>
	
	<h3><?= @text('taken date'); ?></h3>
	<div class="box">
		<p><?= $photo->taken_date; ?></p>
	</div>
	
	<?php if(!empty($photo->tags)): ?>
	<h3><?= @text('tags'); ?></h3>
	<div class="box">
	<?php foreach($photo->tags as $tag): ?>
		<span class="tag"><?= $tag ?></span>
	<?php endforeach;?>
	</div>
	<?php endif; ?>

	<?php if(!empty($photo->sizes)): ?>
	<h3><?= @text('sizes'); ?></h3>
	<div class="box">
	<?php foreach($photo->sizes as $size): ?>
		<p><?= $size->width; ?> x <?= $size->height; ?> (<a target="_blank" href="<?= $size->url; ?>"><?= @text('url'); ?></a> | <a target="_blank" href="<?= $size->source; ?>"><?= @text('source'); ?></a>)</p>
	<?php endforeach; ?>
	</div>
	<?php endif; ?>
</div>