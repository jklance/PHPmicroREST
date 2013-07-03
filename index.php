<?php
/**
 * PHPmicroREST
 *
 * @package     PHPmicroREST
 * @version     0.1
 * @author      Jer Lance <me@jerlanc<me@jerlance.com>
 * @copyright   Copyright (c) 2013 Jer Lance (http://jerlance.com)
 * @license     http://opensource.org/licenses/LGPL-3.0 (LGPL 3.0)
 * 
 * This file is part of PHPmicroREST.
 *
 * PHPmicroREST is free software: you can redistribute it and/or modify it 
 * under the terms of the Lesser GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your 
 * option) any later version.
 *
 * PHPmicroREST is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE.  See the Lesser GNU General Public 
 * License for more details.
 *
 * You should have received a copy of the Lesser GNU General Public License
 * along with PHPmicroREST.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Error reporting
 */
error_reporting(E_ALL);

/**
 * Include a file supplying necessary database credentials
 *
 * @param string $dbHost    hostname for the database
 * @param string $dbBase    database name
 * @param string $dbUser    username for the database
 * @param string $dbPass    password for username supplied
 */
require('../../scripts/ibloviateCreds.php');

/**
 * Core functionality
 */
$urlParts = get_url_parts();
$className = $urlParts[0];
$controller = $urlParts[1];

$front = new $className();
$front->{$argument}();


/**
 * Loads a class if it exists and isn't loaded
 *
 * @param string $class name of the class
 */
function __autoload($class) {
    (require('classes/' . $class . 'class.php')) || set_status_header('404', 'Not Found');

    if (!class_exists($class, false)) {
        set_status_header('404', 'Not Found');
        exit;
    }
}

/**
 * Returns the front and controller in an array
 * 
 * @return array    Returns array of front[0] and controller[1]
 */
function get_url_parts() {
    $url = end(explode(basename(__FILE__), $_SERVER['PHP_SELF']));
    $urlParts = explode('/', strtolower(trim($url, '/')));
    
    if ($urlParts[0]) {
        $urlParts[0] = ucfirst($urlParts[0]);
    } else {
        exit;
    }
    if (!$urlParts[1]) {
        $urlParts[1] = 'index';
    }
    return($urlParts);   
}

/**
 * Puts the appropriate header on the response 
 * 
 * @param string $code The numeric HTTP response code
 * @param string $text The message for the response (default: null)
 */
function set_status_header($code, $text = '') {
    $server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;

    if (substr(php_sapi_name(), 0, 3) == 'cgi') {
        header("Status: $code $text", TRUE);
    } elseif ($server_protocol == 'HTTP/1.0') {
        header("HTTP/1.0 $code $text", TRUE, $code);
    } else {
        header("HTTP/1.1 $code $text", TRUE, $code);
    }
}

