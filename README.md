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

- `GET` request with no arguments passed


    ```
    curl -i http://api.url.com/testcase/index
     
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
- `GET` request with arguments passed


    ```
    curl -i http://api.url.com/testcase/index/title/Name+of+the+wind/type/paperback
    
    HTTP/1.1 200 Success
    Date: Thu, 04 Jul 2013 19:37:34 GMT
    Server: Apache
    Transfer-Encoding: chunked
    Content-Type: application/json

    {
        "result":"Test successful",
        "method":"get",
        "format":"json",
        "arguments":{
            "title":"Name of the Wind",
            "type":"paperback"
        }
    }
    ```
    
- `POST` request with arguments in the URL and POST, returned as XML


    ```
    curl -iX POST http://api.jerlance.com/v1/testcase/index/title/Name+of+the+Wind.xml -dtype=paperback -dprice=4.99

    HTTP/1.1 200 Success
    Date: Thu, 04 Jul 2013 19:42:04 GMT
    Server: Apache
    Vary: Accept-Encoding
    Transfer-Encoding: chunked
    Content-Type: text/xml

    <?xml version="1.0"?>
    <response>
        <result>Test successful</result>
        <method>post</method>
        <format>xml</format>
        <arguments>
            <type>paperback</type>
            <price>4.99</price>
            <title>Name of the Wind</title>
        </arguments>
    </response>
    ```


