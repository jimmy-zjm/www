<?php
include (dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Chinapay/util/Settings.php');

class Settings_INI extends Settings
{
   function load($file=NULL)
    {
        if (file_exists($file) == false) {
            return false;
        }
        $this->_settings = parse_ini_file($file, true);
    }
}