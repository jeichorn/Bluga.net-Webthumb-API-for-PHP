<?php

class Bluga_Webthumb_Request {

	public static $USE_PRETTY_PRINT = false;

	public $apikey;
	public $apiversion = 2;
	public $jobs = array();

	public function __construct($apikey = null) {
		$this->apikey = $apikey;
	}

	protected function render() {
		$xml = new SimpleXMLElement('<webthumb/>');

		$xml->apikey = $this->apikey;
		$xml->version = $this->apiversion;

		return $xml;
	}

	public function __toString() {
		return $this->render()->asXML();
	}

	public function asXML() {
		return Bluga_Webthumb_Request::prettyPrint($this->render()->asXML());
	}

	public static function prettyPrint($xml) {

		if (self::$USE_PRETTY_PRINT && class_exists('DOMDocument'))
		{
			$doc = new DOMDocument('1.0');
			$doc->formatOutput = true;
			if ($xml instanceof SimpleXMLElement) {
				$domnode = dom_import_simplexml($xml);
				$domnode = $doc->importNode($domnode, true);
				$domnode = $doc->appendChild($domnode);
			}
			else {
				$doc->loadXML($xml);
			}
			return $doc->saveXML();
		}
		else
		{
			return $xml->asXML();	
		}
	}
}
