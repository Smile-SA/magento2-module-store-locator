<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Plugin;

use Magento\Ui\Component\Form;

class RetailerUiComponentFormPlugin
{
    private const DATA_SOURCE_NAME = 'storelocator_retailer_mass_edit_hours_form_data_source';

    /**
     * Set provider data.
     */
    public function afterGetDataSourceData(Form $subject, array $result): array
    {
        $dataProvider = $subject->getContext()->getDataProvider();
        if ($dataProvider->getName() == self::DATA_SOURCE_NAME && empty($result['data'])) {
            $result['data'] = $dataProvider->getData();
        }

        return $result;
    }
}
