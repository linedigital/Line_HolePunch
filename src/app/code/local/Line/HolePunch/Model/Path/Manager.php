<?php

/**
 * HolePunch Path Manager
 *
 *  - Responsible for handling paths in the module.  Primarily, whether a
 * path is to be exluded from any holepunching
 * 
 * @category   Line
 * @package    Line_HolePunch
 * @author     Line <drew@line.uk.com>
 * @copyright  Copyright (c) 2012 Line Digital Limited (http://www.line.uk.com)
 * @license    http://opensource.org/licenses/OSL-3.0  Open Software License (OSL 3.0)
 */
class Line_HolePunch_Model_Path_Manager extends Mage_Core_Model_Abstract
{
    /*
     * @var string
     */
    const XML_CONFIG_PATH = 'global/holepunch/paths';
    
    /*
     * @var Mage_Core_Model_Config_Element
     */
    protected $_config = null;
    
    /*
     * @var array
     */
    protected $_excluded = null;
    
    public function _construct()
    {
        $this->_initConfig(); 
        $this->_initExcluded();
    }
    
    /*
     * Initialise the config node containing path info for holepunch module
     * 
     * @return Line_HolePunch_Model_Path_Manager $this
     */
    protected function _initConfig()
    {
        if (is_null($this->_config)) {
            $this->_config = Mage::getConfig()->getNode(self::XML_CONFIG_PATH);
        }
        return $this;
    }
    
    /*
     * Prepare an array containing all excluded paths set in global config into 
     * an array.
     * 
     * @return Line_HolePunch_Model_Path_Manager $this
     */
    protected function _initExcluded()
    {
        $excluded = $this->getConfig()->excluded->asArray();
        foreach($excluded as $excludedPath) {            
            $this->_excluded[] = $excludedPath['path'];
        }
        return $this;
    }
    
    /*
     * Returnt the global config node containing path info for the holepunch 
     * module
     *  
     * @return Mage_Core_Model_Config_Element $_config
     */
    public function getConfig()
    {
        if (is_null($this->_config)) {
            $this->_initConfig;
        }
        return $this->_config;
    }
    
    /*
     * Return the array containing all excluded paths, as set in config
     * 
     * @return array $excluded
     */
    public function getExcluded()
    {
        if (is_null($this->_excluded)) {
            $this-_initExcluded;
        }
        return $this->_excluded;
    }

    /*
     * Determin whether the current request path should be excluded.
     * 
     * @uses isPathExcluded
     * @return bool
     */
    public function isCurrentPathExcluded()
    {
        $path = $this->getCurrentRequestPath();
        return $this->isPathExcluded($path);
    }
    
    /*
     * For any given $path, determine whether it should be exluded or not.
     * If the $path is found at the start of any of the excluded paths then
     * it should be excluded.
     * 
     * @param string $path
     * @return bool
     */
    public function isPathExcluded($path)
    {
        foreach($this->_excluded as $excluded) {
            if (strpos($path, $excluded) === 0) {
                return true;
            }
        }
        return false;
    }
    
    /*
     * Get the current request path - formatted to remove end slashes and 
     * file extension
     * 
     * @return string
     */
    protected function getCurrentRequestPath()
    {
        $requestPath = trim($this->getRequest()->getOriginalRequest()->getPathInfo(), '/');        
        return preg_replace('/\.[^.]*$/', '', $requestPath);
    }
    
    /*
     * Return the current request object
     * 
     * @return Mage_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        return Mage::app()->getRequest();
    }
}