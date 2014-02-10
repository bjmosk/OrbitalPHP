<?php

namespace OrbitalPHP;
use OrbitalPHP\Client as OrbitalPHP;
use OrbitalPHP\Exception as OrbitalPHPException;

/**
 * Object representation of an Orbital gateway XML response.
 * 
 * @author Brian Moskowitz <bjmosk@gmail.com>
 * @package OrbitalPHP
 */
class Response
{
	/**
	 * Orbital XML request type.
	 * 
	 * @var string
	 */
	private $_type;

	/**
	 * The raw response data that the object will be constructed from.
	 * 
	 * @var string
	 */
	private $_response;

	/**
	 * Array representing the XML response values.
	 * 
	 * @var array
	 */
	private $_data;

	/**
	 * Loads an \OrbitalPHP\Request object of the given type and constructs
	 * a data array representing the raw response provided.
	 * 
	 * @param string $type Orbital XML request type.
	 * @param string $response Raw Orbital response data.
	 * @throws OrbitalPHPException
	 */
	public function __construct($type, $response)
	{
		if (!in_array($type, OrbitalPHP::validTypes())) {
			throw new OrbitalPHPException('Invalid response type provided');
		} else if (!is_string($response) || 
		    strpos($response, 'HTTP/1.1') !== 0 || 
		    strpos($response, '<?xml') === false
		) {
			throw new OrbitalPHPException('Invalid response data');
		}

		// Store the response type
		$this->_type = $type;

		// Store the raw response data
		$this->_response = $response;

		// Turn the XML into an array
		$this->_setData($response);
	}

	/**
	 * Returns the XML request type.
	 * 
	 * @return string
	 */
	public function getType()
	{
		return $this->_type;
	}

	/**
	 * Returns the raw Orbital response data.
	 * 
	 * @return string
	 */
	public function getResponse()
	{
		return $this->_response;
	}

	/**
	 * Parse the HTTP status code from the raw Orbital response data.
	 * 
	 * @return integer The HTTP status code.
	 * @throws OrbitalPHPException
	 */
	public function getHttpStatusCode()
	{
		// Parse the status code from the first line of the response
		$statusCode = substr($this->_response, 9, 3);
		if (!is_numeric($statusCode) || strlen(trim($statusCode)) != 3) {
			throw new OrbitalPHPException('Could not parse HTTP status code');
		}
		
		return (integer) $statusCode;
	}

	/**
	 * Parses and returns the XML portion of the raw Orbital response.
	 * 
	 * @return string
	 */
	public function getXml()
	{
		return substr($this->_response, strpos($this->_response, '<?xml'));
	}

	/**
	 * Turns the raw XML response into an array.
	 * 
	 * @param string $response The XML response data.
	 */
	private function _setData($response)
	{
		// Neat trick to take advantage of json_encode's ability to handle XML
		// We use array_shift to disregard the initial XML wrapping element
		$this->_data = array_shift(
			json_decode(
				json_encode(simplexml_load_string($this->getXml())
			), true)
		);
	}

	/**
	 * Parse the ProcStatus from the response data and return as integer.
	 * 
	 * @return integer|boolean The ProcStatus as integer or false on failure
	 */
	public function getProcStatus()
	{
		$procStatus = $this->getData('ProcStatus');
		return is_numeric($procStatus) ? (integer) $procStatus : false;
	}

	/**
	 * Retrives the XML data array or an individual value if a key is provided.
	 * 
	 * @param string $key The key for the value to return, corresponds to the
	 *                    XML element name.
	 * @return array|string|boolean Returns the value for the key provided. If 
	 *                              no key provided, returns the full data 
	 *                              array. Returns false if invalid key given.
	 * @throws OrbitalPHPException
	 */
	public function getData($key = '')
	{
		if (!$key || !is_string($key)) {
			return $this->_data;
		} else {
			return isset($this->_data[$key]) ? $this->_data[$key] : false;
		}
	}

	/**
	 * Checks whether the response indicates the request was successful.
	 * 
	 * @return boolean True if the response is successful, false otherwise.
	 */
	public function isOk()
	{
		$statusCode = $this->getHttpStatusCode();
		$procStatus = $this->getProcStatus();

		return $statusCode == 200 && $procStatus === 0;
	}
}
