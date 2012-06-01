<?php

/**
 * HolePunch Base Helper
 *
 * @category   Line
 * @package    Line_HolePunch
 * @author     Line <drew@line.uk.com>
 * @copyright  Copyright (c) 2012 Line Digital Limited (http://www.line.uk.com)
 * @license    http://opensource.org/licenses/OSL-3.0  Open Software License (OSL 3.0)
 */
class Line_HolePunch_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @var string
     * 
     * Xml store config path for active/inactive state flag
     */
    const XML_STORE_CONFIG_PATH_ACTIVE = 'dev/holepunch/active';
    
    /**
     * @var string
     * 
     * Xml store config path for jQuery inclusion flag
     */
    const XML_STORE_CONFIG_PATH_USE_JQUERY = 'dev/holepunch/use_jquery';
    
    /**
     * @var string
     * 
     * Config path - list of blocks that will be holepunched
     */
    const XML_GLOBAL_CONFIG_PATH_BLOCKS = 'global/holepunch/blocks';
    
    /**
     * @var string 
     * 
     * Template used for placeholder elements - will be an empty div and its
     * id will be the name of the block it replaces
     */
    const DEFAULT_PLACEHOLDER_TEMPLATE = 'holepunch/placeholders/default.phtml';

    /**
     * Is the module active
     * 
     * @return bool
     */
    public function isActive()
    {
        if (! Mage::getStoreConfigFlag(self::XML_STORE_CONFIG_PATH_ACTIVE)) {
            return false;
        }
        if (! parent::isModuleOutputEnabled($this->_getModuleName())) {
            return false;
        }
        return true;
    }
    
    /**
     * Check if we are in the admin area - no holepunching should occur in that
     * area
     * 
     * @return bool
     */
    public function isAdmin()
    {
        if (Mage::app()->getStore()->isAdmin() 
            || Mage::getDesign()->getArea() == 'adminhtml') {
            return true;
        }
        return false;
    }
	
    /**
     * Should we be using holepunch for the given request?
     *
     * - This is determined by the conditions in this method:
     *   - Primarily depends on the context of request i.e. xmlHttp, 
     *     https, admin/frontend etc
     *
     * @param bool $isXmlHttp | if the context request is coming from is 
     *                          important then it should be passed in
     * @return bool
     */
    public function shouldProcess($isXmlHttp = null)
    {
        $request = Mage::app()->getRequest();

        if (! $this->isActive()) {
            return false;
        }
        
        if (! is_null($isXmlHttp)) {
            if ($isXmlHttp && !$request->isXmlHttpRequest()) {
                return false;
            }
            if (!$isXmlHttp && $request->isXmlHttpRequest()) {
                return false;
            }
        }
        if (Mage::getSingleton('holepunch/path_manager')->isCurrentPathExcluded()) {
            return false;
        }
        if ($request->isSecure()) {
            return false;
        }
        if ($this->isAdmin()) {
            return false;
        }
        return true;
    }

    /**
     * Parse the admin config settings (comma seperated list) to get the block
     * names that the module will serve via ajax
     *
     * @return mixed array
     */
    public function getBlockNames()
    {
        $blockNames = Mage::getConfig()->getNode(self::XML_GLOBAL_CONFIG_PATH_BLOCKS)->asArray();
        
        $preparedBlocks = array();
        foreach($blockNames as $block) {
            $preparedBlocks[] = $block['name'];
        }
        return $preparedBlocks;
    }
    
    /**
     * If there is a placeholder template file specified in global config for
     * the passed in block name then we want to use that, otherwise use the 
     * default placeholder.
     * 
     * @param string $blockName
     * @return string 
     */
    public function getBlockPlaceholder($blockName)
    {
        $blocks = Mage::getConfig()->getNode(self::XML_GLOBAL_CONFIG_PATH_BLOCKS)->asArray();
        foreach($blocks as $block) {
            if ($block['name'] == $blockName) {
                if (isset($block['placeholder'])) {
                    return $block['placeholder'];
                }
                break;
            }
        }
        return self::getDefaultPlaceholder();
    }
    
    /**
     * Return the default placeholder template
     *  
     * @return string
     */
    public function getDefaultPlaceholder()
    {
        return self::DEFAULT_PLACEHOLDER_TEMPLATE;
    }
    
    /**
     * @return string
     */
    public function getBlockConfigPath()
    {
        return self::XML_GLOBAL_CONFIG_PATH_BLOCKS;
    }
}