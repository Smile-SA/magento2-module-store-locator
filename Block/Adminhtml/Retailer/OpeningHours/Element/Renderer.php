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

use DateTime;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory as FormElementFactory;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\Form\Element\Text as FormElementText;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\Stdlib\DateTime as MagentoDateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface;
use Smile\StoreLocator\Api\Data\TimeSlotsInterface;

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
     * @var FormElementFactory
     */
    protected FormElementFactory $elementFactory;

    /**
     * @var AbstractElement
     */
    protected AbstractElement $element;

    /**
     * @var AbstractElement|FormElementText
     */
    protected AbstractElement|FormElementText $input;

    /**
     * @var string
     */
    protected $_template = 'retailer/openinghours/element.phtml';

    /**
     * @var JsonSerializer
     */
    private JsonSerializer $jsonSerializer;

    /**
     * @var TimezoneInterface
     */
    private TimezoneInterface $date;

    /**
     * Block constructor.
     *
     * @param Context               $context        Templating context.
     * @param FormElementFactory    $elementFactory Form element factory.
     * @param JsonSerializer        $jsonSerializer JSON Serializer
     * @param TimezoneInterface     $date
     * @param array                 $data           Additional data.
     */
    public function __construct(
        Context $context,
        FormElementFactory $elementFactory,
        JsonSerializer $jsonSerializer,
        TimezoneInterface $date,
        array $data = []
    ) {
        $this->elementFactory = $elementFactory;
        $this->jsonSerializer = $jsonSerializer;
        $this->date           = $date;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function render(AbstractElement $element): string
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
    public function getElement(): AbstractElement
    {
        return $this->element;
    }

    /**
     * Retrieve element unique container id.
     *
     * @return string
     */
    public function getHtmlId(): string
    {
        return $this->input->getHtmlId();
    }

    /**
     * Render HTML of the element using the opening hours engine.
     *
     * @return string
     */
    public function getInputHtml(): string
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
    public function getJsonValues(): string
    {
        $values = $this->getValues();

        return $this->jsonSerializer->serialize($values);
    }

    /**
     * Retrieve element values
     *
     * @throws \Exception
     * @return array
     */
    private function getValues(): array
    {
        $values = [];
        if ($this->element->getValue()) {
            foreach ($this->element->getValue() as $timeSlot) {
                $date      = new DateTime($this->date->date()->format(MagentoDateTime::DATE_PHP_FORMAT));
                $startTime = $date->setTimestamp(strtotime($timeSlot->getStartTime()))->format(MagentoDateTime::DATETIME_PHP_FORMAT);
                $endTime   = $date->setTimestamp(strtotime($timeSlot->getEndTime()))->format(MagentoDateTime::DATETIME_PHP_FORMAT);
                $values[]  = [$startTime, $endTime];
            }
        }

        return $values;
    }
}
