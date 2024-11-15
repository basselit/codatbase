<?php

namespace App\TFramework\Network;

use App\TeckModels\Filters\TOrdersOneDay;
use App\TeckPay\Models\TMerchant;

class TFilters
{
    public array $elements;
    public TMerchant $curMerch;

    public function __construct(TMerchant $curMerch)
    {
        $this->curMerch = $curMerch;
        $this->elements = [];
    }

    public function get(int $index): TFilterBase
    {
        return $this->elements[$index];

    }

    public function processOneDayParts(int $year, int $month, int $day, int $totalParts)
    {

        for ($i = 1; $i <= $totalParts; $i++)
        {
            $oneFilter = new TOrdersOneDay($this->curMerch,$year,$month,$day,$totalParts,$i);
            $this->elements[] = $oneFilter;
        }
    }





}
