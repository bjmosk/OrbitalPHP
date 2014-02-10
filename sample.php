<?php

/*
 * This example uses OrbitalPHP to send a NewOrder request to the Chase
 * Paymentech Orbital Gateway and inspect the response.
 */

// Load OrbitalPHP
require_once '../lib/OrbitalPHP/orbitalphp.php';

// Set some convenient aliases
use OrbitalPHP\Client as OrbitalPHP;
use OrbitalPHP\Exception as OrbitalPHPException;

try {

	// Create an OrbitalPHP\Client object
	$orbitalPHP = new OrbitalPHP(
		'ssl://orbitalvar1.paymentech.net:443', // sockethost
		123451234512,                           // merchant id
		function($request) {},                  // onbeforesend
		function($request, $response) {}        // onaftersend
	);

	// Creates an OrbitalPHP\Request object of type NewOrder
	$request = $orbitalPHP->newOrder(array(
		'OrbitalConnectionUsername' => 'MYUSERNAME',
		'OrbitalConnectionPassword' => 'MYPASSWORD',
		'IndustryType' => 'EC',
		'MessageType' => 'AC',
		'BIN' => '000002',
		'MerchantID' => '123451234512',
		'TerminalID' => '001',
		'AccountNum' => '5454545454545454',
		'Exp' => '0915',
		'CurrencyCode' => '840',
		'CurrencyExponent' => '2',
		'CardSecVal' => '111',
		'OrderID' => time(),
		'Amount' => '100',
	));

	// Send the request and get response object
	$traceNumber = time();
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

} catch (OrbitalPHPException $e) {
	// Handle the exception
} catch (Exception $e) {
	// Handle the exception
}
