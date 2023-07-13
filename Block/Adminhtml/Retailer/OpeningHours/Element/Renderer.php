<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Block\Adminhtml\Retailer\OpeningHours\Element;

use DateTime;
use Exception;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory as FormElementFactory;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\Form\Element\Text as FormElementText;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\Stdlib\DateTime as MagentoDateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Opening Hours field renderer.
 */
class Renderer extends Template implements RendererInterface
{
    protected AbstractElement $element;
    protected AbstractElement|FormElementText $input;

    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
    protected $_template = 'retailer/openinghours/element.phtml';

    public function __construct(
        Context $context,
        protected FormElementFactory $elementFactory,
        private JsonSerializer $jsonSerializer,
        private TimezoneInterface $date,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    public function render(AbstractElement $element)
    {
        $this->element = $element;
        $this->input   = $this->elementFactory->create('hidden');
        $this->input->setForm($this->getElement()->getForm());

        $inputId = $this->getData("input_id") ?? "opening_hours" . uniqid();

        $this->input->setId($inputId);
        $this->input->setName($element->getName());

        $this->element->addClass("opening-hours-wrapper")->removeClass("admin__control-text");

        return $this->toHtml();
    }

    /**
     * Get currently edited element.
     */
    public function getElement(): AbstractElement
    {
        return $this->element;
    }

    /**
     * Retrieve element unique container id.
     */
    public function getHtmlId(): string
    {
        return $this->input->getHtmlId();
    }

    /**
     * Render HTML of the element using the opening hours engine.
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
     */
    public function getJsonValues(): string
    {
        $values = $this->getValues();

        return $this->jsonSerializer->serialize($values);
    }

    /**
     * Retrieve element values
     *
     * @throws Exception.
     */
    private function getValues(): array
    {
        $values = [];
        if ($this->element->getValue()) {
            foreach ($this->element->getValue() as $timeSlot) {
                $date = new DateTime($this->date->date()->format(MagentoDateTime::DATE_PHP_FORMAT));
                $startTime = $date->setTimestamp(strtotime($timeSlot->getStartTime()))
                    ->format(MagentoDateTime::DATETIME_PHP_FORMAT);
                $endTime = $date->setTimestamp(strtotime($timeSlot->getEndTime()))
                    ->format(MagentoDateTime::DATETIME_PHP_FORMAT);
                $values[]  = [$startTime, $endTime];
            }
        }

        return $values;
    }
}
