<?php

/**
 * HolePunch Session
 *
 * - Session namespace specifically for holepunch data.  Useful primarily for the 
 * addBlockHtml and getBlockHtml functions - see below.
 *
 * @category   Line
 * @package    Line_HolePunch
 * @author     Line <drew@line.uk.com>
 * @copyright  Copyright (c) 2012 Line Digital Limited (http://www.line.uk.com)
 * @license    http://opensource.org/licenses/OSL-3.0  Open Software License (OSL 3.0)
 */
class Line_HolePunch_Model_Session extends Mage_Core_Model_Session_Abstract
{
    /*
     * @var string 
     */
    const SESSION_NAMESPACE = 'holepunch';
    
    public function __construct()
    {
        $namespace = $this->_getSessionNamespace();
        $this->init($namespace);
        Mage::dispatchEvent(self::SESSION_NAMESPACE . '_session_init', array(self::SESSION_NAMESPACE . '_session' => $this)); 
    }
    
    /**
     * Prepare the session namespace based on modules name and store code
     * 
     * @return string
     */
    protected function _getSessionNamespace()
    {
        return self::SESSION_NAMESPACE . '_' . (Mage::app()->getStore()->getWebsite()->getCode());
    }
    
    /**
     * Init  blockhtml member to gurantee it will always be an array and init
     * session
     *
     * @param string $namespace
     * @param string $sessionName
     * @return Mage_Core_Model_Session_Abstract
     */
    public function init($namespace, $sessionName = null)
    {
        $this->_initBlockHtml();
        return parent::init($namespace);
    }
    
    /**
     * Initialise storedBlockHtml member to an empty array
     * 
     * @return Line_HolePunch_Model_Session
     */
    protected function _initBlockHtml()
    {
        $this->setStoredBlockHtml(array());
        return $this;
    }

    /**
     * Add a new html block to the session.  Ensure that block html member 
     * has been initialised properly.  This will always have been done so
     * is merely a sanity check here to ensure array_merge doesnt fall over
     * 
     * @param string $item
     * @return Line_HolePunch_Model_Session
     */
    public function addBlockHtml($item)
    {
        if (! is_array($this->getStoredBlockHtml())) {
            $this->_initBlockHtml();
        }
        $this->setStoredBlockHtml(array_merge($this->getStoredBlockHtml(), $item));
        return $this;
    }
    
    /**
     * Return the stored block html data and clear out so its fresh for next 
     * request/response
     * 
     * @return array
     */
    public function getBlockHtml()
    {
        if (is_null($this->getStoredBlockHtml())) {
            $this->_initBlockHtml();
        }
        $blockHtml = $this->getStoredBlockHtml();
        $this->_initBlockHtml();
        return $blockHtml;
    }
}