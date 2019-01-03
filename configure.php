<?php

# Get http server
$http_server_text = stripcslashes($_SERVER['SERVER_SOFTWARE']);

# Set apache as a valid http server
$http_server_valid = 0;
if (strpos($http_server_text, 'Apache') !== false) {
    $http_server_valid = 1;
}


# Check if apache rewrite module is enabled
$http_server_rewrite_text = "No. Rewrite module must be enabled '# a2enmod rewrite'.";
$http_server_rewrite_valid = 0;

# If http server is not Apache, does not need to check
if ($http_server_valid != 0) {
    if (in_array('mod_rewrite', apache_get_modules())) {
        $http_server_rewrite_text = "Yes";
        $http_server_rewrite_valid = 1;
    };
}


# Check php version
$php_version_text = phpversion();
$php_version_valid = 0;

# Set php 7.0+ as valid and php 5.3+ as acceptable
if (version_compare($php_version_text, '7.0.0') >= 0) {
    $php_version_valid = 1;
} else if (version_compare($php_version_text, '5.3.0') >= 0) {
    $php_version_valid = 2;
}


# Check write permission on settings folder
$user = posix_getpwuid(posix_geteuid());
$username = $user['name'];

$write_on_settings_text = "Denided. Create a folder 'settings' on your 'realm' folder and give write permission to user '" . $username . "'";
$write_on_settings_valid = 0;
$settings_folder = '.' . DIRECTORY_SEPARATOR . 'settings';

if (file_exists($settings_folder) && is_writable($settings_folder)) {
    $write_on_settings_text = "Allowed";
    $write_on_settings_valid = 1;
}


header("Content-Type: text/plain");

echo "http_server:" . $http_server_valid . ":" . $http_server_text . "\n";
echo "http_server_rewrite:" . $http_server_rewrite_valid . ":" . $http_server_rewrite_text . "\n";
echo "php_version:" . $php_version_valid . ":" . $php_version_text . "\n";
echo "write_on_settings:" . $write_on_settings_valid . ":" . $write_on_settings_text . "\n";
