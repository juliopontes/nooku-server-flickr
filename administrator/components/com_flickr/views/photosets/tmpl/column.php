<div class="column opacity">
	<h3><?= @text($column_title); ?></h3>
	<ul class="list">
	<?php foreach($photosets as $photoset): ?>
		<li>
			<a href="<?= @route('view=photoset&id='.$photoset->id); ?>"><?= @helper('image.photo', array('photo' => $photoset->image,'size' => 's')) ?></a>
			<p><?= $photoset->title; ?></p>
			<br clear="all" />
		</li>
	<?php endforeach; ?>
	</ul>
</div>