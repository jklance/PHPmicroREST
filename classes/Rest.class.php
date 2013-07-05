<?php
/**
 * Rest is an abstract class to serve as a base for working classes
 *
 * @abstract
 * @package     PHPmicroREST
 * @version     1.0
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

abstract class Rest {
    /**
     * @var string the method by which the request is being made
     */
    private $_method;

    /**
     * @var array the arguments passed with the request
     */
    private $_arguments = array();

    /**
     * @var string the format requested for the response
     */
    private $_format;

    /**
     * Constructor stores the method and the correct arguments
     *
     * @param array $components rest components passed as an array, elements:
     *  - string [format]     the format of the return response
     *  - array  [arguments]  array of passed GET arguments
     *  - string [front]      front name of REST call
     *  - string [controller] name of REST controller called
     */
    function __construct($components) {
        $this->_method    = $this->_getRequestMethod();
        $this->_arguments = $this->_getRequestArguments($this->_method, $components['arguments']);
        $this->_format    = $components['format'];
    }

    /**
     * Either call the appropriate controller method or show an error
     */
    function __call($name, $args) {
        $action = $name . '_' . $this->_method;
        $results = '';

        if (method_exists($this, $action)) {
            $results = $this->$action();
        } else {
            $results = array(
                array( 'error'  => 'Unknown endpoint ' . $name ),
                array( 'status' => '404', 'message' => 'Not found' )
            );
        }
        
        $this->_sendResponse($results);
    }

/*************************************************************************************************
 * BEGIN PUBLICLY ACCESSIBLE METHOD CALLS
 *************************************************************************************************/

    /**
     * Creates response array from the result, status code, and message
     *
     * @param  array  $result    Associative array with the response data payload
     * @param  string $code      HTML status code
     * @param  string $message   status message for result     
     * @return array             properly formed response package
     */
    public function createResponse($result, $code = '200', $message = 'Success') {
        $response = array(
            $result,
            array(
                'status'  => $code,
                'message' => $message
            )
        );
        return($response);
    }

    /**
     * @return string method by which the request was called
     */
    public function getMethod() {
        return($this->_method);
    }

    /**
     * @return array arguments passed with the request as an associative array
     */
    public function getArguments() {
        return($this->_arguments);
    }

    /**
     * @return string format to return results in
     */
    public function getFormat() {
        return($this->_format);
    }

/*************************************************************************************************
 * END OF PUBLICLY AVAILABLE METHOD CALLS
 *************************************************************************************************/

    /**
     * Format and return the response to the call
     *
     * @param array $results  the reponse package as an associative array with elements:
     *  - array     [0]       the data payload to return as an associative array
     *  - array     [1]       the response status info containing elements:
     *    - string  [status]  the HTML status code
     *    - string  [message] the HTML status message
     */
    private function _sendResponse($results) {
        $this->_setStatusHeader($results[1]['status'], $results[1]['message']); 
        
        $action = '_sendResponse_' . $this->_format;
        $this->{$action}($results[0]);
        exit;
    }

    /**
     * Output response in raw format
     */
    private function _sendResponse_raw($results) {
        print_r($results);
    }
    /**
     * Output response in json format
     */
    private function _sendResponse_json($results) {
        header("Content-type:application/json");
        echo json_encode($results);
    }
    /**
     * Output response in xml format
     */
    private function _sendResponse_xml($results) {
        header("Content-type:text/xml");
        $outXml = new SimpleXMLElement('<?xml version="1.0"?><response></response>');
        $this->_convertArrayToXml($results, $outXml);

        echo $outXml->asXML();
    }

    /**
     * Convert a passed array into a passed SimpleXML object
     *
     * @param array            $inArray  the array to convert to XML
     * @param SimpleXMLElement $outXml   XML element to update passed as reference
     */
    private function _convertArrayToXml($inArray, &$outXml) {
        if (!is_array($inArray)) {
            return;
        }

        foreach($inArray as $k => $v) {
            if (is_numeric($k)) {
                $k = 'element_' . $k;
            }

            if (is_array($v)) {
                $childNode = $outXml->addChild($k);
                $this->_convertArrayToXml($v, $childNode);
            } else {
                $v = htmlentities($v);
                $outXml->addChild($k, $v);
            }
        }
    } 

    /**
     * Retrieve the appropriate arguments for the request
     *
     * @param  string $method  the method by which the request was made
     * @return array           arguments stored in an associative array
     */
    private function _getRequestArguments($method, $passedArgs) {
        $args = '';

        switch($method) {
            case 'get':
                // We don't do anything here, get is in $passedArgs
                break;
            case 'post':
                $args = $_POST;
                break;
            case 'put':
                parse_str(file_get_contents('php://input'), $args);
                break;
            case 'delete':
                parse_str(file_get_contents('php://input'), $args);
                break;
            default:
                $args = $_REQUEST;
        }
        if (isset($args) && is_array($args) && is_array($passedArgs)) {
            $args = array_merge($args, $passedArgs);
        } elseif (is_array($passedArgs)) {
            $args = $passedArgs;
        } 
        
        return($args);
    }

    /**
     * Find the method by which the request is being sent
     *
     * @return string the method being used (get, post, put, or delete)
     */
    private function _getRequestMethod() {
        $validMethods = array('get', 'post', 'delete', 'put');
        $requestMethod = strtolower($_SERVER['REQUEST_METHOD']);

        if (!in_array($requestMethod, $validMethods)) {
            $requestMethod = 'get';
        }

        return($requestMethod);
    }

    /**
     * Puts the appropriate header on the response
     *
     * @param string $code The numeric HTTP response code
     * @param string $text The message for the response (default: null)
     */
    private function _setStatusHeader($code, $text = '') {
        $server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : false;

        if (substr(php_sapi_name(), 0, 3) == 'cgi') {
            header("Status: $code $text", TRUE);
        } elseif ($server_protocol == 'HTTP/1.0') {
            header("HTTP/1.0 $code $text", TRUE, $code);
        } else {
            header("HTTP/1.1 $code $text", TRUE, $code);
        }
    }
}
