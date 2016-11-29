<?php

namespace Smile\StoreLocator\Controller;

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
     * @var \Smile\StoreLocator\Helper\Data
     */
    private $storeLocatorHelper;

    /**
     *
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Smile\StoreLocator\Helper\Data $storeLocatorHelper
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Smile\StoreLocator\Helper\Data $storeLocatorHelper
    ) {
        $this->actionFactory      = $actionFactory;
        $this->eventManager       = $eventManager;
        $this->storeLocatorHelper = $storeLocatorHelper;
    }

    /**
     * Validate and Match Cms Page and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $action = null;

        $identifier = trim($request->getPathInfo(), '/');
        $condition  = new \Magento\Framework\DataObject(['identifier' => $identifier]);

        if ($this->matchStoreLocatorHome($identifier)) {

            $this->eventManager->dispatch(
                'store_locator_search_controller_router_match_before',
                ['router' => $this, 'condition' => $condition]
            );

            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier)
                ->setModuleName('storelocator')
                ->setControllerName('search')
                ->setActionName('index');

            $action = $this->actionFactory->create('Magento\Framework\App\Action\Forward', ['request' => $request]);
        }

        return $action;
    }

    private function matchStoreLocatorHome($identifier)
    {
        return $this->storeLocatorHelper->getBaseUrlPrefix() == $identifier;
    }
}
