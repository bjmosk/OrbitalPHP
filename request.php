<?php

namespace OrbitalPHP;
use OrbitalPHP\Client as OrbitalPHP;
use OrbitalPHP\Exception as OrbitalPHPException;

/**
 * Object representation of an Orbital gateway XML request.
 * 
 * @author Brian Moskowitz <bjmosk@gmail.com>
 * @package OrbitalPHP
 */
class Request
{
	/**
	 * Orbital XML request type.
	 * 
	 * @var string
	 */
	private $_type;

	/**
	 * The XML payload that is generated from the values provided.
	 * 
	 * @var string
	 */
	private $_xml;

	/**
	 * Loads an \OrbitalPHP\Request object of the given type and constructs
	 * the XML from the values provided.
	 * 
	 * @param string $type Orbital XML request type.
	 * @param array $values Key-value pairs for the XML elements, can be nested.
	 * @throws OrbitalPHPException
	 */
	public function __construct($type, $values) 
	{
		if (!in_array($type, OrbitalPHP::validTypes())) {
			throw new OrbitalPHPException('Invalid request type provided');
		} else if (!is_array($values)) {
			throw new OrbitalPHPException('Request values must be an array');
		} else if (!$values) {
			throw new OrbitalPHPException('No request values provided');
		}

		// Set the request type
		$this->_type = $type;

		// Compose and set full XML
		$this->_setXml($values);
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
	 * Returns the request XML.
	 * 
	 * @return string
	 */
	public function getXml()
	{
		return $this->_xml;
	}

	/**
	 * Compose the XML to be sent to the Orbital gateway.
	 * 
	 * @param array $values Key-value pairs for the XML elements, can be nested.
	 */
	private function _setXml($values)
	{
		$this->_xml = 
			'<?xml version="1.0" encoding="UTF-8"?>'
		.	'<Request>'
		.		"<{$this->getType()}>"
		.			$this->_composeRequestElements($values)
		.		"</{$this->getType()}>"
		.	'</Request>';
	}

	/**
	 * Composes the individual request elements. Called recursively for nested
	 * values.
	 * 
	 * @param array $values Key-value pairs for the XML elements, can be nested.
	 * @return string The generated XML.
	 * @throws OrbitalPHPException
	 */
	private function _composeRequestElements($values)
	{
		$requestElements = '';

		foreach ($values as $key => $value) {
			if (is_string($value) || is_numeric($value)) {
				$requestElements .= "<$key>$value</$key>";
			} else if (is_array($value)) {
				// call recursively
				$requestElements .=
					"<$key>{$this->_composeRequestElements($value)}</$key>";
			} else {
				throw new OrbitalPHPException("Invalid XML value for key $key");
			}
		}

		return $requestElements;
	}

}
