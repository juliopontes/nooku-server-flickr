<?php
interface ComFlickrModelHttpFilterInterface extends KCommandInterface, KObjectIdentifiable
{
	/**
     * Get the Http object
     *
     * @return  object	The http object
     */
    public function getHttp();
}