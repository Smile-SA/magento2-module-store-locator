<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @copyright 2017 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Block\Adminhtml\Retailer;

use Magento\Framework\Stdlib\DateTime;
use Smile\Retailer\Api\Data\RetailerInterface;

/**
 * Special Opening Hours rendering block
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class SpecialOpeningHours extends \Magento\Backend\Block\AbstractBlock
{
    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    private $formFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * Constructor.
     *
     * @param \Magento\Backend\Block\Context      $context     Block context.
     * @param \Magento\Framework\Data\FormFactory $formFactory Form factory.
     * @param \Magento\Framework\Registry         $registry    Registry.
     * @param array                               $data        Additional data.
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * {@inheritDoc}
     */
    protected function _toHtml()
    {
        return $this->getForm()->toHtml();
    }

    /**
     * Get retailer
     *
     * @return RetailerInterface
     */
    private function getRetailer()
    {
        return $this->registry->registry('current_seller');
    }

    /**
     * Create the form containing the virtual rule field.
     *
     * @return \Magento\Framework\Data\Form
     */
    private function getForm()
    {
        $form = $this->formFactory->create();
        $form->setHtmlId('special_opening_hours');

        $openingHoursFieldset = $form->addFieldset(
            'special_opening_hours',
            ['name' => 'special_opening_hours', 'label' => __('Special Opening Hours'), 'container_id' => 'special_opening_hours']
        );

        if ($this->getRetailer() && $this->getRetailer()->getExtensionAttributes() && $this->getRetailer()->getExtensionAttributes()->getSpecialOpeningHours()) {
            $openingHoursFieldset->setSpecialOpeningHours($this->getRetailer()->getExtensionAttributes()->getSpecialOpeningHours());
        }

        $openingHoursRenderer = $this->getLayout()->createBlock('Smile\StoreLocator\Block\Adminhtml\Retailer\SpecialOpeningHours\Container\Renderer');
        $openingHoursFieldset->setRenderer($openingHoursRenderer->setForm($form));

        return $form;
    }
}
