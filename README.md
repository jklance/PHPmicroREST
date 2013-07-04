PHPmicroREST
=====

Very small, simple to use PHP REST server

Features
-----

- Simple to create your own fronts, controllers, and actions using PHP functions
  - A template class (Testcase.class.php) has been included with instructions
    and examples.
- Supports `GET`, `POST`, `PUT`, and `DELETE`
- Responses in `JSON` (default), `XML`, or `raw` PHP dump

Requirements
-----

- PHP 5.2 or greater (possibly less, but I know it works on 5.2)
- Apache (for included .htaccess configuration)

Sample Calls
-----
Some example calls using the included Testcase front:

- `GET` request with no data transmitted
  ```curl -i http://api.url.com/testcase/index

  HTTP/1.1 200 Success
  Date: Thu, 04 Jul 2013 19:21:13 GMT
  Server: Apache
  Transfer-Encoding: chunked
  Content-Type: application/json

  {
    "result":"Test successful",
    "method":"get",
    "format":"json",
    "arguments":""
  }
  ```

