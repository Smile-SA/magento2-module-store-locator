<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\Retailer
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Block\Adminhtml\Retailer\OpeningHours\Element;

use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Stdlib\DateTime;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface;
use Smile\StoreLocator\Api\Data\TimeSlotsInterface;
use Zend_Date;

/**
 * Opening Hours field renderer
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 *
 * @category Smile
 * @package  Smile\Retailer
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class Renderer extends Template implements RendererInterface
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $elementFactory;

    /**
     * @var AbstractElement
     */
    protected $element;

    /**
     * @var \Magento\Framework\Data\Form\Element\Text
     */
    protected $input;

    /**
     * @var string
     */
    protected $_template = 'retailer/openinghours/element.phtml';

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Magento\Framework\Locale\Resolver
     */
    private $localeResolver;

    /**
     * Block constructor.
     *
     * @param \Magento\Backend\Block\Template\Context      $context        Templating context.
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory Form element factory.
     * @param \Magento\Framework\Json\Helper\Data          $jsonHelper     Helper for JSON
     * @param \Magento\Framework\Locale\Resolver           $localeResolver Locale Resolver
     * @param array                                        $data           Additional data.
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Locale\Resolver $localeResolver,
        array $data = []
    ) {
        $this->elementFactory = $elementFactory;
        $this->jsonHelper     = $jsonHelper;
        $this->localeResolver = $localeResolver;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function render(AbstractElement $element)
    {
        $this->element = $element;
        $this->input   = $this->elementFactory->create('hidden');
        $this->input->setForm($this->getElement()->getForm());

        $inputId = $this->getData("input_id") !== null ? $this->getData("input_id") : "opening_hours" . uniqid();

        $this->input->setId($inputId);
        $this->input->setName($element->getName());

        $this->element->addClass("opening-hours-wrapper")->removeClass("admin__control-text");

        return $this->toHtml();
    }

    /**
     * Get currently edited element.
     *
     * @return AbstractElement
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * Retrieve element unique container id.
     *
     * @return string
     */
    public function getHtmlId()
    {
        return $this->input->getHtmlId();
    }

    /**
     * Render HTML of the element using the opening hours engine.
     *
     * @return string
     */
    public function getInputHtml()
    {
        if ($this->element->getValue()) {
            $this->input->setValue($this->getJsonValues());
        }

        return $this->input->toHtml();
    }

    /**
     * Retrieve element values in Json.
     *
     * @return string
     */
    public function getJsonValues()
    {
        $values = $this->getValues();

        return $this->jsonHelper->jsonEncode($values);
    }

    /**
     * Retrieve element values
     *
     * @return array
     */
    private function getValues()
    {
        $values = [];
        if ($this->element->getValue()) {
            foreach ($this->element->getValue() as $timeSlot) {
                $date      = new Zend_Date();
                $date->setLocale($this->localeResolver->getLocale());
                $startTime = $date->setTime($timeSlot->getStartTime())->toString(DateTime::DATETIME_INTERNAL_FORMAT);
                $endTime   = $date->setTime($timeSlot->getEndTime())->toString(DateTime::DATETIME_INTERNAL_FORMAT);
                $values[]  = [$startTime, $endTime];
            }
        }

        return $values;
    }
}
