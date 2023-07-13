<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Block\Adminhtml\Retailer\OpeningHours\Container;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\Form\Element\Text;
use Magento\Framework\Locale\ListsInterface;
use Magento\Framework\Stdlib\DateTime;
use Smile\StoreLocator\Block\Adminhtml\Retailer\OpeningHours\Element\Renderer as ElementRenderer;

/**
 * Opening Hours field renderer.
 */
class Renderer extends Template implements RendererInterface
{
    protected AbstractElement $element;

    // phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
    protected Text $input;
    protected $_template = 'retailer/openinghours/container.phtml';
    // phpcs:enable

    public function __construct(
        Context $context,
        protected Factory $elementFactory,
        private ListsInterface $localeLists,
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
        $this->element->addClass("opening-hours-container-fieldset");

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
     * Render HTML of the element using the opening hours engine.
     */
    public function getInputHtml(): string
    {
        if ($this->element->getOpeningHours()) {
            $values = $this->element->getOpeningHours();
        }

        $html = "";
        $days = $this->localeLists->getOptionWeekdays(true, true);

        foreach ($days as $key => $day) {
            $input = $this->elementFactory->create('text');
            $input->setForm($this->getElement()->getForm());

            /** @var ElementRenderer $elementRenderer */
            $elementRenderer = $this->getLayout()->createBlock(ElementRenderer::class);
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
