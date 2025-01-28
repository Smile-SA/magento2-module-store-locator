<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Plugin\Rest;

use Smile\StoreLocator\Model\Data\RetailerTimeSlotConverter;

/**
 * Plugin to load retailer extension attribute for rest usage
 */
class SerializeTimeslot
{
    /**
     * Encode slots to json
     *
     * @param $subject
     * @param array $openingHours
     * @return array encoded time slots
     */
    public function afterToEntity(RetailerTimeSlotConverter $subject, mixed $openingHours): array
    {
        $res = json_encode($openingHours, JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE);
        return [$res];
    }
}
