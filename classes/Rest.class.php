<?php
/**
 * Rest is an abstract class to serve as a base for working classes
 *
 * @abstract
 * @package     PHPmicroREST
 * @version     0.1
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
    

    function __construct() {
        $this->_method = $this->_getRequestMethod();
        $this->_arguments = $this->_getRequestArguments($this->_method);

    }
    function __call($name, $args) {
        $action = $name . '_' . $this->_method;

        if (method_exists($this, $action)) {
            $this->$action();
        } else {
            setStatusHeader('404', 'Unknown action');
            exit;
        }
    }

    public function getArguments() {
        return($this->_arguments);
    }

    /**
     * Retrieve the appropriate arguments for the request
     *
     * @param  string $method  the method by which the request was made
     * @return array arguments stored in an associative array
     */
    private function _getRequestArguments($method) {
        $args = '';

        switch($method) {
            case 'get':
                $args = $_GET;
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
}
