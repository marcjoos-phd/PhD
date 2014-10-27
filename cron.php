#!/usr/local/bin/php.ORIG.5
<?php
define('_VALID_INCLUDE', TRUE);
//include_once("includes/opendb.php");
//include_once("includes/closedb.php");
function add_include_path ($path) {
    foreach (func_get_args() AS $path) {
        if (!file_exists($path) OR (file_exists($path) && filetype($path) !== 'dir')) {
            trigger_error("Include path '{$path}' not exists", E_USER_WARNING);
            continue;
        }
        $paths = explode(PATH_SEPARATOR, get_include_path());
        if (array_search($path, $paths) === false)
            array_push($paths, $path);
        set_include_path(implode(PATH_SEPARATOR, $paths));
    }
}
add_include_path("includes/");
include_once("webservices/auddas_account.php");
Account::check_subscription_all();
?>
