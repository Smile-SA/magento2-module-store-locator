<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Block\Adminhtml\Retailer;

use Magento\Backend\Block\AbstractBlock;
use Magento\Backend\Block\Context;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\StoreLocator\Block\Adminhtml\Retailer\SpecialOpeningHours\Container\Renderer;

/**
 * Special Opening Hours rendering block.
 */
class SpecialOpeningHours extends AbstractBlock
{
    public function __construct(
        Context $context,
        private FormFactory $formFactory,
        private Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        return $this->getForm()->toHtml();
    }

    /**
     * Get retailer.
     */
    private function getRetailer(): ?RetailerInterface
    {
        return $this->registry->registry('current_seller');
    }

    /**
     * Create the form containing the virtual rule field.
     */
    private function getForm(): Form
    {
        $form = $this->formFactory->create();
        $form->setHtmlId('special_opening_hours');

        $openingHoursFieldset = $form->addFieldset(
            'special_opening_hours',
            [
                'name' => 'special_opening_hours',
                'label' => __('Special Opening Hours'),
                'container_id' => 'special_opening_hours',
            ]
        );

        if ($this->getRetailer() && $this->getRetailer()->getData('special_opening_hours')) {
            $openingHoursFieldset->setSpecialOpeningHours($this->getRetailer()->getData('special_opening_hours'));
        }

        /** @var Renderer|AbstractElement $openingHoursRenderer */
        $openingHoursRenderer = $this->getLayout()->createBlock(Renderer::class);
        $openingHoursFieldset->setRenderer($openingHoursRenderer->setForm($form));

        return $form;
    }
}
