<?php

/**
 * Hole Punch Index Controller
 *
 * - Handles requests to serve any blocks that have been marked as being served via ajax
 *
 * @category   Line
 * @package    Line_HolePunch
 * @author     Line <drew@line.uk.com>
 * @copyright  Copyright (c) 2012 Line Digital Limited (http://www.line.uk.com)
 * @license    http://opensource.org/licenses/OSL-3.0  Open Software License (OSL 3.0)
 */
class Line_HolePunch_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * @var Line_HolePunch_Helper_Data
     */
    protected $_helper;
    
    public function _construct()
    {
        $this->_helper = Mage::helper('holepunch');
    }

    /**
     * Fill all placeholders with their content.  This includes setting up the 
     * registry and applying necessary layout handles.
     * 
     * @return void
     */
    public function fillAction()
    {
        /**
         * Bail early if for any reason we should not be required to 
         * process this request
         */
        if (! $this->_helper->shouldProcess(true)) {
            return $this;
        }
        
        if ($this->requireFullLoad()) {
            Mage::getSingleton('holepunch/registry_mapper')->prepRegistry();
            $this->prepareAdditionalHandles();
        }
       
        $this->loadLayout();

        $blockHtml = $this->getBlockHtml();
        
        $this->getResponse()
            ->setHeader('Content-Type','text/json')
            ->setBody(json_encode(array('blocks' => $blockHtml)));
    }
    
    /**
     * Apply all additional layout handles required for request.  Will 
     * inlucde all layout handles that were added to the last request 
     * for this same route
     * 
     * @return Line_HolePunch_IndexController
     */
    protected function prepareAdditionalHandles()
    {
        if ($handles = $this->getRequest()->getParam('handles')) {
            $this->getLayout()->getUpdate()->addHandle($handles);
        }
        return $this;
    }
	
    /**
     * PrepareCustomerHandles - apply customer logged in / logged out handles on
     * every request.
     * 
     * @return Line_HolePunch_IndexController
     */
    protected function prepareCustomerHandles()
    {
        $update = $this->getLayout()->getUpdate();
        if (Mage::helper('customer')->isLoggedIn()) {
            $update->removeHandle('customer_logged_out')
                    ->addHandle('customer_logged_in');
        } else {
            $update->removeHandle('customer_logged_in')
                    ->addHandle('customer_logged_out');
        }
        return $this;
    }
    
    /**
     * Generate html blocks from the html blocks found on global config + merge
     * these with any html blocks found in the holepunch session.
     * 
     * @return array
     */
    public function getBlockHtml()
    {  
        $blockHtml = array();
		$blockNames = $this->_helper->getBlockNames();
        foreach($blockNames as $key) {
            if ($block = $this->getLayout()->getBlock($key)) {
                $blockHtml[$key] = $block->toHtml();
            }
        }
        $blockHtml = array_merge(Mage::getSingleton('holepunch/session')->getBlockHtml(), $blockHtml);
		
        return $blockHtml;
    }
    
    /**
     * @return boolean 
     */
    public function requireFullLoad()
    {
        $blocks = Mage::getConfig()->getNode($this->_helper->getBlockConfigPath())->asArray();
        foreach($blocks as $block) {
            if (isset($block['require_full_load'])) {
                return true;
            }
        }
        return false;
    }
}