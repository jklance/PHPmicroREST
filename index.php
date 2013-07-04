<?php
/**
 * PHPmicroREST
 *
 * @package     PHPmicroREST
 * @version     0.1
 * @author      Jer Lance <me@jerlance.com>
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
require('classes/Rest.class.php');

/**
 * Core functionality
 */
$urlParts = getUrlParts();
$className = $urlParts[0];
$controller = $urlParts[1];
$arguments = count($urlParts) > 2 ? $urlParts[2] : '';

$front = new $className();
$front->{$controller}($arguments);


/**
 * Loads a class if it exists and isn't loaded
 *
 * @param string $class name of the class
 */
function __autoload($class) {
    $classFile = 'classes/' . $class . '.class.php';

    if (file_exists($classFile)) {
        require($classFile);
    } else {
        setStatusHeader('404', 'Not Found');
        exit;
    }
}

/**
 * Returns the front and controller in an array
 * 
 * @return array    Returns array of front[0] and controller[1]
 */
function getUrlParts() {
    $urlParts = explode('/', strtolower($_GET['req']));
    $returnParts = array();

    if ($urlParts[0]) {
        array_push($returnParts, ucfirst($urlParts[0]));
    } else {
        exit;
    }
    if (count($urlParts) > 1) {
        array_push($returnParts, $urlParts[1]);
    } else {
        array_push($returnParts, 'index');
    }
    if (count($urlParts) > 2) {
        $arguments = '';
        for($i = 2; $i < count($urlParts); $i += 2) {
            $arguments .= $urlParts[$i];
            if (count($urlParts) > ($i + 1)) {
                $arguments .= '=' . $urlParts[$i + 1];
            }
            if (count($urlParts) > ($i + 2)) {
                $arguments .= '&';
            }
        }
        array_push($returnParts, $arguments);
    }
    return($returnParts);   
}

/**
 * Puts the appropriate header on the response 
 * 
 * @param string $code The numeric HTTP response code
 * @param string $text The message for the response (default: null)
 */
function setStatusHeader($code, $text = '') {
    $server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;

    if (substr(php_sapi_name(), 0, 3) == 'cgi') {
        header("Status: $code $text", TRUE);
    } elseif ($server_protocol == 'HTTP/1.0') {
        header("HTTP/1.0 $code $text", TRUE, $code);
    } else {
        header("HTTP/1.1 $code $text", TRUE, $code);
    }
}

