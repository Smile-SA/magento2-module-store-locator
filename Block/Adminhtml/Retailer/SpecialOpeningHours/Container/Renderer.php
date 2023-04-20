<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Block\Adminhtml\Retailer\SpecialOpeningHours\Container;

use DateTime;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory as FormElementFactory;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\Stdlib\DateTime as MagentoDateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface;

/**
 * Special Opening Hours fieldset renderer
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 *
 * @category Smile
 * @package  Smile\Retailer
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class Renderer extends AbstractFieldArray
{
    /**
     * @var FormElementFactory
     */
    private FormElementFactory $elementFactory;

    /**
     * @var AbstractElement
     */
    private AbstractElement $element;

    /**
     * @var JsonSerializer
     */
    private JsonSerializer $jsonSerializer;

    /**
     * @var Resolver
     */
    private Resolver $localeResolver;

    /**
     * @var TimezoneInterface
     */
    private TimezoneInterface $localeDate;

    /**
     * @param Context               $context            Application context
     * @param FormElementFactory    $elementFactory     Element Factory
     * @param JsonSerializer        $jsonSerializer     JSON helper
     * @param Resolver              $localeResolver     Locale Resolver
     * @param TimezoneInterface     $localeDate         The Locale Date Interface
     * @param array                 $data               Element Data
     */
    public function __construct(
        Context $context,
        FormElementFactory $elementFactory,
        JsonSerializer $jsonSerializer,
        Resolver $localeResolver,
        TimezoneInterface $localeDate,
        array $data = []
    ) {
        $this->elementFactory = $elementFactory;
        $this->jsonSerializer = $jsonSerializer;
        $this->localeResolver = $localeResolver;
        $this->localeDate     = $localeDate;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function render(AbstractElement $element): string
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
        return $this->getElement()->getContainer()->getHtmlId();
    }

    /**
     * Render array cell for JS template
     *
     * @param string $columnName The column name
     *
     * @throws \Exception
     * @return string
     */
    public function renderCellTemplate($columnName): string
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
     * Initialise form fields
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName) Method is inherited
     * @return void
     */
    protected function _construct(): void
    {
        $this->addColumn('date', ['label' => 'Date']);
        $this->addColumn('opening_hours', ['label' => __('Special Opening Hours')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Special Opening Hours');

        parent::_construct();
    }

    /**
     * Wrap this container into a parent div when rendering.
     * Mainly used to have propoer binding via the Ui Component.
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName) Method is inherited
     *
     * @param string $html The rendered HTML
     *
     * @return string
     */
    protected function _afterToHtml($html): string
    {
        $htmlId = $this->getHtmlId();

        return "<div id=\"{$htmlId}\">{$html}</div>";
    }

    /**
     * Render The "Date" Column
     *
     * @param string $columnName The column name
     *
     * @return string
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
     * Render "Special Opening Hours" Column
     *
     * @param string $columnName The column name
     *
     * @return string
     */
    private function renderOpeningHoursColumn(string $columnName): string
    {
        $input = $this->elementFactory->create('text');
        $input->setForm($this->getElement()->getForm());

        $elementRenderer = $this->getLayout()
            ->createBlock(
                'Smile\StoreLocator\Block\Adminhtml\Retailer\OpeningHours\Element\Renderer'
            )->setData("input_id", $this->_getCellInputElementId('<%- _id %>', $columnName));

        $input->setName($this->_getCellInputElementName($columnName));
        $input->setRenderer($elementRenderer);

        return $input->toHtml();
    }

    /**
     * Apply date picker on an element.
     * Mandatory since Magento does this with x-magento-init tag which is NOT triggered when adding a field into array dynamically
     *
     * @param AbstractElement $element The element to apply calendar on
     * @return void
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
     * Parse Values to proper array-renderer compatible format
     *
     * @param array $values The values coming from model object
     *
     * @return array
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
                    $startTime = $timeDate->setTimestamp(strtotime($timeSlot->getStartTime()))->format(MagentoDateTime::DATETIME_PHP_FORMAT);
                    $endTime   = $timeDate->setTimestamp(strtotime($timeSlot->getEndTime()))->format(MagentoDateTime::DATETIME_PHP_FORMAT);
                    $timeRanges[] = [$startTime, $endTime];
                }

                $date = new DateTime($date);
                $arrayValues[] = [
                    "date" => $date->format(MagentoDateTime::DATETIME_PHP_FORMAT),
                    "opening_hours" => $this->jsonSerializer->serialize(array_filter($timeRanges)),
                ];
            }
        }

        return $arrayValues;
    }
}
