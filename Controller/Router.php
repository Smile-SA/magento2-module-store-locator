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

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Smile\StoreLocator\Model\Url;

/**
 * Store locator routing (handling rewritten URL).
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Router implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    private ActionFactory $actionFactory;

    /**
     * @var ManagerInterface
     */
    private ManagerInterface $eventManager;

    /**
     * @var Url
     */
    private Url $urlModel;

    /**
     * Constructor.
     *
     * @param ActionFactory     $actionFactory Action factory.
     * @param ManagerInterface  $eventManager  Event manager.
     * @param Url               $urlModel      Retailer URL model.
     */
    public function __construct(
        ActionFactory $actionFactory,
        ManagerInterface $eventManager,
        Url $urlModel
    ) {
        $this->actionFactory = $actionFactory;
        $this->eventManager  = $eventManager;
        $this->urlModel      = $urlModel;
    }

    /**
     * Validate and Match Cms Page and modify request
     *
     * @param RequestInterface $request Request.
     *
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request): ActionInterface|null
    {
        $action = null;

        $requestPath = trim($request->getPathInfo(), '/');
        $condition   = new DataObject(['identifier' => $requestPath]);

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
     * @return bool
     */
    private function matchStoreLocatorHome(string $requestPath): bool
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
    private function matchRetailer(string $requestPath): int|false
    {
        $retailerId       = false;
        $requestPathArray = explode('/', $requestPath);

        if (count($requestPathArray) && $this->matchStoreLocatorHome(current($requestPathArray))) {
            $retailerId = $this->urlModel->checkIdentifier(end($requestPathArray));
        }

        return $retailerId;
    }
}
