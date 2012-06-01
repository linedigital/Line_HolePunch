<?php

/**
 * Hole Punch JS Block
 *
 * Handles the javascript that should be displayed on any given page
 * 
 * @category   Line
 * @package    Line_HolePunch
 * @author     Line <drew@line.uk.com>
 * @copyright  Copyright (c) 2012 Line Digital Limited (http://www.line.uk.com)
 * @license    http://opensource.org/licenses/OSL-3.0  Open Software License (OSL 3.0)
 */
class Line_HolePunch_Block_Js extends Mage_Core_Block_Template
{
    /**
     * Should we put the js on page?
     *
     * @return bool
     */
    public function shouldOutput()
    {
        return Mage::helper('holepunch')->shouldProcess(false);
    }

    /**
     * Controller used to serve holepunch requests
     *
     * @return string
     */
    public function getActionUrl()
    {
        return Mage::getUrl('holepunch/index/fill');
    }
	
    /**
     * Each page will have a specific set of handles associated, return
     * these for passing back to the ajax controller so the layout can 
     * be replicated.
     *
     * @return string
     */
    public function getHandles()
    {
        return json_encode(Mage::app()->getLayout()->getUpdate()->getHandles());
    }
	
    /**
     * Similar to the getHandles method - we need to know what models are used in each specific layout
     * Typically will be products, categories etc but can be custom.
     *
     * @return string
     */
    public function getRegistryModelIds()
    {
        $registryMaps = Mage::getSingleton('holepunch/registry_mapper')->getMaps();
        
        $json = '';
        foreach($registryMaps as $map) {
            if (Mage::registry($map['registry_key'])) {
                $json .= "'" . $map['registry_key'] . "' : " . Mage::registry($map['registry_key'])->getId() . ",";
            }
        }
        return $json;
    }

    /**
     * Prepares all required params for the ajax request
     *
     * @return string
     */
    public function getJsonParams()
    {	
        $json = "'handles' : " . $this->getHandles() . ",";
        $json .= $this->getRegistryModelIds();
        return $json;
    }
}