<?php

/**
 * Hole Punch Observer
 *
 * - This is the first stage of a two stage process.
 *   
 *   After all blocks have been generated, we need to pick out those that 
 *   contain dynamic content and replace them with an empty placeholder dom 
 *   element.  The next stage uses an ajax call to then generate those
 *   blocks and replace the placeholder element with the correct content.
 *
 * @category   Line
 * @package    Line_HolePunch
 * @author     Line <drew@line.uk.com>
 * @copyright  Copyright (c) 2012 Line Digital Limited (http://www.line.uk.com)
 * @license    http://opensource.org/licenses/OSL-3.0  Open Software License (OSL 3.0)
 */
class Line_HolePunch_Model_Observer
{
    /**
     * Iterate over a list of blocks that contain dynamic content.  
     * If the block exists in the current layout object, them replace 
     * their content with an empty placeholder
     * 
     * Observes controller_action_layout_generate_blocks_after
     * 
     * @param Varien_Event_Observer $observer
     * @return Line_HolePunch_Model_Observer
     */
    public function generateBlocksAfter(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('holepunch');
        
        if (! $helper->shouldProcess(false)) {
            return $this;
        }
        
        $layout = $observer->getEvent()->getLayout();
        
        $blockNames = $helper->getBlockNames();
        foreach($blockNames as $blockName) {
            if ($block = $layout->getBlock($blockName)) {
                $placeholder = $helper->getBlockPlaceholder($blockName);
                $block->setContentBlock($blockName)->setTemplate($placeholder);
            }
        }
        return $this;
    }
}