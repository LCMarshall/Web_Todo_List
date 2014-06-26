<?php

class Filestore {

    public $filename = '';
    public $is_csv = false;

    function __construct($filename = '') 
    {
        $this->filename = $filename;

        if (substr($this->filename, -3) == 'csv') {
            $this->is_csv = true; 
        }
            
        // Sets $this->filename
    }
    
    public function read()
    {
        
        if ($this->is_csv) {
            return $this->read_csv();
        } else {
            return $this->read_lines();
        }

    }   
    // public function write($array){

    //     if ($this->is_csv) {
    //         $this->write_csv($array);
    //     } else {
    //         $this->write_lines($array);
    //     }
    // }


    /**
     * Returns array of lines in $this->filename
     */
    private function read_lines()
    {
        $handle = fopen($this->filename, 'r');
        $todo_string = fread($handle, filesize($this->filename));
        fclose($handle); 
        $ret=explode("\n", $todo_string);  
        return $ret;
    }

// }
    /**
     * Writes each element in $array to a new line in $this->filename
     */
    // private function write_lines ($array)
    // {
    //     if (is_writable($this->filename)) {        
    //     $handle = fopen($this->filename, 'w');
    //     fwrite($handle, implode("\n", $array));
    //     fclose($handle);        
    //     }   

    // }

    /**
     * Reads contents of csv $this->filename, returns an array
     */
    function read_csv()
    {
        $handle = fopen($this->filename, 'r');
        $address_book = [];

        while (!feof($handle)) {
            $row = fgetcsv($handle);
            if(is_array($row)) {
                $address_book[] = $row;
            }
        }
        fclose($handle); 
        return $address_book;
    }
    /**
     * Writes contents of $array to csv $this->filename
     */
    function write_csv($array)
    {

    }

}