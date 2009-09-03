<?php

class Bluga_Webthumb_Request_Submit extends Bluga_Webthumb_Request {

	public $jobs = array();

	protected function render($xml = null) {
		if (is_null($xml)) {
			$xml = parent::render($xml);
		}
		else {
			parent::render($xml);
		}

		foreach($this->jobs as $job) {
			$job->render($xml);
		}
		return $xml;
	}

	public function asXML() {
		return Bluga_Webthumb_Request::prettyPrint($this->render());
	}
}
