<?php
/**
 * PHPmicroREST ... index.php
 *
 * @package     PHPmicroREST
 * @version     1.1
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

error_reporting(E_ALL);

/**
 * Base abstract class for the rest endpoints
 */
require('classes/Rest.class.php');

define('REST_ARGS',      'phpmicrorest_rest_request_argument_list');
define('KEY_MAX_LENGTH', 50);
define('VAL_MAX_LENGTH', 250);


/**
 * Core functionality
 */

if (!isset($_GET[REST_ARGS])) { exit; }

$urlParts = getUrlParts();
$className = $urlParts['front'];
$controller = $urlParts['controller'];

$front = new $className($urlParts);
$front->{$controller}();


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
        setStatusHeader('404', 'Not found');
        exit;
    }
}

/**
 * @return array    Returns array of REST components
 */
function getUrlParts() {
    $returnParts = array(
        'front' => '',
        'controller' => 'index',
        'arguments' => '',
        'format' => popFormat(),
    );

    $urlParts = explode('/', $_GET[REST_ARGS]);
    if (!is_array($urlParts)) { 
        setStatusHeader('404', 'Not Found');
        exit;
    }
    
    $returnParts['front'] = parseUrlFront($urlParts);
    $returnParts['controller'] = parseUrlController($urlParts);
    $returnParts['arguments'] = parseUrlArguments($urlParts);

    return($returnParts);   
}

/**
 * @param  array  $urlParts the exploded URI string
 * @return string           the 'front' of the REST call
 */
function parseUrlFront($urlParts) {
    return(ucfirst(strtolower($urlParts[0])));
}

/**
 * @param  array  $urlParts the exploded URI string
 * @return string           the 'controller' of the REST call
 */
function parseUrlController($urlParts) {
    if (count($urlParts) > 1 && $urlParts[1]) {
        return(strtolower($urlParts[1]));
    } else {
        return('index');
    }
}

/**
 * Breaks the arguments passed in the URI into key/value pairs and validates the key formats
 * 
 * @param  array $urlParts  the exploded URI string
 * @return array            key/value pairs of arguments with validated keys
 */
function parseUrlArguments($urlParts) {
    $arguments = '';

    if (count($urlParts) > 2) {
        $arguments = array();
        
        for($i = 2; $i < count($urlParts); $i += 2) {
            if (isValidKey($urlParts[$i]) && count($urlParts) > ($i + 1) && $urlParts[$i + 1]) {
                $arguments[$urlParts[$i]] = $urlParts[$i + 1];
            }
        }
        $returnParts['arguments'] = $arguments;
    }
    return($arguments);
}

/**
 * Ensures that they keys are valid. Using XML tag naming rules as a boundary
 * 
 * @param  string  $key value to validate
 * @return boolean      whether the key is valid or not
 */
function isValidKey($key) {
    // Valix XML name
    if (!preg_match('/^(?!XML)[a-z][\w0-9-]*$/i', $key)) {
        return(false);
    }
    // Valid length 
    if (strlen($key) > KEY_MAX_LENGTH) {
        return(false);
    }
    return(true);
}        

/**
 * Ensures that they values are valid. 
 * 
 * @param  string  $val value to validate
 * @return boolean      whether the value is valid or not
 */
function isValidValue($val) {
    // Valid length 
    if (strlen($val) > VAL_MAX_LENGTH) {
        return(false);
    }
    return(true);
}        

/**
 * Removes the format from the end of the query string and returns it (or the default format)
 * Default format is the first element in the $validFormats array
 *
 * @return string requested valid format or default format
 */
function popFormat() {
    $validFormats = array('json', 'xml', 'raw');
    $requestFormat = end(explode('.', $_GET[REST_ARGS]));

    if (!in_array($requestFormat, $validFormats)) {
        $requestFormat = $validFormats[0];
    } else {
        $getArgs = explode('.', $_GET[REST_ARGS]);
        array_pop($getArgs);
        $_GET[REST_ARGS] = implode(',', $getArgs);
    }
    
    return($requestFormat);
}

/**
 * Puts the appropriate header on the response 
 * 
 * @todo This is a duplication of the one in the Rest base class. Fix that.
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

