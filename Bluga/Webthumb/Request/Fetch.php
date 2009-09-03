<?php

class Bluga_Webthumb_Request_Fetch extends Bluga_Webthumb_Request {

	public $job = null;
	public $size = null;

	public function __construct($job = null,$size = null) {
		$this->job = $job;
		$this->size = $size;
	}

	/**
	 * @todo add size error checking
	 */
	protected function render($xml = null) {
		if (is_null($xml)) {
			$xml = parent::render($xml);
		}
		else {
			parent::render($xml);
		}

		if ($this->job instanceof Bluga_Webthumb_Job) {
			$id = $this->job->status->id;
		}
		else {
			$id = $this->job;
		}
		$fetch = $xml->addChild('fetch');
		$fetch->addChild('job',$id);
		$fetch->addChild('size',$this->size);

		return $xml;
	}

	public function asXML() {
		return Bluga_Webthumb_Request::prettyPrint($this->render());
	}
}
