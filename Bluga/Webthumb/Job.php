<?php

class Bluga_Webthumb_Job {

	public $options = null;
	public $status = null;
	public $user = array();

	public function __construct() {
		$this->options = new Bluga_Propertybag(array(
			'url',
			'outputType',
			'width',
			'height',
			'fullthumb',
			'customThumbnail',
			'effect',
			'delay',
			'excerpt',
			'notify',
			));
		$this->options->setters['url'] = create_function('$url','return trim($url);');
		$this->status = new Bluga_Propertybag(array(
			'start_time',
			'end_time',
			'est_time',
			'status',
			'cost',
			'pickup',
			'id',
			));
	}

	public function render($parent = null) {
		if (is_null($parent)) {
			$xml = new SimpleXMLElement('<request />');
		}
		else {
			$xml = $parent->addchild('request');
		}

		foreach($this->options as $key => $value) {
			switch($key) {
			case 'excerpt':
				$n = $xml->addchild('excerpt');
				foreach($value as $k => $v) {
					$n->$k = $v;

				}
				break;
			case 'customThumbnail':
				$xml->$key = '';
				foreach($value as $k => $v) {
					$xml->$key->addAttribute($k,$v);
				}
				break;
			case 'notify':
				if (is_array($value))
				{
					$xml->$key = $value['url'];
					foreach($value as $k => $v) {
						if ($k == 'url')
							continue;
						$xml->$key->addAttribute($k,$v);
					}
				}
				else
				{
					$xml->$key = $value;
				}
				break;
			default:
				$xml->$key = $value;
				break;
			}
		}
		return $xml;
	}

	public function asXml() {
		return Bluga_Webthumb_Request::prettyPrint($this->render());
	}
}

