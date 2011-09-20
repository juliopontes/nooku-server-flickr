<?php defined('KOOWA') or die('Restricted access'); ?>

<ul class="display">
<?php foreach($photos as $photo): ?>
	<li class="content_block">
		<a href="<?= @route('view=photo&id='.$photo->id) ?>">
			<?= $photo->img; ?>
		</a>
		<h2><?= $photo->title; ?></h2>
		<p><?= $photo->description; ?></p>
	</li>
<?php endforeach; ?>
</ul>