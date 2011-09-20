<div class="column">
	<h3><?= @text($column_title); ?></h3>
	<ul class="list">
	<?php foreach($photos as $photo): ?>
		<li>
			<a href="<?= @route('view=photo&id='.$photo->id); ?>"><?= @helper('image.photo', array('photo' => $photo->image,'size' => 's')) ?></a>
			<p><?= $photo->title; ?></p>
			<br clear="all" />
		</li>
	<?php endforeach; ?>
	</ul>
</div>