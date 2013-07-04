<?php
/**
 * Testcase extends the base Rest class to verify endpoints
 *
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

class Testcase extends Rest {

    /**
     * Test function to verify that calls to url.com/testcase/index work
     * 
     * This can serve as a template for new API endpoints. A function name of:
     *      public function {controller}_{method}() 
     * in class {Classname} will serve as a 'listener' to a url endpoint of:
     *      http://url.com/{classname}/{controller} 
     * using the specified method (get, post, put, delete).
     *
     * The call components are available through parent methods of the abstract 
     * Rest class. Some include:
     *  - getArguments()   the arguments passed in the request
     *  - getFormat()      the format that the caller requested for the response
     *  - createResponse() this will appropriately put headers on and format your response
     */
    public function index_get() {
        $result = $this->_createDataResult();
        return($this->createResponse($result));
    }
    public function index_post() {
        $result = $this->_createDataResult();
        return($this->createResponse($result, '200', 'Success'));
    }
    public function index_put() {
        $result = $this->_createDataResult();
        return($this->createResponse($result, '200', 'Success'));
    }
    public function index_delete() {
        $result = $this->_createDataResult();
        return($this->createResponse($result, '200', 'Success'));
    }
    public function book_get() {
        $result = array('book_title' => 'blah');
        return($this->createResponse($result, '200', 'Success'));
    }

    /**
     * Creates a response package including arguments and method
     *
     * @return array message, method, and request arguments for validation
     */
    private function _createDataResult() {
        $result = array(
            'result'    => 'Test successful',
            'method'    => $this->getMethod(),
            'format'    => $this->getFormat(),
            'arguments' => $this->getArguments(),
        );
        return($result);
    }
}
