<?php
namespace Realm;

abstract class Configuration {

    /* Load Configuration from file */
    public static function Load($config_name) {
        /* Clean the config name and set the configuration file path and configuration variable */
        $config_name     = ltrim($config_name, '\\');
        $file_name       = str_replace('_', DIRECTORY_SEPARATOR, $config_name) . '.json';
        $file_name       = strtolower(SETTINGS . DIRECTORY_SEPARATOR . $file_name);

        /* Verify if the file exists */
        if (!is_readable($file_name)) {
            return null;
        }

        /* Return the configuration in array */
        $retval = json_decode(file_get_contents($file_name), true);
        return $retval;
    }
}
