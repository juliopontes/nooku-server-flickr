<?php defined('KOOWA') or die('Restricted access'); ?>

<?= @template('admin::com.flickr.view.tags.cloud', array('tags' => KFactory::tmp('admin::com.flickr.model.tags')->set('user_id','39269070@N03')->getHotList())); ?>

<?= @template('admin::com.flickr.view.person.info', array('person' => KFactory::tmp('admin::com.flickr.model.people')->set('user_id','39269070@N03')->getInfo())); ?>

<?= @template('admin::com.flickr.view.photo.info', array('photo' => KFactory::tmp('admin::com.flickr.model.photos')->set('photo_id','5517132356')->getInfo())); ?>