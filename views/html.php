<?php
class ComFlickrViewHtml extends ComDefaultViewHtml
{
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'views' => array(
                'dashboard' => JText::_('Dashboard'),
                'tags' => JText::_('Tags')
            )
        ));

        parent::_initialize($config);
    }
}