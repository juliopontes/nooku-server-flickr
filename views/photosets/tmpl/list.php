<?php defined('KOOWA') or die('Restricted access'); ?>

<?php if(!empty($photosets)): ?>
<ul>
<?php foreach($photosets as $photoset): ?>
	<li>
		<?php echo $photoset->title->_content; ?>
		<p><?php echo $photoset->description->_content; ?></p>
	</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>