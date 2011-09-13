<?php defined('KOOWA') or die('Restricted access'); ?>
<style>
span.tag {
	background: none repeat scroll 0 0 #A0CE57;
    border: 1px solid #678F28;
    border-radius: 8px 8px 8px 8px;
    color: #486619;
    display: block;
    float: left;
    font-size: 11px;
    line-height: 11px;
    margin: 0 3px 3px 0;
    padding: 1px 10px 3px;
}
.box {
    font-size: 11px;
    line-height: 0;
    padding: 3px 7px;
    position: relative;
    margin-top: 6px;
}
</style>
<div class="box">
<?php foreach($tags as $tag): ?>
	<span class="tag"><?php echo $tag->name; ?></span>
<?php endforeach; ?>
</div>