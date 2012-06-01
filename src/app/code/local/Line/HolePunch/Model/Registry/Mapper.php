<?php

/**
 * HolePunch Registry Mapper
 *
 *  - Responsible for mapping registry values between requests and ensuring that 
 * all required objects are available for the layout to load successfully
 * 
 * @category   Line
 * @package    Line_HolePunch
 * @author     Line <drew@line.uk.com>
 * @copyright  Copyright (c) 2012 Line Digital Limited (http://www.line.uk.com)
 * @license    http://opensource.org/licenses/OSL-3.0  Open Software License (OSL 3.0)
 */
class Line_HolePunch_Model_Registry_Mapper extends Mage_Core_Model_Abstract
{
    /*
     * @var string
     */
    const MAPS_XML_CONFIG_PATH = 'global/holepunch/registry/maps';
    
    /*
     * @var array
     */
    private $_config = null;
   
    
    public function _construct()
    {
        $this->_initConfig();
    }
    
    /*
     * 
     * @return h_HolePunch_Model_Registry_Mapper
     */
    protected function _initConfig()
    {
        if (is_null($this->_config)) {
           $this->_config = Mage::getConfig()->getNode(self::MAPS_XML_CONFIG_PATH)->asArray();
        }
        return $this;
    }
    
    /*
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
     * 
     * @return array
     */
    public function getMaps()
    {
        return $this->_config;
    }
    
    /*
     * @param string $key
     * @return mixed bool|string
     */
    public function getClassAlias($key)
    {
        foreach($this->_config as $map) {
            if ($map['registry_key'] == $key) {
                return $map['class_alias'];
            }
        }
        return false;
    }
    
    /*
     * Prepare the resgistry with any requried objects and their associated ids
     * 
     * @return Line_HolePunch_Model_Registry_Mapper
     */
    public function prepRegistry()
    {
        $request = Mage::app()->getRequest();
        
        $registryMaps = $this->getMaps();
        
        foreach($registryMaps as $map) {
            if ($modelId = $request->getParam($map['registry_key'])) {
                $classAlias = $this->getClassAlias($map['registry_key']);
                Mage::register($map['registry_key'], Mage::getModel($classAlias)->load($modelId));
            }
        }
        return $this;
    }
}