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
class Line_HolePunch_Block_Rewrite_Messages extends Mage_Core_Block_Messages
{
    /**
     * @var string 
     */
    const BLOCK_NAME = 'messages';
    
    /**
     * @return mixed 
     */
    public function getGroupedHtml()
    {
        $groupedHtml = parent::getGroupedHtml();
        
        $helper = Mage::helper('holepunch');
        if (! $helper->shouldProcess()) {
            return $groupedHtml;    
        } elseif (! empty($groupedHtml)) {
            Mage::getSingleton('holepunch/session')->addBlockHtml(array(self::BLOCK_NAME => $groupedHtml));
            $this->setName(self::BLOCK_NAME)
                    ->setContentBlock(self::BLOCK_NAME)
                    ->setTemplate($helper::DEFAULT_PLACEHOLDER_TEMPLATE);
            return $this->renderView();        
        }
    }
}