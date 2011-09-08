<?php defined('KOOWA') or die('Restricted access'); ?>

<?= @template('admin::com.flickr.view.tags.cloud', array('tags' => KFactory::tmp('admin::com.flickr.model.tags')->getHotList())); ?>

<?= @template('admin::com.flickr.view.person.info', array('person' => KFactory::tmp('admin::com.flickr.model.people')->set('user_id','39269070@N03')->getInfo())); ?> 