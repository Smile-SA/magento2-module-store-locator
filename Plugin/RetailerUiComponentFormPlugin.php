<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Fanny DECLERCK <fadec@smile.fr>
 * @copyright 2019 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Plugin;

use Magento\Ui\Component\Form;

/**
 * Retailer ui component form plugin.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Fanny DECLERCK <fadec@smile.fr>
 */
class RetailerUiComponentFormPlugin
{
    /**
     * Data source name
     */
    const DATA_SOURCE_NAME = 'storelocator_retailer_mass_edit_hours_form_data_source';

    /**
     * @param Form  $subject
     * @param array $result
     *
     * @return array
     */
    public function afterGetDataSourceData(Form $subject, array $result)
    {
        $dataProvider = $subject->getContext()->getDataProvider();
        if ($dataProvider->getName() == self::DATA_SOURCE_NAME && empty($result['data'])) {
            $result['data'] = $dataProvider->getData();
        }

        return $result;
    }
}
