<?php

namespace common\helpers;

/**
 * DateHelper include functions for work with dates
 *
 */
class DateHelper
{

    /**
     * Get american format of date
     */
    public static function getAmericanFormatDbDate($mysqlDate, $withTime = false, $fullYear = false)
    {
        if ($fullYear) {
            $year = 'Y';
        } else {
            $year = 'y';
        }

        if ($withTime) {
            return date('m/d/' . $year . ' H:i:s', strtotime($mysqlDate));
        }
        else {
            return date('m/d/' . $year, strtotime($mysqlDate));
        }
    }

    /**
     * Get format of date for save
     */
    public static function getFileSaveFormatDbDate($mysqlDate)
    {
        return date('m-d-Y', strtotime($mysqlDate));
    }

    /**
     * Get DB date from Datepicker
     *
     * @param string $pickersDate
     * @param bool $withTime
     * @param bool $withoutDay
     * @return string
     */
    public static function getDbDateFromDatePicker($pickersDate = '', $withTime = false, $withoutDay = false)
    {
        // Also should pay attention that date field can has mask ("_" symbol as placeholder for masks position) and during submit be focused, so mask can be submitted also
        if (($pickersDate = trim($pickersDate)) && strpos($pickersDate, '_') === false) {
            if ($withTime) {
                $inputFormat = 'd/m/Y H:i:s';
                $outputFormat = 'Y-m-d H:i:s';
            } elseif ($withoutDay) {
                $inputFormat = 'm/Y';
                $outputFormat = 'Y-m';
            } else {
                $inputFormat = 'd/m/Y';
                $outputFormat = 'Y-m-d';
            }

            return \Yii::$app->formatter->asDate(\DateTime::createFromFormat($inputFormat, $pickersDate), 'php:' . $outputFormat);
        }

        return null;
    }

    /**
     * Get DB date from Effitrace
     */
    public static function getDbDateFromEffitrace($date = '', $withTime = false)
    {
        //TODO: adapt this code to Yii2

        if (!$date) {
            return null;
        }

        if ($withTime) {
            $inputFormat = 'yyyyMMddhhmmss?';
            $outputFormat = 'Y-m-d H:i:s';
        } else {
            $inputFormat = 'yyyyMMdd';
            $outputFormat = 'Y-m-d';
        }

        return date($outputFormat, CDateTimeParser::parse($date, $inputFormat));
    }

    /**
     * Get date from MySQL Date.
     */
    public static function getFullMonthFormatDbDate($mysqlDate, $withTime = false)
    {
        if ($withTime)
        {
            return date('d F Y H:i', strtotime( $mysqlDate ));
        }
        else
        {
            return date('d F Y', strtotime( $mysqlDate ));
        }
    }

    /**
     * Get french format of date
     */
    public static function getFrenchFormatDbDate($mysqlDate, $withTime = false, $withSeconds = false, $withoutDay = false)
    {
        if (empty($mysqlDate)) {
            return null;
        }

        if ($withTime) {
            if ($withSeconds) {
                $outputFormat = 'd/m/Y H:i:s';
            } else {
                $outputFormat = 'd/m/Y H:i';
            }
        } elseif ($withoutDay) {
            $outputFormat = 'm/Y';
        } else {
            $outputFormat = 'd/m/Y';
        }

        return date($outputFormat, strtotime($mysqlDate));
    }

    /**
     * Get difference of datetimes in seconds
     *
     * @author Perica Levatic <perica.levatic@gmail.com>
     *
     * @param string $startDatetime - TIMESTAMP
     * @param string $endDatetime - TIMESTAMP
     * @return int
     */
    public static function getSecondsDiff($startDatetime, $endDatetime)
    {
        $startTime = strtotime($startDatetime);
        $endTime = strtotime($endDatetime);

        return $endTime - $startTime;
    }

    public static function addMonths($date, $nb_months)
    {
        return date("Y-m-d", strtotime("+".$nb_months." month", strtotime($date)));
    }

    public static function substractMonths($date, $nb_months)
    {
        return date("Y-m-d", strtotime("-".$nb_months." month", strtotime($date)));
    }

    public static function getYesterday($date, $nb_months)
    {
        return date("Y-m-d", strtotime("+".$nb_months." month", strtotime($date)));
    }

    public static function getFirstDayOfMonth($date)
    {
        return date("Y-m-01", strtotime($date));
    }

    public static function getLastDayOfMonth($date)
    {
        return date("Y-m-d", mktime(0,0,0,date("n", strtotime($date))+1,0,date("Y", strtotime($date))));
    }

    public static function isLeapYear($year)
    {
        return ((($year % 4) == 0) && ((($year % 100) != 0) || (($year % 400) == 0)));
    }

    public static function getLastExistingDayOfMonth($day, $month, $year)
    {
        switch ($month) {
            case 1:
            case 3:
            case 5:
            case 7:
            case 8:
            case 10:
            case 12:
                return $day;
                break;
            case 4:
            case 6:
            case 9:
            case 11:
                if($day == 31){
                    return 30;
                }else{
                    return $day;
                }
                break;
            case 2:
                if($day == 31 || $day == 30 || $day == 29){
                    if( DateHelper::isLeapYear($year) ){
                        return 29;
                    }else{
                        return 28;
                    }
                }else{
                    return $day;
                }
                break;
            default:
                return -1;
                break;
        }
    }

}
