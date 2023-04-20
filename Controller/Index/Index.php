<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @author    Guillaume Vrac <guillaume.vrac@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Smile\StoreLocator\Helper\Data as StoreLocatorHelper;

/**
 * Index action (redirect to the search/index action).
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 * @author   Guillaume Vrac <guillaume.vrac@smile.fr>
 */
class Index extends Action
{
    /**
     * @var StoreLocatorHelper
     */
    private StoreLocatorHelper $storeLocatorHelper;

    /**
     * Constructor.
     *
     * @param Context            $context            Controller context.
     * @param StoreLocatorHelper $storeLocatorHelper Store locator helper.
     */
    public function __construct(
        Context $context,
        StoreLocatorHelper $storeLocatorHelper
    ) {
        parent::__construct($context);
        $this->storeLocatorHelper = $storeLocatorHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(): ResponseInterface|ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectUrl    = $this->storeLocatorHelper->getHomeUrl();
        $resultRedirect->setPath($redirectUrl);

        return $resultRedirect;
    }
}
