<?php

namespace Guesl\Admin\Utilities;

use Carbon\Carbon;
use Illuminate\Support\Arr;

/**
 * Trait QueryBuild
 * @package Guesl\Query\Utilities
 */
class MultipleHours
{
    /**
     * @param $storeHourData
     * @return array|mixed
     */
    public static function getMultiOpenHours($storeHourData)
    {
        // retrieve data from request
        $monday = Arr::get($storeHourData, "monday") ?? [];
        $tuesday = Arr::get($storeHourData, "tuesday") ?? [];
        $wednesday = Arr::get($storeHourData, "wednesday") ?? [];
        $thursday = Arr::get($storeHourData, "thursday") ?? [];
        $friday = Arr::get($storeHourData, "friday") ?? [];
        $saturday = Arr::get($storeHourData, "saturday") ?? [];
        $sunday = Arr::get($storeHourData, "sunday") ?? [];

        $weekday = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $data = [];
        for ($i = 0; $i < count($weekday); $i++) {
            $day = $weekday[$i];
            $dayArray = ${$day};
            self::getOpenHourArray($data, $day, $dayArray);
        }

        return $data;
    }

    /**
     * Get the [day_from, day_to] formation hours array.
     *
     * return the old $data with the new day info.
     *
     * @param $data
     * @param string $day
     * @param array $dayArray
     * @return array
     */
    public static function getOpenHourArray(&$data, string $day, array $dayArray)
    {
        // traversal each time range in the array
        if (!empty($dayArray)) {
            foreach ($dayArray as $item) {
                if (count($item) == 2) {
                    $key1 = $day . '_from';
                    $key2 = $day . '_to';
                    $timeFrom = self::getDayFormat($item[$key1] ?? "00:00");
                    $timeTo = self::getDayFormat($item[$key2] ?? "23:59");
                    $data[$day][] = array($timeFrom, $timeTo);
                }
            }
        }
        return $data;
    }

    /**
     * a helper function to format time only
     *
     * @param $dayTime
     * @return string
     */
    private static function getDayFormat($dayTime)
    {
        // regular time is like 9:00, but the overnight time ends with seconds.
        // so regular time length is less or equal to 5 only hour and minute
        // overtime end with "23:59:59", and the second day start at "00:00:01" . which is length larger than 5.
        if (strlen($dayTime) > 5) {
            return Carbon::createFromFormat("H:i:s", strtolower($dayTime))->toTimeString();
        }
        return Carbon::createFromFormat("H:i", strtolower($dayTime))->toTimeString();
    }
}
