<?php

namespace App\Services;

use Exception;
use ZipArchive;

class FileService{

    /**
     * Generates a csv file from a hashmap of unique column keys mapped to column names. Each row is a hashmap mapping
     * the column keys to the row value for that column.
     * @param $colMap - A hashmap of id => name. The name is what ends up in the column header.
     * @param $rowMaps - An array of hashmaps for each row. Hashmap keys should correspond to the $colMap keys.
     * @param $filePath - The path to the csv file.
     * @param $nullValue - The string to use in place of null and empty strings.
     * @param $replacements - A HashMap of strings to replace. Only exact matches are replaced.
     */
    public static function writeCsv($colMap, $rowMaps, $filePath, $nullValue='NA', $replacements=['DK'=>'Dont_Know','RF'=>'Refused','respondent_id'=>'respondent_master_id']){

        $headerIds = [];
        $headerNames = [];
        foreach ($colMap as $id => $name){
            array_push($headerIds, $id);
            array_push($headerNames, $name);
        }

        if(!file_exists(dirname($filePath))){
            mkdir(dirname($filePath), 0777, true);
        }

        $file = fopen($filePath, 'w');
        // Write the file encoding header -> https://stackoverflow.com/questions/21988581/write-utf-8-characters-to-file-with-fputcsv-in-php
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        // Make any replacements on the column headers
        foreach($headerNames as $index => $name){
            if(isset($replacements[$name])){
                $headerNames[$index] = $replacements[$name];
            }
        }

        // Write headers
        fputcsv($file, $headerNames);
        foreach ($rowMaps as $rowMap){
            $row = [];
            foreach ($headerIds as $id){
                if(isset($rowMap[$id])
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

  /**
   * Copy a file from a zip archive into the file system with a new name.
   * @param $zip
   * @param $entryName
   * @param $fileName
   * @throws Exception
   */
    public static function copyFromZip ($zip, $entryName, $fileName) {
      $fp = $zip->getStream($entryName);
      if(!$fp) {
        throw new Exception("No entry with the name, $entryName exists in this zipfile");
      }

      $contents = '';
      while (!feof($fp)) {
        $contents .= fread($fp, 2);
      }

      fclose($fp);
      file_put_contents($fileName, $contents);
    }

}