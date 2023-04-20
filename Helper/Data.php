<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Aurelien FOUCRET <aurelien.foucret@gmail.com>
 * @author    Fanny DECLERCK <fadec@smile.fr>
 * @copyright 2020 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\StoreLocator\Model\Url;

/**
 * Store locator helper.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@gmail.com>
 * @author   Fanny DECLERCK <fadec@smile.fr>
 */
class Data extends AbstractHelper
{
    /**
     * @var Url
     */
    private Url $urlModel;

    /**
     * Constructor.
     *
     * @param Context   $context  Helper context.
     * @param Url       $urlModel Retailer URL model.
     */
    public function __construct(
        Context $context,
        Url $urlModel
    ) {
        parent::__construct($context);
        $this->urlModel = $urlModel;
    }

    /**
     * Store locator home URL.
     *
     * @param ?int $storeId Store id.
     *
     * @return string
     */
    public function getHomeUrl(?int $storeId = null): string
    {
        return $this->urlModel->getHomeUrl($storeId);
    }

    /**
     * Retailer URL.
     *
     * @param RetailerInterface $retailer Retailer.
     *
     * @return string
     */
    public function getRetailerUrl(RetailerInterface $retailer): string
    {
        return $this->urlModel->getUrl($retailer);
    }
}
