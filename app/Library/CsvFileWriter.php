<?php

namespace App\Library;


class CsvFileWriter {
    private $filePath;
    private $headers;
    private $headerIds;
    private $headerNames;
    private $nullValue = 'NA';
    private $replacements = [
        'DK' => 'Dont_Know',
        'RF' => 'Refused',
        'respondent_id' => 'respondent_master_id'
    ];
    private $file;

    public function __construct (String $filePath, $headers) {
        $this->filePath = $filePath;
        $this->headers = $headers;
        $this->headerIds = [];
        $this->headerNames = [];
        foreach ($headers as $id => $name){
            array_push($this->headerIds, $id);
            array_push($this->headerNames, $name);
        }
    }

    public function setReplacements ($replacements) {
        $this->replacements = $replacements;
    }

    public function writeHeader () {
        fputcsv($this->file, $this->headerNames);
    }

    public function open () {

        if(!file_exists(dirname($this->filePath))){
            mkdir(dirname($this->filePath), 0777, true);
        }

        $this->file = fopen($this->filePath, 'w');

        // Write the file encoding header -> https://stackoverflow.com/questions/21988581/write-utf-8-characters-to-file-with-fputcsv-in-php
        fprintf($this->file, chr(0xEF).chr(0xBB).chr(0xBF));

    }

    public function writeRow ($rowMap) {
        $row = [];
        foreach ($this->headerIds as $id){
            if(isset($rowMap[$id])
                && $rowMap[$id] !== ''){
                if(is_string($rowMap[$id]) && array_key_exists($rowMap[$id], $this->replacements)){
                    array_push($row, $this->replacements[$rowMap[$id]]);
                } else {
                    array_push($row, $rowMap[$id]);
                }
            } else {
                array_push($row, $this->nullValue);
            }
        }
        fputcsv($this->file, $row);
    }

    public function writeRows (&$rows) {
        foreach ($rows as $row) {
            $this->writeRow($row);
        }
    }

    public function close () {
        if (isset($this->file)) {
            fclose($this->file);
        }
    }

}