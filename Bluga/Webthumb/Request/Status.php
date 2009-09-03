<?php

class Bluga_Webthumb_Request_Status extends Bluga_Webthumb_Request {

	public $jobs = array();

	protected function render($xml = null) {
		if (is_null($xml)) {
			$xml = parent::render($xml);
		}
		else {
			parent::render($xml);
		}

		$status = $xml->addChild('status');
		foreach($this->jobs as $job) {
			$j = $status->addChild('job',$job);
		}
		return $xml;
	}

	public function asXML() {
		return Bluga_Webthumb_Request::prettyPrint($this->render());
	}
}
