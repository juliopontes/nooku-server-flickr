<?php
/**
 * @version     $Id: abstract.php 1919 2010-04-25 20:49:47Z johanjanssens $
 * @category    Koowa
 * @package     Koowa_Database
 * @subpackage  Behavior
 * @copyright   Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Abstract Template Filter
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Template
 * @subpackage  Filter
 */
abstract class ComFlickrModelHttpFilterAbstract extends KObject implements ComFlickrModelHttpFilterInterface
{
    /**
     * The behavior priority
     *
     * @var KIdentifierInterface
     */
    protected $_priority;
    
    /**
     * HTTP object
     *
     * @var object
     */
    protected $_http;
    
    /**
     * Constructor.
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct( KConfig $config = null) 
    { 
        parent::__construct($config);
        
        $this->_priority = $config->priority;
    }
    
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'priority'   => KCommand::PRIORITY_NORMAL,
        ));

        parent::_initialize($config);
    }
    
    /**
     * Get the object identifier
     * 
     * @return  KIdentifier 
     * @see     KObjectIdentifiable
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }
    
    /**
     * Get the priority of a behavior
     *
     * @return  integer The command priority
     */
    public function getPriority()
    {
        return $this->_priority;
    }
    
    /**
     * Get the http object
     *
     * @return  object	The http object
     */
    public function getHttp()
    {
        return $this->_http;
    }
        
    /**
     * Command handler
     * 
     * @param   string      The command name
     * @param   object      The command context
     * @return  boolean     Always returns TRUE
     */
    final public function execute( $name, KCommandContext $context) 
    {
        //Set the template
        $this->_http = $context->caller;
        
        //Set the data
        $data = $context->data;

        if(($name & ComFlickrModelHttpFilter::MODE_READ) && $this instanceof ComFlickrModelFilterRead) {
            $this->read($data);
        }
        
        //Get the data
        $context->data = $data;
        
        //Reset the http
        $this->_http = null;
        
        //@TODO : Allows filters to return false and halt the filter chain
        return true;
    }
    
    /**
     * Method to extract key/value pairs out of a string with xml style attributes
     *
     * @param   string  String containing xml style attributes
     * @return  array   Key/Value pairs for the attributes
     */
    protected function _parseAttributes( $string )
    {
        $result = array(); 
        
        if(!empty($string))
        {
            $attr   = array();

            preg_match_all( '/([\w:-]+)[\s]?=[\s]?"([^"]*)"/i', $string, $attr );

            if (is_array($attr))
            {
                $numPairs = count($attr[1]);
                for($i = 0; $i < $numPairs; $i++ ) {
                     $result[$attr[1][$i]] = $attr[2][$i];
                }   
            }
        }
            
        return $result;
    }
}