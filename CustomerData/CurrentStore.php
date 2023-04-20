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
use Magento\Customer\Model\Session;
use Magento\Framework\App\Http\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Smile\Map\Model\AddressFormatter;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Retailer\Api\RetailerRepositoryInterface;
use Smile\StoreLocator\Model\Url;

/**
 * Current Store data.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class CurrentStore implements SectionSourceInterface
{
    /**
     * Will be added as a Vary to HTTP Context
     */
    const CONTEXT_RETAILER = 'smile_retailer_id';

    /**
     * @var Session
     */
    private Session $customerSession;

    /**
     * @var RetailerRepositoryInterface
     */
    private RetailerRepositoryInterface $retailerRepository;

    /**
     * @var Url
     */
    private Url $urlModel;

    /**
     * @var AddressFormatter
     */
    private AddressFormatter $addressFormatter;

    /**
     * @var Context
     */
    private Context $httpContext;

    /**
     * CurrentStore constructor
     *
     * @param Session                       $customerSession    Customer session.
     * @param RetailerRepositoryInterface   $retailerRepository Retailer repository.
     * @param AddressFormatter              $addressFormatter   Address formatter.
     * @param Url                           $urlModel           URL model.
     * @param Context                       $context            The HTTP Context
     */
    public function __construct(
        Session $customerSession,
        RetailerRepositoryInterface $retailerRepository,
        AddressFormatter $addressFormatter,
        Url $urlModel,
        Context $context
    ) {
        $this->customerSession    = $customerSession;
        $this->retailerRepository = $retailerRepository;
        $this->urlModel           = $urlModel;
        $this->addressFormatter   = $addressFormatter;
        $this->httpContext        = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData(): array
    {
        $data     = [];
        $retailer = $this->getRetailer();

        if ($retailer) {
            $data = $retailer->toArray(['entity_id', 'name']);

            $data['url']          = $this->urlModel->getUrl($retailer);
            $data['address']      = $this->addressFormatter->formatAddress(
                $retailer->getAddress(),
                AddressFormatter::FORMAT_HTML
            );
            $data['address_data'] = $retailer->getAddress()->toArray();
        }

        return $data;
    }

    /**
     * Get the current session retailer.
     *
     * @return ?RetailerInterface
     */
    public function getRetailer(): ?RetailerInterface
    {
        $retailer = null;

        $retailerId = $this->customerSession->getRetailerId();

        if (!$retailerId) {
            $retailerId = $this->httpContext->getValue(self::CONTEXT_RETAILER);
        }

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
    public function setRetailer(RetailerInterface $retailer): self
    {
        $this->customerSession->setRetailerId($retailer->getId());
        $this->httpContext->setValue(self::CONTEXT_RETAILER, $retailer->getId(), false);

        return $this;
    }
}
