<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Fanny DECLERCK <fadec@smile.fr>
 * @copyright 2020 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Controller\Ajax;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Smile\StoreLocator\Model\Autocomplete\DataProvider;
/**
 * Suggest action
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Fanny DECLERCK <fadec@smile.fr>
 */
class Suggest extends Action implements HttpGetActionInterface
{
    /**
     * @var DataProvider
     */
    protected $dataProvider;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param DataProvider                          $dataProvider
     */
    public function __construct(
        Context $context,
        DataProvider $dataProvider
    ) {
        $this->dataProvider = $dataProvider;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->getRequest()->getParam('q', false)) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_url->getBaseUrl());
            return $resultRedirect;
        }

        $autocompleteData = $this->dataProvider->getItems($this->getRequest()->getParam('q'));
        $responseData = [];
        foreach ($autocompleteData as $resultItem) {
            $responseData[] = $resultItem->toArray();
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseData);
        return $resultJson;
    }
}
