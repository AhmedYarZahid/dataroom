<?php

namespace common\helpers;

/**
 * StringHelper represents the data needed to do manipulations with strings
 *
 */
class StringHelper
{
    /**
     * Generate Random string.
     *
     * @param int|string $strSize Random string size.
     * @param bool $onlyWordSymbols
     * @param bool $onlyNumbers
     *
     * @return  string
     */
    public static function getRandom($strSize = 8, $onlyWordSymbols = true, $onlyNumbers = false)
    {
        $result = '';
        $symbolStr = '';

        for ($i = 0; $i < 255; $i++)
        {
            $symbolStr .= chr($i);
        }

        if ($onlyNumbers)
        {
            $eregPattern = "/[^0-9]/";
        }
        else
        {
            $eregPattern = ($onlyWordSymbols) ? "/[^0-9A-Za-z]/" : "/[^0-9A-Za-z\_\%\&\-\^]/";
        }

        $symbolStr = preg_replace($eregPattern, '', $symbolStr);

        $symbolsCount = strlen($symbolStr);
        for ($i = 0; $i < $strSize; $i++)
        {
            mt_srand((double) microtime() * ($i + 13) * 100000);
            $randomIndex = mt_rand(0, $symbolsCount - 1);
            $result .= $symbolStr{$randomIndex};
        }

        return $result;
    }

    /**
     * Trim string to specified number of symbols (to nearest of words).
     *
     * @param string $string
     * @param integer $numberOfSymbols number of symbols in returning string
     * @param string $closingString closing string of trancated string
     * @param bool $truncateByWords
     *
     * @return string
     */
    public static function trimToSymbols($string = '', $numberOfSymbols = 0, $closingString = '...', $truncateByWords = true)
    {
        $numberOfSymbols = (int) $numberOfSymbols;
        $currentLength = strlen($string);

        if ($numberOfSymbols <= 0 || $numberOfSymbols >= $currentLength) {
            return $string;
        }

        $symbolsNumber = $truncateByWords ? strpos($string, ' ', $numberOfSymbols) : $numberOfSymbols;
        return substr($string, 0, $symbolsNumber) . $closingString;
    }

    /**
     * Strip only script tags
     *
     * @param string $html
     *
     * @return string
     */
    static public function stripScript($html)
    {
        if (is_string($html)) {
            return preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        }

        return '';
    }

    /**
     * Strip all valid html tags
     * @param string $str
     * @return string
     */
    static public function stripHtmlTags($str)
    {
        return strip_tags($str);
        
        /*if (is_string($str))
        {
            $p = new CHtmlPurifier();
            $p->options = array('HTML.Allowed' => '');

            return $p->purify($str);
        }

        return '';*/
    }

    /**
     * Highlight word in specified string
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param string $string
     * @param string $word
     * @return string
     */
    public function highlight($string, $word)
    {
        if (!empty($word)) {
            $string = preg_replace('~' . $word . '~i', '<span class="search-mark">$0</span>', $string);
        }
        return $string;
    }

    /**
     * Remove french accents from a string
     *
     * @author Perica Levatic <perica.levatic@gmail.com>
     *
     * @param string $str
     * @return string
     */
    public static function removeAccents($str)
    {
        return str_replace(
            array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'),
            array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'),
            $str
        );
    }

}
