<?php

namespace common\helpers;

use Yii;
use yii\db\ActiveRecord;
use \yii\helpers\BaseArrayHelper;
use yii\helpers\Url;

/**
 * Manipulation with arrays 
 *
 * @author Perica
 */
class ArrayHelper extends BaseArrayHelper
{
    CONST NO = 0;
    CONST YES = 1;

    /**
     * Get yes no list
     *
     * @author Perica Levatic <perica.levatic@gmail.com>
     *
     * @return array
     */
    public static function getYesNoList()
    {
        return array(
            self::YES => Yii::t('admin', 'Yes'),
            self::NO => Yii::t('admin', 'No')
        );
    }

    public static function printArray(array $array, $indent = 0)
    {
        $indentStep = 8;
        echo "array ( ".($indent>=0 ? "<br /> " : '' );
        foreach ($array as $key => $value){
            if ($indent>=0) {
                for ($index = 0; $index<$indent+$indentStep; $index++){
                    echo "&nbsp;";
                }
            }
            if (is_array($value)) {
                echo '"'.$key.'" => ';
                self::printArray($value, ($indent>=0 ? $indent+$indentStep : $indent));
            } else {
                echo '"'.$key.'" => "'.$value.'", '.($indent>=0 ? "<br /> " : '' );
            }
        }
        if ($indent>=0) {
            for ($index = 0; $index<$indent; $index++){
                echo "&nbsp;";
            }
        }
        echo " ), ".($indent>=0 ? "<br /> " : '' );
    }

    /**
     * Makes an array of parameters become a querystring like string.
     *
     * @param array $array
     *
     * @return string
     */
    static public function stringify(array $array)
    {
        $result = array();

        foreach ($array as $key => $value) {
            $result[] = sprintf('%s=%s', $key, $value);
        }

        return implode('&', $result);
    }

    /**
     * Merges two or more arrays into one recursively (taken from CMap class of framework core)
     *
     * If each array has an element with the same string key value, the latter
     * will overwrite the former (different from array_merge_recursive).
     * Recursive merging will be conducted if both arrays have an element of array
     * type and are having the same key.
     * For integer-keyed elements, the elements from the latter array will
     * be appended to the former array.
     * @param array $a array to be merged to
     * @param array $b array to be merged from. You can specify additional
     * arrays via third argument, fourth argument etc.
     * @return array the merged array (the original arrays are not changed.)
     * @see mergeWith
     */
    public static function mergeArray($a, $b)
    {
        $args = func_get_args();
        $res = array_shift($args);
        while (!empty($args)) {
            $next = array_shift($args);
            foreach ($next as $k => $v) {
                if (is_integer($k)) {
                    isset($res[$k]) ? $res[] = $v : $res[$k] = $v;
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = self::mergeArray($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }

        return $res;
    }

    /**
     * Prepare items for bootstrap dropdown widget
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param ActiveRecord[] $modelsList
     * @param string $labelAttr
     * @param array $routeParamToAttr
     * @param array $urlRoute
     * @return array
     */
    public static function prepareItemsForBootstrapDropdown($modelsList, $labelAttr, $routeParamToAttr, $urlRoute, $selectedItemID = null)
    {
        $result = [];
        foreach ($modelsList as $model) {
            foreach ($routeParamToAttr as $key => $attr) {
                $urlRoute[$key] = $model->$attr;
            }

            $result[] = ['label' => $model->$labelAttr, 'url' => Url::to($urlRoute), 'options' => ['class' => $model->id == $selectedItemID ? 'active' : '']];
        }

        return $result;
    }

    /**
     * Parse to float each elemnt of an array
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $array
     * @return array
     */
    public static function parseFloat($array)
    {
        foreach ($array as &$item) {
            $item = floatval($item);
        }

        return $array;
    }

    /**
     * Parse to french format each date of an array
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $array
     * @return array
     */
    public static function formatFrenchDate($array)
    {
        foreach ($array as &$item) {
            $item = DateHelper::getFrenchFormatDbDate($item);
        }

        return $array;
    }

    /**
     * Get random key from array
     *
     * @param array $array
     * @param string|null $excludeKey
     * @return string
     */
    public static function getRandomKeyFromArray($array, $excludeKey = null)
    {
        $key = intval(array_rand($array));

        if (count($array) > 1 && $excludeKey != null && $excludeKey == $key)
            return self::getRandomKeyFromArray($array);
        else
            return $key;
    }
}