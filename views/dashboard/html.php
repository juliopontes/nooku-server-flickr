<?php
class ComFlickrViewDashboardHtml extends ComFlickrViewHtml
{
    public function display()
    {
        KRequest::set('get.hidemainmenu', 0);

        return parent::display();
    }
}