<?php defined('KOOWA') or die('Restricted access'); ?>
<ul>
<?php foreach($photos->photos->photo as $photo): ?>
	<li>
		<img src="http://farm<?php echo $photo->farm; ?>.static.flickr.com/<?php echo $photo->server; ?>/<?php echo $photo->id; ?>_<?php echo $photo->secret; ?>.jpg" alt="<?php echo $photo->title; ?>" />
		<br />
		<?php echo $photo->title; ?>
	</li>
<?php endforeach; ?>
</ul>