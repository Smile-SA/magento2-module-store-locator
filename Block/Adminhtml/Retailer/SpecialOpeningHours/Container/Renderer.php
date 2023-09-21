<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Block\Adminhtml\Retailer\SpecialOpeningHours\Container;

use DateTime;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory as FormElementFactory;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\Stdlib\DateTime as MagentoDateTime;
use Smile\StoreLocator\Block\Adminhtml\Retailer\OpeningHours\Element\Renderer as ElementRenderer;

/**
 * Special Opening Hours fieldset renderer.
 */
class Renderer extends AbstractFieldArray
{
    private AbstractElement $element;

    public function __construct(
        Context $context,
        private FormElementFactory $elementFactory,
        private JsonSerializer $jsonSerializer,
        array $data = []
    ) {

        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->addColumn('date', ['label' => 'Date']);
        $this->addColumn('opening_hours', ['label' => __('Special Opening Hours')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Special Opening Hours');

        parent::_construct();
    }

    /**
     * @inheritdoc
     */
    public function render(AbstractElement $element)
    {
        $this->element = $element;

        if ($element->getSpecialOpeningHours()) {
            $element->setValue($this->parseValuesToArray($element->getSpecialOpeningHours()));
        }

        $this->element->addClass("special-opening-hours-container-fieldset");

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
        return $this->getElement()->getContainer()->getHtmlId();
    }

    /**
     * @inheritdoc
     */
    public function renderCellTemplate($columnName)
    {
        if ($columnName == 'date' && isset($this->_columns[$columnName])) {
            return $this->renderDateColumn($columnName);
        }

        if ($columnName == 'opening_hours' && isset($this->_columns[$columnName])) {
            return $this->renderOpeningHoursColumn($columnName);
        }

        return parent::renderCellTemplate($columnName);
    }

    /**
     * @inheritdoc
     */
    protected function _afterToHtml($html)
    {
        // Wrap this container into a parent div when rendering
        // Mainly used to have propoer binding via the Ui Component
        $htmlId = $this->getHtmlId();

        return "<div id=\"{$htmlId}\">{$html}</div>";
    }

    /**
     * Render The "Date" Column.
     */
    private function renderDateColumn(string $columnName): string
    {
        $element = $this->elementFactory->create('date');
        $element->setFormat($this->_localeDate->getDateFormatWithLongYear())
            ->setForm($this->getForm())
            ->setDisabled(false)
            ->setValue('')
            ->setName($this->_getCellInputElementName($columnName))
            ->setHtmlId($this->_getCellInputElementId('<%- _id %>', $columnName))
            ->addClass("smile-special-opening-hours-datepicker");

        $this->appendDatePickerConfiguration($element);

        return $element->getElementHtml();
    }

    /**
     * Render "Special Opening Hours" Column.
     */
    private function renderOpeningHoursColumn(string $columnName): string
    {
        $input = $this->elementFactory->create('text');
        $input->setForm($this->getElement()->getForm());

        /** @var ElementRenderer $elementRenderer */
        $elementRenderer = $this->getLayout()->createBlock(ElementRenderer::class);
        $elementRenderer->setData('input_id', $this->_getCellInputElementId('<%- _id %>', $columnName));

        $input->setName($this->_getCellInputElementName($columnName));
        $input->setRenderer($elementRenderer);

        return $input->toHtml();
    }

    /**
     * Apply date picker on an element.
     * Mandatory since Magento does this with x-magento-init tag
     * which is NOT triggered when adding a field into array dynamically.
     */
    private function appendDatePickerConfiguration(AbstractElement $element): void
    {
        $inputId = $element->getHtmlId();
        $calendarConfig = $this->jsonSerializer->serialize([
            'dateFormat'  => $element->getFormat(),
            'showsTime'   => !empty($element->getTimeFormat()),
            'timeFormat'  => $element->getTimeFormat(),
            'buttonImage' => $element->getImage(),
            'buttonText'  => 'Select Date',
            'disabled'    => $element->getDisabled(),
        ]);

        // Class toggle on change() is mandatory to have the Mutation Observer working properly.
        // Since jquery Ui Datepicker value appliance is made with val(), this does not trigger changes on DOM.
        $datePickerJsInit = <<<JAVASCRIPT
            <script type="text/javascript">
                require(["jquery", "calendar"],
                    function($, calendar) {
                        $("#$inputId").calendar($calendarConfig);
                        $("#$inputId").change(function() { $("#$inputId").toggleClass("updated-datepicker");});
                    }
                );
            </script>
JAVASCRIPT;

        $element->setAfterElementHtml($datePickerJsInit);
    }

    /**
     * Parse Values to proper array-renderer compatible format.
     */
    private function parseValuesToArray(array $values): array
    {
        $arrayValues = [];

        if (!empty($values)) {
            ksort($values);

            foreach ($values as $date => $timeSlots) {
                $timeRanges = [];

                foreach ($timeSlots as $timeSlot) {
                    $timeDate   = new DateTime();
                    $startTime = $timeDate->setTimestamp(strtotime($timeSlot->getStartTime()))
                        ->format(MagentoDateTime::DATETIME_PHP_FORMAT);
                    $endTime   = $timeDate->setTimestamp(strtotime($timeSlot->getEndTime()))
                        ->format(MagentoDateTime::DATETIME_PHP_FORMAT);
                    $timeRanges[] = [$startTime, $endTime];
                }

                $date = new DateTime($date);
                $arrayValues[] = [
                    'date' => $date->format(MagentoDateTime::DATETIME_PHP_FORMAT),
                    'opening_hours' => $this->jsonSerializer->serialize(array_filter($timeRanges)),
                ];
            }
        }

        return $arrayValues;
    }
}
