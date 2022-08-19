<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Controller;

/**
 * Store locator routing (handling rewritten URL).
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    private $actionFactory;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * @var \Smile\StoreLocator\Model\Url
     */
    private $urlModel;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\ActionFactory      $actionFactory Action factory.
     * @param \Magento\Framework\Event\ManagerInterface $eventManager  Event manager.
     * @param \Smile\StoreLocator\Model\Url             $urlModel      Retailer URL model.
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Smile\StoreLocator\Model\Url $urlModel
    ) {
        $this->actionFactory = $actionFactory;
        $this->eventManager  = $eventManager;
        $this->urlModel      = $urlModel;
    }

    /**
     * Validate and Match Cms Page and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request Request.
     *
     * @return NULL|\Magento\Framework\App\ActionInterface
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $action = null;

        $requestPath = trim($request->getPathInfo(), '/');
        $condition  = new \Magento\Framework\DataObject(['identifier' => $requestPath]);

        if ($this->matchStoreLocatorHome($requestPath)) {
            $this->eventManager->dispatch(
                'store_locator_search_controller_router_match_before',
                ['router' => $this, 'condition' => $condition]
            );

            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $requestPath)
                ->setModuleName('storelocator')
                ->setControllerName('store')
                ->setActionName('search');

            $action = $this->actionFactory->create('Magento\Framework\App\Action\Forward', ['request' => $request]);
        } elseif ($retailerId = $this->matchRetailer($requestPath)) {
            $this->eventManager->dispatch(
                'store_locator_view_controller_router_match_before',
                ['router' => $this, 'condition' => $condition]
            );

            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $requestPath)
                ->setModuleName('storelocator')
                ->setControllerName('store')
                ->setActionName('view')
                ->setParam('id', $retailerId);

            $action = $this->actionFactory->create('Magento\Framework\App\Action\Forward', ['request' => $request]);
        }

        return $action;
    }

    /**
     * Check if the current request path match the configured store locator home.
     *
     * @param string $requestPath Request path.
     *
     * @return boolean
     */
    private function matchStoreLocatorHome($requestPath)
    {
        return $this->urlModel->getRequestPathPrefix() == $requestPath;
    }

    /**
     * Check if the current request path match a retailer URL and returns its id.
     *
     * @param string $requestPath Request path.
     *
     * @return int|false
     */
    private function matchRetailer($requestPath)
    {
        $retailerId       = false;
        $requestPathArray = explode('/', $requestPath);

        if (count($requestPathArray) && $this->matchStoreLocatorHome(current($requestPathArray))) {
            $retailerId = $this->urlModel->checkIdentifier(end($requestPathArray));
        }

        return $retailerId;
    }
}
