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

use Magento\Backend\Block\AbstractBlock;
use Magento\Backend\Block\Context;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime;
use Smile\Retailer\Api\Data\RetailerInterface;

/**
 * Special Opening Hours rendering block
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class SpecialOpeningHours extends AbstractBlock
{
    /**
     * @var FormFactory
     */
    private FormFactory $formFactory;

    /**
     * @var Registry
     */
    private Registry $registry;

    /**
     * Constructor.
     *
     * @param Context       $context     Block context.
     * @param FormFactory   $formFactory Form factory.
     * @param Registry      $registry    Registry.
     * @param array         $data        Additional data.
     */
    public function __construct(
        Context $context,
        FormFactory $formFactory,
        Registry $registry,
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
    protected function _toHtml(): string
    {
        return $this->getForm()->toHtml();
    }

    /**
     * Get retailer
     *
     * @return ?RetailerInterface
     */
    private function getRetailer(): ?RetailerInterface
    {
        return $this->registry->registry('current_seller');
    }

    /**
     * Create the form containing the virtual rule field.
     *
     * @return Form
     */
    private function getForm(): Form
    {
        $form = $this->formFactory->create();
        $form->setHtmlId('special_opening_hours');

        $openingHoursFieldset = $form->addFieldset(
            'special_opening_hours',
            ['name' => 'special_opening_hours', 'label' => __('Special Opening Hours'), 'container_id' => 'special_opening_hours']
        );

        if ($this->getRetailer() && $this->getRetailer()->getSpecialOpeningHours()) {
            $openingHoursFieldset->setSpecialOpeningHours($this->getRetailer()->getSpecialOpeningHours());
        }

        $openingHoursRenderer = $this->getLayout()->createBlock('Smile\StoreLocator\Block\Adminhtml\Retailer\SpecialOpeningHours\Container\Renderer');
        $openingHoursFieldset->setRenderer($openingHoursRenderer->setForm($form));

        return $form;
    }
}
