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
namespace Smile\StoreLocator\Block\Adminhtml\Retailer\OpeningHours\Container;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\Form\Element\Text;
use Magento\Framework\Locale\ListsInterface;
use Magento\Framework\Stdlib\DateTime;

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
     * @var Factory
     */
    protected Factory $elementFactory;

    /**
     * @var AbstractElement
     */
    protected AbstractElement $element;

    /**
     * @var Text
     */
    protected Text $input;

    /**
     * @var string
     */
    protected $_template = 'retailer/openinghours/container.phtml';

    /**
     * @var ?ListsInterface
     */
    private ?ListsInterface $localeList = null;

    /**
     * Block constructor.
     *
     * @param Context           $context        Templating context.
     * @param Factory           $elementFactory Form element factory.
     * @param ListsInterface    $localeLists    Locale List.
     * @param array             $data           Additional data.
     */
    public function __construct(
        Context $context,
        Factory $elementFactory,
        ListsInterface $localeLists,
        array $data = []
    ) {
        $this->elementFactory = $elementFactory;
        $this->localeList     = $localeLists;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function render(AbstractElement $element): string
    {
        $this->element = $element;
        $this->element->addClass("opening-hours-container-fieldset");

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
     * Render HTML of the element using the opening hours engine.
     *
     * @return string
     */
    public function getInputHtml(): string
    {
        if ($this->element->getOpeningHours()) {
            $values = $this->element->getOpeningHours();
        }

        $html = "";
        $days = $this->localeList->getOptionWeekdays(true, true);

        foreach ($days as $key => $day) {
            $input = $this->elementFactory->create('text');
            $input->setForm($this->getElement()->getForm());

            $elementRenderer = $this->getLayout()
                ->createBlock('Smile\StoreLocator\Block\Adminhtml\Retailer\OpeningHours\Element\Renderer');

            $elementRenderer->setDateFormat(DateTime::DATETIME_INTERNAL_FORMAT);

            $input->setLabel(ucfirst($day['label']));
            $input->setName($this->element->getName() . "[$key]");
            $input->setRenderer($elementRenderer);

            if (isset($values[$key])) {
                $input->setValue($values[$key]);
            }

            $html .= $input->toHtml();
        }

        return $html;
    }
}
