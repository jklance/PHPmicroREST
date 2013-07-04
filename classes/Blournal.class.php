<?php
/**
 * Blournal extends the base Rest class to access blog posts
 *
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

/**
 * Include a file supplying necessary database credentials
 *
 * @param string $dbHost    hostname for the database
 * @param string $dbBase    database name
 * @param string $dbUser    username for the database
 * @param string $dbPass    password for username supplied
 */
require_once('../../scripts/ibloviateCreds.php');

class Blournal extends Rest {
    /**
     * Test function to verify that this endpoint works
     */
    public function index_get() {
        echo 'Nothing to see here';
    }

    /**
     * Retrieve a single blog post by identifier 
     *
     */
    public function entry_get() {
        echo 'TEST';
        print_r($this->getArguments());
    }

}
