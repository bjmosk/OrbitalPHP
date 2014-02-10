<?php
/*
 * Copyright (c) 2014 Brian Moskowitz
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace OrbitalPHP;
use OrbitalPHP\Exception as OrbitalPHPException;

/**
 * OrbitalPHP client class to send XML requests to the Chase Paymentech Orbital
 * gateway.
 * 
 * Complies with Orbital gateway spec version 5.8, PDF is included with the
 * library.
 * 
 * @author Brian Moskowitz <bjmosk@gmail.com>
 * @package OrbitalPHP
 */
class Client
{
	/**
	 * Orbital gateway endpoint, ie. ssl://orbitalvar1.paymentech.net:443.
	 * 
	 * @var string
	 */
	private $_sockethost;

	/**
	 * The merchant ID of the Chase Paymentech account.
	 * 
	 * @var integer
	 */
	private $_merchantId;

	/**
	 * Function to be called before the request is sent to the Orbital gateway.
	 * 
	 * @var Closure Given an \OrbitalPHP\Request object as its only parameter.
	 */
	private $_onBeforeSend;

	/**
	 * Function to be called after the request is sent to the Orbital gateway.
	 * 
	 * @var Closure Given an \OrbitalPHP\Request and OrbitalPHP\Response object
	 *              as parameters.
	 */
	private $_onAfterSend;

	/**
	 * Loads and bootstraps an OrbitalPHP\Client object.
	 * 
	 * @param string $sockethost Orbital gateway endpoint, 
	 *                           ie. ssl://orbitalvar1.paymentech.net:443
	 * @param integer $merchantId The merchant ID of the Chase Paymentech 
	 *                            account.
	 * @param Closure $onBeforeSend Function to be called before the request is 
	 *                              sent to the Orbital gateway.
	 *                              Given an \OrbitalPHP\Request object as its 
	 *                              only parameters.
	 * @param Closure $onAfterSend Function to be called after the request is 
	 *                             sent to the Orbital gateway.
	 *                             Given an \OrbitalPHP\Request and 
	 *                             \OrbitalPHP\Response object as parameters.
	 * @throws OrbitalPHPException
	 */
	public function __construct($sockethost,
	                            $merchantId,
	                            $onBeforeSend = null,
	                            $onAfterSend = null
	) {
		require_once 'autoloader.php';

		if ($onBeforeSend && !is_callable($onBeforeSend)) {
			throw new OrbitalPHPException('Invalid onBeforeSend callback');
		} else if ($onAfterSend && !is_callable($onAfterSend)) {
			throw new OrbitalPHPException('Invalid onAfterSend callback');
		}

		$this->_sockethost = $sockethost;
		$this->_merchantId = $merchantId;
		$this->_onBeforeSend = $onBeforeSend;
		$this->_onAfterSend = $onAfterSend;
	}

	/**
	 * Retrieves an \OrbitalPHP\Request object representing an Orbital request.
	 * 
	 * @param string $type A valid Orbital XML request type.
	 * @param array $values An array of key-value pairs corresponding to XML
	 *                      elements of the given request type.
	 * @return \OrbitalPHP\Request
	 */
	public function getRequest($type, $values)
	{
		return new Request($type, $values);
	}

	/**
	 * Loads an \OrbitalPHP\Request object for an Orbital NewOrder request.
	 * 
	 * @param array $values An array of key-value pairs corresponding to XML
	 *                      elements of the given request type.
	 * @return \OrbitalPHP\Request
	 */
	public function newOrder($values)
	{
		return $this->getRequest('NewOrder', $values);
	}

	/**
	 * Loads an \OrbitalPHP\Request object for an Orbital MarkForCapture request.
	 * 
	 * @param array $values An array of key-value pairs corresponding to XML
	 *                      elements of the given request type.
	 * @return \OrbitalPHP\Request
	 */
	public function markForCapture($values)
	{
		return $this->getRequest('MarkForCapture', $values);
	}

	/**
	 * Loads an \OrbitalPHP\Request object for an Orbital Reversal request.
	 * 
	 * @param array $values An array of key-value pairs corresponding to XML
	 *                      elements of the given request type.
	 * @return \OrbitalPHP\Request
	 */
	public function reversal($values)
	{
		return $this->getRequest('Reversal', $values);
	}

	/**
	 * Loads an \OrbitalPHP\Request object for an Orbital EndOfDay request.
	 * 
	 * @param array $values An array of key-value pairs corresponding to XML
	 *                      elements of the given request type.
	 * @return \OrbitalPHP\Request
	 */
	public function endOfDay($values)
	{
		return $this->getRequest('EndOfDay', $values);
	}

	/**
	 * Loads an \OrbitalPHP\Request object for an Orbital Inquiry request.
	 * 
	 * @param array $values An array of key-value pairs corresponding to XML
	 *                      elements of the given request type.
	 * @return \OrbitalPHP\Request
	 */
	public function inquiry($values)
	{
		return $this->getRequest('Inquiry', $values);
	}

	/**
	 * Loads an \OrbitalPHP\Request object for an Orbital Profile request.
	 * 
	 * @param array $values An array of key-value pairs corresponding to XML
	 *                      elements of the given request type.
	 * @return \OrbitalPHP\Request
	 */
	public function profile($values)
	{
		return $this->getRequest('Profile', $values);
	}

	/**
	 * Loads an \OrbitalPHP\Request object for an Orbital FlexCache request.
	 * 
	 * @param array $values An array of key-value pairs corresponding to XML
	 *                      elements of the given request type.
	 * @return \OrbitalPHP\Request
	 */
	public function flexCache($values)
	{
		return $this->getRequest('FlexCache', $values);
	}

	/**
	 * Loads an \OrbitalPHP\Request object for an Orbital AccountUpdater 
	 * request.
	 * 
	 * @param array $values An array of key-value pairs corresponding to XML
	 *                      elements of the given request type.
	 * @return \OrbitalPHP\Request
	 */
	public function accountUpdater($values)
	{
		return $this->getRequest('AccountUpdater', $values);
	}

	/**
	 * Loads an \OrbitalPHP\Request object for an Orbital SafetechFraudAnalysis 
	 * request.
	 * 
	 * @param array $values An array of key-value pairs corresponding to XML
	 *                      elements of the given request type.
	 * @return \OrbitalPHP\Request
	 */
	public function safetechFraudAnalysis($values)
	{
		return $this->getRequest('SafetechFraudAnalysis', $values);
	}

	/**
	 * Sends a request to the OrbitalPHP gateway and returns the response.
	 * Also triggers the onBeforeSend and onAfterSend events if provided.
	 * 
	 * @param \OrbitalPHP\Request $request
	 * @param type $traceNumber An ID to be used for retry logic.
	 * @return \OrbitalPHP\Response
	 * @throws OrbitalPHPException
	 */
	public function send(Request $request, $traceNumber)
	{
		if (!is_numeric($traceNumber)) {
			throw new OrbitalPHPException('Trace number must be numeric');
		}

		// Run onBeforeSend
		if ($this->_onBeforeSend) {
			call_user_func($this->_onBeforeSend, $request);
		}

		// Send the request
		$content_length = strlen($request->getXml());
		$fp = stream_socket_client($this->_sockethost, $errno, $errstr, 30);
		if (!$fp) {
			throw new OrbitalPHPException(
				'Could not connect to Orbital, stream_socket_client failed. '
			.	"Errno: $errno, Errstr: $errstr"
			);
		}

		fputs($fp, "POST /authorize HTTP/1.0\r\n");
		fputs($fp, "MIME-Version: 1.0\r\n");
		fputs($fp, "Content-type: application/PTI50\r\n");
		fputs($fp, "Content-length: $content_length\r\n");
		fputs($fp, "Content-transfer-encoding: text\r\n");
		fputs($fp, "Request-number: 1\r\n");
		fputs($fp, "Document-type: Request\r\n");
		fputs($fp, "Merchant-id: {$this->_merchantId}\r\n");
		fputs($fp, "Trace-number: $traceNumber\r\n");
		fputs($fp, "\r\n");
		fputs($fp, $request->getXml());

		// Get the response
		$orbitalResponse = '';
		while (!feof($fp)) {
			$orbitalResponse .= fgets($fp);
		}

		// Load response object
		$response = new Response($request->getType(), $orbitalResponse);

		// Run onAfterSend
		if ($this->_onAfterSend) {
			call_user_func($this->_onAfterSend, $request, $response);
		}

		// Return the response object
		return $response;
	}

	/**
	 * Defines the valid Orbital XML message types.
	 * 
	 * @return array Returns an array of valid XML message types.
	 */
	public static function validTypes()
	{
		return array(
			'NewOrder', 'MarkForCapture', 'Reversal', 'EndOfDay', 'Inquiry',
			'Profile', 'FlexCache', 'AccountUpdater', 'SafetechFraudAnalysis',
		);
	}

	/**
	 * Performs a mod 10 check on the credit card number provided.
	 * http://en.wikipedia.org/wiki/Luhn_algorithm
	 * 
	 * @param integer $cardNumber The credit card number to check, digits only.
	 * @return boolean True if passes mod 10 check, false otherwise.
	 */
	public static function mod10($cardNumber)
	{
		if (!is_numeric($cardNumber) || 
		    !preg_match('/^[0-9]*$/', $cardNumber)) {
			return false;
		}

		$cardNumber = (string) $cardNumber;
		$checkDigit = null;
		$multiplier = 1;
		$sum = 0;

		for ($i=strlen($cardNumber)-1; $i>=0; $i--) {

			if (!$checkDigit) {
				$checkDigit = $cardNumber[$i];
				continue;
			}

			$multiplier = $multiplier == 1 ? 2 : 1;
			$sum += array_sum(str_split($cardNumber[$i] * $multiplier));
		}

		return 10 - $sum % 10 == $checkDigit;
	}

}
