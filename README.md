### OrbitalPHP
---
OrbitalPHP is a PHP library that simplifies interaction with Chase Paymentech's Orbital Payment Gateway.

It eliminates the clutter of XML generation and parsing.  Easily compose your requests, check for success, and retrieve response data.

The library follows the Orbital Gateway XML interface specification version 5.8.

---
### Usage
**1) Create an OrbitalPHP client object.**

    // In addition to the sockethost and merchant ID, anonymous functions can be
    // provided that will be run immediately before and after the request is sent
    $orbitalPHP = new \OrbitalPHP\Client(
        'ssl://orbitalvar1.paymentech.net:443', // sockethost
        123451234512,                           // merchant id
        function($request) {},                  // onbeforesend (optional)
        function($request, $response) {}        // onaftersend (optional)
    );

**2) Create a request of the desired type.**  

    // Returns an \OrbitalPHP\Request object
    // The array's key-value pairs correspond to XML elements and their values
    $request = $orbitalPHP->newOrder(array(
        'AccountNum' => '1111222233334444',
        'CardSecVal' => '111',
        'Exp' => '0915',
        // ...
    ));

Methods are provided for all Orbital request types in lowerCamelCase format, i.e. `newOrder()`, `markForCapture()`, `reversal()`, etc.

**3) Send the request and get the response.**

    // Returns an \OrbitalPHP\Response object
    // Also triggers the onbeforesend and onaftersend events
    $response = $orbitalPHP->send($request, $traceNumber);

    // Get some response details
    $statusCode = $response->getHttpStatusCode();
    $procStatus = $response->getProcStatus();

    // Check for success
    $success = $statusCode == 200 && $procStatus === 0;

    // Or, simply
    if ($response->isOk()) {

        // Check an individual XML element
        $txRefNum = $response->getData('TxRefNum');

        // Get the entire response as an array
        $data = $response->getData();

    }
---

The library also contains a handy method to perform a mod10 check to validate a credit card number.

    if (\OrbitalPHP\Client::mod10(1111222233334444)) {
        // card number is valid
    }

See `sample.php` in the repository for a full usage example.  The PDF with the full Orbital XML specification is also included in the `/doc` folder of the repo. 

### Dependencies
 * PHP 5.3+ w/ libxml extension
 * A valid Chase Paymentech merchant account

### Contributing
Everyone is free to contribute. Just fork the repository and submit your pull request.