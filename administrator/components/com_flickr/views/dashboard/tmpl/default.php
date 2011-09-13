<?php defined('KOOWA') or die('Restricted access'); ?>

<?= @template('admin::com.flickr.view.photos.list', array('photos' => KFactory::tmp('admin::com.flickr.model.interestingness')->getList())); ?>
