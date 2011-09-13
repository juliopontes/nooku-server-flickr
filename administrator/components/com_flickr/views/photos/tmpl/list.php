<?php defined('KOOWA') or die('Restricted access'); ?>

<?php foreach($photos as $photo): ?>
	<a href="<?= @route('index.php?option=com_flickr&view=photo&id='.$photo->id) ?>">
		<span></span>
		<?= $photo->img; ?>
	</a>
<?php endforeach; ?>