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
namespace Smile\StoreLocator\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Smile\Map\Model\AddressFormatter;
use Magento\Framework\Exception\NoSuchEntityException;
use Smile\Retailer\Api\Data\RetailerInterface;

/**
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class CurrentStore implements SectionSourceInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Smile\Retailer\Api\RetailerRepositoryInterface
     */
    private $retailerRepository;

    /**
     * @var \Smile\StoreLocator\Model\Url
     */
    private $urlModel;

    /**
     * @var \Smile\Map\Model\AddressFormatter
     */
    private $addressFormatter;

    /**
     *
     * @param \Magento\Customer\Model\Session                 $customerSession    Customer session.
     * @param \Smile\Retailer\Api\RetailerRepositoryInterface $retailerRepository Retailer repository.
     * @param \Smile\Map\Model\AddressFormatter               $addressFormatter   Address formatter.
     * @param \Smile\StoreLocator\Model\Url                   $urlModel           URL model.
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Smile\Retailer\Api\RetailerRepositoryInterface $retailerRepository,
        \Smile\Map\Model\AddressFormatter $addressFormatter,
        \Smile\StoreLocator\Model\Url $urlModel
    ) {
        $this->customerSession    = $customerSession;
        $this->retailerRepository = $retailerRepository;
        $this->urlModel           = $urlModel;
        $this->addressFormatter   = $addressFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        $data     = [];
        $retailer = $this->getRetailer();

        if ($retailer) {
            $data = $retailer->toArray(['entity_id', 'name']);

            $data['url']     = $this->urlModel->getUrl($retailer);
            $data['address'] = $this->addressFormatter->formatAddress($retailer->getAddress(), AddressFormatter::FORMAT_HTML);
        }

        return $data;
    }

    /**
     * Get the current session retailer.
     *
     * @return \Smile\Retailer\Api\Data\RetailerInterface
     */
    public function getRetailer()
    {
        $retailer = null;

        $retailerId = $this->customerSession->getRetailerId();

        if ($retailerId) {
            try {
                $retailer = $this->retailerRepository->get($retailerId);
            } catch (NoSuchEntityException $e) {
                $this->customerSession->unsRetailerId();
            }
        }

        return $retailer;
    }

    /**
     * Set a new retailer.
     *
     * @param RetailerInterface $retailer Current retailer.
     *
     * @return $this
     */
    public function setRetailer($retailer)
    {
        $this->customerSession->setRetailerId($retailer->getId());

        return $this;
    }
}
