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
     * Add retailer ids selected to datasource data
     *
     * @param \Magento\Ui\Component\Form $form    Form
     * @param \Closure                   $proceed Closure
     * @return array
     */
    public function aroundGetDataSourceData(\Magento\Ui\Component\Form $form, \Closure $proceed)
    {
        $dataSource = $proceed();

        $dataProvider = $form->getContext()->getDataProvider();
        if ($dataProvider->getName() == 'storelocator_retailer_mass_edit_hours_form_data_source'
            && !isset($dataSource['data'])
        ) {
            $dataSource['data'] = $dataProvider->getData();
        }

        return $dataSource;
    }
}
