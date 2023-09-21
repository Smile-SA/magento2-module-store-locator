<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Controller;

use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Framework\Url as CoreUrl;
use Smile\StoreLocator\Model\Url;

/**
 * Store locator routing (handling rewritten URL).
 */
class Router implements RouterInterface
{
    public function __construct(
        private ActionFactory $actionFactory,
        private ManagerInterface $eventManager,
        private Url $urlModel
    ) {
    }

    /**
     * @inheritdoc
     */
    public function match(RequestInterface $request)
    {
        /** @var Request|RequestInterface $request */
        $action = null;
        $requestPath = trim($request->getPathInfo(), '/');
        $condition = new DataObject(['identifier' => $requestPath]);

        if ($this->matchStoreLocatorHome($requestPath)) {
            $this->eventManager->dispatch(
                'store_locator_search_controller_router_match_before',
                ['router' => $this, 'condition' => $condition]
            );

            $request->setAlias(CoreUrl::REWRITE_REQUEST_PATH_ALIAS, $requestPath)
                ->setModuleName('storelocator')
                ->setControllerName('store')
                ->setActionName('search');

            $action = $this->actionFactory->create(Forward::class);
        } else {
            $retailerId = $this->matchRetailer($requestPath);
            if ($retailerId) {
                $this->eventManager->dispatch(
                    'store_locator_view_controller_router_match_before',
                    ['router' => $this, 'condition' => $condition]
                );

                $request->setAlias(CoreUrl::REWRITE_REQUEST_PATH_ALIAS, $requestPath)
                    ->setModuleName('storelocator')
                    ->setControllerName('store')
                    ->setActionName('view')
                    ->setParam('id', $retailerId);

                $action = $this->actionFactory->create(Forward::class);
            }
        }

        return $action;
    }

    /**
     * Check if the current request path match the configured store locator home.
     */
    private function matchStoreLocatorHome(string $requestPath): bool
    {
        return $this->urlModel->getRequestPathPrefix() == $requestPath;
    }

    /**
     * Check if the current request path match a retailer URL and returns its id.
     */
    private function matchRetailer(string $requestPath): int|false
    {
        $retailerId = false;
        $requestPathArray = explode('/', $requestPath);

        // @phpstan-ignore-next-line
        if (count($requestPathArray) && $this->matchStoreLocatorHome(current($requestPathArray))) {
            $retailerId = $this->urlModel->checkIdentifier(end($requestPathArray));
        }

        return $retailerId;
    }
}
