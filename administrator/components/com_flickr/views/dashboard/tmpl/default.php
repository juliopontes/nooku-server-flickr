<?php defined('KOOWA') or die('Restricted access'); ?>

<?= @template('admin::com.flickr.view.photo.info', array('photo' => KFactory::tmp('admin::com.flickr.model.photos')->set('photo_id','5517132356')->getInfo()->getItem())); ?>