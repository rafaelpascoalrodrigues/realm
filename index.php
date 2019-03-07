<?php
/* Set directory structure */
define('CORE',    'realm');
define('CLASSES', 'classes');
define('DOMAINS', 'domains');


/* Initialize classes autoload function */
spl_autoload_register(function($class) {
    /* Clean the class name and set the class file path */
    $class_parts = explode('\\', $class);
    $class_name = strtolower(array_pop($class_parts));
    $namespace  = strtolower(implode(DIRECTORY_SEPARATOR, $class_parts));

    if (empty($namespace)) {
        return false;
    } else if ($namespace == CORE) {
        $class_file =  CORE . DIRECTORY_SEPARATOR . $class_name . ".php";
    } else {
        $class_file = DOMAINS . DIRECTORY_SEPARATOR . $namespace . DIRECTORY_SEPARATOR . CLASSES .DIRECTORY_SEPARATOR . $class_name . ".php";
    }

    /* Try to include the core class file */
    $retval = @include_once $class_file;

    /* Increase the class load count */
    if (($retval == true) && isset($REALM)) {
        $REALM->classes_loaded += 1;
    }
});


/* Create the core object */
$REALM = new \Realm\Core();


return true;
