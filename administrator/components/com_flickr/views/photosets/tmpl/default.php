<?php defined('KOOWA') or die('Restricted access'); ?>

<ul>
<?php foreach($photosets as $photoset): ?>
	<li>
		<a href="<?= @route('option=com_flickr&view=photoset&id='.$photoset->id) ?>"><?php echo $photoset->title; ?></a>
		<?php if(!empty($photoset->description)): ?>
			<p><?php echo $photoset->description; ?></p>
		<?php endif; ?>
	</li>
<?php endforeach; ?>
</ul>