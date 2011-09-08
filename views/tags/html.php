<?php
class ComFlickrViewTagsHtml extends ComFlickrViewHtml
{
    public function display()
    {
        KRequest::set('get.hidemainmenu', 0);

        return parent::display();
    }
}