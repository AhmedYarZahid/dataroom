<?php

namespace common\helpers;

use \yii\helpers\BaseFileHelper;

/**
 * FileHelper represents the data needed to manage files/directories in project
 *
 */
class FileHelper extends BaseFileHelper
{
    /**
     * Get (generate if needed) file storage structure for specified directory
     *
     * @param string $baseFolder Base directory
     * @param bool $useHours
     * @param bool $useDays
     * @return string
     */
    public static function getStorageStructure($baseFolder, $useHours = false, $useDays = true)
    {
        $date = date('Y.m');
        $exploded = explode('.', $date);

        $folderStructure = $exploded[0] . '/' . $exploded[1] . '/';

        if ($useDays) {
            $daysFolder = date('d');
            $exploded[] = $daysFolder;
            $folderStructure .= $daysFolder . '/';
        }

        if ($useHours) {
            $hoursFolder = date('H');
            $exploded[] = $hoursFolder;
            $folderStructure .= $hoursFolder . '/';
        }

        $baseFolder .= (substr($baseFolder, -1) == '/' ? '' : '/');

        if (!file_exists($baseFolder . $folderStructure)) {
            static::createDirectory($baseFolder . $folderStructure);
        }

        return $folderStructure;
    }

}
