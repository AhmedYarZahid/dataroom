<?php

namespace common\helpers;

/**
 * NumberHelper represents the data needed to do manipulations with strings
 */
class NumberHelper
{
    /**
     * Format price
     *
     * @param float $value
     * @param bool $keepDecimals
     * @param string $thousandsSeparator
     * @return string
     * @access public
     */
    public static function formatPrice($value, $keepDecimals = false, $thousandsSeparator = ',')
    {
        return (intval($value) == $value && !$keepDecimals) ? intval($value) : number_format($value, 2, '.', $thousandsSeparator);
    }

    /**
     * Format price for use in Paybox
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param float $value
     * @param bool $use10digits
     * @return string
     */
    public static function formatPayboxPrice($value, $use10digits = false)
    {
        $value = trim(str_replace(' ', '', str_replace(',', '', str_replace('.', '', (string) number_format($value, 2)))));

        if ($use10digits) {
            $value = str_pad($value, 10, '0', STR_PAD_LEFT);
        }

        return $value;
    }
}