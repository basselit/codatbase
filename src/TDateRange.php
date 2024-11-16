<?php

namespace Codatsoft\Codatbase;

use DateTime;

class TDateRange
{
    public int $unixStartTime;
    public int $unixEndTime;

    public DateTime $startDateTime;
    public DateTime $endDateTime;

    public int $day;
    public int $month;
    public int $year;

    public function __construct(string $teckStartDate = null, string $teckEndDate = null)
    {
        if (!is_null($teckStartDate) && !is_null($teckEndDate))
        {
            $this->unixStartTime = STDates::getUnixFromDate($teckStartDate,true);
            $this->unixEndTime = STDates::getUnixFromDate($teckEndDate,false);
            $this->startDateTime = STDates::getSqlDT($teckStartDate,true);
            $this->endDateTime = STDates::getSqlDT($teckEndDate,false);
        }
    }

    public function buildDateParts(int $day, int $month, int $year)
    {
        $this->day = $day;
        $this->month = $month;
        $this->year = $year;
        $teckDate = $month . '-' . $day . '-' . $this->year;

        $this->unixStartTime = STDates::getUnixFromDate($teckDate,true);
        $this->unixEndTime = STDates::getUnixFromDate($teckDate,false);
        $this->startDateTime = STDates::getSqlDT($teckDate,true);
        $this->endDateTime = STDates::getSqlDT($teckDate,false);

    }


}
