<?php

namespace App\Services;

use ZipArchive;

class FileService{

    /**
     * Generates a csv file from a hashmap of unique column keys mapped to column names. Each row is a hashmap mapping
     * the column keys to the row value for that column.
     * @param $colMap - A hashmap of id => name. The name is what ends up in the column header.
     * @param $rowMaps - An array of hashmaps for each row. Hashmap keys should correspond to the $colMap keys.
     * @param $nullValue - The string to use in place of null and empty strings.
     * @param $filePath - The path to the csv file.
     */
    public static function writeCsv($colMap, $rowMaps, $filePath, $nullValue='NA', $replacements=['DK'=>'Dont_Know','RF'=>'Refused']){

        $headerIds = array();
        $headerNames = array();
        foreach ($colMap as $id => $name){
            array_push($headerIds, $id);
            array_push($headerNames, $name);
        }


        $file = fopen($filePath, 'w');

        // Write headers
        fputcsv($file, $headerNames);
        foreach ($rowMaps as $rowMap){
            $row = array();
            foreach ($headerIds as $id){
                if(array_key_exists($id, $rowMap)
                    && $rowMap[$id] !== ''){
                    if(is_string($rowMap[$id]) && array_key_exists($rowMap[$id], $replacements)){
                        array_push($row, $replacements[$rowMap[$id]]);
                    } else {
                        array_push($row, $rowMap[$id]);
                    }
                } else {
                    // Value doesn't exist or the string is empty
                    array_push($row, $nullValue);
                }
            }
            // Write row
            fputcsv($file, $row);
        }


        fclose($file);

    }

    /**
     * Add all files supplied to the base level of the specified zip file.
     * @param $filename - Full path to the zip archive. This is created if it doesn't already exist.
     * @param $files - Array of file paths
     * @return string - [saved|failed]
     */
    public static function addToZipArchive($filename, $files){

        // This works with the correct build of php
        $zip = new ZipArchive;
        if ($zip->open($filename,ZipArchive::CREATE) === TRUE){
            foreach($files as $filepath){
                $zip->addFile($filepath, basename($filepath));
            }
            $zip->close();
            return 'saved';
        } else {
            return 'failed';
        }

    }

}