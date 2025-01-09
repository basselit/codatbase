<?php

namespace Codatsoft\Codatbase;

use DateTime;
use DateTimeZone;

class STDates
{
    public static function areTwoSqlDatesCloseBySeconds(DateTime $date1,DateTime $date2): bool
    {

        $diff = $date1->getTimestamp() - $date2->getTimestamp();
        $diff = abs($diff);

        if ($diff < 3)
        {
            return true;
        } else
        {
            return  false;
        }

    }

    private static function getTimeZone(): DateTimeZone
    {
        try {
            return new DateTimeZone(date_default_timezone_get());

        } catch (\Throwable $exception)
        {
            return new DateTimeZone("America/New_York");
        }
    }


    public static function areTwoUnixDatesCloseBySeconds($unixClovDate1,$unixClovDate2): bool
    {
        $fix1 = substr($unixClovDate1,0,10);
        $fix2 = substr($unixClovDate2,0,10);

        if ($fix1 == '')
        {
            $fix1 = 0;
        }

        if ($fix2 == '')
        {
            $fix2 = 0;
        }

        $diff = $fix1 - $fix2;
        $diff = abs($diff);

        if ($diff < 3)
        {
            return true;
        } else
        {
            return  false;
        }

    }


    public static function getHoursPassedSinceUnixClovTime($unixClovDate)
    {
        $fix1 = substr($unixClovDate,0,10);
        $now = time();

        $diff = $now - $fix1;

        $hour = (($diff / 60) / 60);

        return $hour;

    }

    public static function getSqlDT(int|DateTime|string $someDate, ?bool $timeFirst = null): ?DateTime
    {

        if (is_int($someDate))
        {
            $unix = substr($someDate,0,10);
            $source = date(STDateFormats::SQL_DATE_TIME , $unix);
            $newDate = new DateTime($source);

        } else if (is_string($someDate))
        {
            $curFormat = self::getFormatFrom($someDate);
            $newDate = DateTime::createFromFormat($curFormat,$someDate);
        } else if ($someDate instanceof DateTime)
        {
            $newDate = $someDate;
        } else
        {
            $newDate = null;
        }

        if (!is_null($timeFirst) && !is_null($newDate))
        {
            if ($timeFirst)
            {
                $newDate->setTime(0,0,0);
            } else
            {
                $newDate->setTime(23,59,59);
            }
        }

        return $newDate;

    }

    public static function getUnixFromDate(string|DateTime $sqlDate, ?bool $timeFirst = null): int
    {
        //here now
        if (is_string($sqlDate))
        {
            $curFormat = self::getFormatFrom($sqlDate);
            $originalDate = DateTime::createFromFormat($curFormat,$sqlDate,self::getTimeZone());
            //$newDateString = DateTime::createFromFormat($curFormat,$sqlDate,STCache::getTimeZone())->format(STDateFormats::SQL_DATE);
            $dateString = $originalDate->format(STDateFormats::SQL_DATE);
            $dateTimeString = $originalDate->format(STDateFormats::SQL_DATE_TIME);

            if (!is_null($timeFirst))
            {
                if (!$timeFirst)
                {
                    $newDateString = $dateString . ' 23:59:59';
                } else
                {
                    $newDateString = $dateString . ' 00:00:00';
                }
            } else
            {
                $newDateString = $dateTimeString;
            }

            return DateTime::createFromFormat(STDateFormats::SQL_DATE_TIME,$newDateString,self::getTimeZone())->getTimestamp() * 1000;

        } else
        {
            //$sqlDate->setTimezone(STCache::getTimeZone());
            $sqlDate->format(STDateFormats::SQL_DATE_TIME);
            return $sqlDate->getTimestamp() * 1000;
        }

    }

    //end of above main date functions


    public static function getSqlDTString(int|DateTime|string $someDate, ?bool $timeFirst = null): string
    {
        return self::getSqlDT($someDate,$timeFirst)->format(STDateFormats::SQL_DATE_TIME);
    }

    public static function getSqlDString(int|DateTime|string $someDate): string
    {
        return self::getSqlDT($someDate)->format(STDateFormats::SQL_DATE);
    }


    public static function tryConvert(string|int|DateTime $firstDate, string|int|DateTime $secondDate)
    {
        if (is_int($firstDate))
        {
            if ($secondDate instanceof DateTime)
            {
                return self::getSqlDT($firstDate);
            }


        }

    }


    public static function getShortDateTimeFromUnixClov(string $unixCloverString): string
    {
        return self::getDateStringFromUnixClover($unixCloverString, STDateFormats::TECK_SHORT_DATE);

    }

    public static function getTeckTwoLinesTimeDate(string $unixCloverString): string
    {
        return self::getDateStringFromUnixClover($unixCloverString, STDateFormats::TECK_TWO_LINES_TIME_DATE);
    }

    private static function getDateStringFromUnixClover(string $unixCloverString, string $format): string
    {
        //18 May  5:57 PM
        $fix1 = substr($unixCloverString,0,10);
        $fix2 = date(STDateFormats::SQL_DATE_TIME, $fix1);
        $fix3 = new DateTime($fix2);
        $fix3->setTimezone(self::getTimeZone());

        $fix4 = $fix3->format($format);

        return $fix4;
    }

    private static function getFormatFrom(string $date): string
    {
        if (self::isFormat($date,STDateFormats::SQL_DATE_TIME))
        {
            return STDateFormats::SQL_DATE_TIME;
        }

        if (self::isFormat($date,STDateFormats::TECK_DATE_FORMAT))
        {
            return STDateFormats::TECK_DATE_FORMAT;
        }

        if (self::isFormat($date,STDateFormats::SQL_DATE))
        {
            return STDateFormats::SQL_DATE;
        }

        return "";

    }

    private static function isFormat(string $date,string $format): bool
    {
        $checkDate = DateTime::createFromFormat($format, $date);
        $valid = $checkDate && $checkDate->format($format) === $date;
        return $valid;
    }

    public static function getOneDayDateRange(string $myDate, int $totalParts, int $wantedPart): TDateRange
    {

        $range = new TDateRange();

        $range->startDateTime = self::getSqlDT($myDate);
        $range->endDateTime = self::getSqlDT($myDate);

        $divCount = 24 / $totalParts;
        $firstHour = $divCount * ($wantedPart - 1);   //starting hour ex. 0,1,2
        $endHour = ($divCount * $wantedPart) - 1;     //last hour ex. 23,

        $range->startDateTime->setTime($firstHour,0,0,0);
        $range->endDateTime->setTime($endHour,59,59,59);

        $range->unixStartTime = self::getUnixFromDate($range->startDateTime);
        $range->unixEndTime = self::getUnixFromDate($range->endDateTime);

        return $range;

    }

    public static function isDateMismatchClover($data): bool
    {
        $created = $data->cloverCreatedTime;
        $clientCreated = $data->cloverClientCreatedTime;

        $modTime = 0;
        if (property_exists($data,'cloverModifiedTime') && $data->cloverModifiedTime != null && $data->cloverModifiedTime != 0)
        {
            $modTime = $data->cloverModifiedTime;
        }

        if ($modTime == 0)
        {
            $modTime = $created;
        }

        $time1 = STDates::getSqlDT($created);
        $time2 = STDates::getSqlDT($clientCreated);
        $time3 = STDates::getSqlDT($modTime);

        $t1 = $time1->format('j');
        $t2 = $time2->format('j');
        $t3 = $time3->format('j');

        if ($t1 == $t2 && $t1 == $t3 && $t2 == $t3)
        {
            return false;
        }

        return true;

    }



    public static function getNowDateString(): string
    {
        return self::getNowDate()->format(STDateFormats::SQL_DATE_TIME);
    }

    public static function getNowDate(): DateTime
    {
        $newDate = new DateTime();
        $newDate->setTimezone(self::getTimeZone());
        return $newDate;
    }

    //
    public static function getNowMonthName($month, $year)
    {
        //$me = date('F - Y',strtotime('2023-9-1'));
        //return date('F - Y', strtotime($year . '-' . $month));
        return date('F - Y', strtotime($year . '-' . $month));
    }

    public static function getNowFileName($month, $year)
    {
        //$me = date('F - Y',strtotime('2023-9-1'));
        //return date('F - Y', strtotime($year . '-' . $month));
        return strtolower(date('M-Y', strtotime($year . '-' . $month)));
    }

    public static function getFileNameDesc($month, $year)
    {
        //$me = date('F - Y',strtotime('2023-9-1'));
        //return date('F - Y', strtotime($year . '-' . $month));
        return date('F - Y', strtotime($year . '-' . $month));
    }



}
