<?php
class Bluga_HTTP_Request_Adapter_Curl extends Bluga_HTTP_Request_Adapter {

    protected $curl = false;
    protected $fp = false;

    public function sendRequest() 
    {
        $this->_setupRequest();

        return $this->_sendRequest();
    }

    public function requestToFile($file) {
        $this->_setupRequest();

        $this->fp = fopen($file,'w');
        curl_setopt($this->curl,CURLOPT_FILE,$this->fp);

        return $this->_sendRequest();
    }

    /**
     * @todo error checking
     * @implement put
     */
    protected function _setupRequest() 
    {
        $this->curl = curl_init($this->uri->url);
        // check error here

        // request timeout
        //curl_setopt($this->curl,CURLOPT_CONNECTTIMEOUT,$this->requestTimeout);
        curl_setopt($this->curl,CURLOPT_TIMEOUT,$this->requestTimeout);

        // progress callback
        // magical breakage the array becomes an int(0) somewhere along the line, we will ignore
        // this for now
        /*
		if (count($this->_listeners) > 0) {
            throw new Exception("Progress callback not implmented for curl yet");
        }
         */

        // follow redirects ???
        // curl_setopt($this->curl,CURLOPT_FOLLOWLOCATION,???);

        // set http version (currently we are only letting you force 1.0 otherwise we let curl auto determine
        switch(strtolower($this->httpVersion))
        {
            case 'http/1.0':
            curl_setopt($this->curl,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_0);
            break;
            case 'http/1.1':
            default:
            curl_setopt($this->curl,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_NONE);
            break;
        }
        
        // http verb
        if (strtoupper($this->verb) == 'PUT') {
            throw new Exception("HTTP put not implmented for Curl yet");
        }
        curl_setopt($this->curl,CURLOPT_CUSTOMREQUEST,$this->verb);

        // headers
        curl_setopt($this->curl,CURLOPT_HTTPHEADER,$this->headers);

        // general stuff
        curl_setopt($this->curl,CURLOPT_BINARYTRANSFER,true);
        curl_setopt($this->curl,CURLOPT_RETURNTRANSFER,true);

        // setup a callback to handle header info
        curl_setopt($this->curl,CURLOPT_HEADERFUNCTION,array($this,'_headerCallback'));

        // post data
        if (!empty($this->body)) {
            curl_setopt($this->curl,CURLOPT_POSTFIELDS,$this->body);
        }
	}

    protected function _sendRequest() {
        $body = curl_exec($this->curl);

        if ($this->fp !== false) {
            fclose($this->fp);
        }

        $details = $this->uri->toArray();


        $details['code'] = curl_getinfo($this->curl,CURLINFO_HTTP_CODE);
        //$details['httpVersion'] = $response->getHttpVersion();

        $headers = new Bluga_HTTP_Request_Headers($this->headers);
        $cookies = array();

        return new Bluga_HTTP_Request_Response($details, $body, $headers, $cookies);
    }

    protected function _headerCallback($curl,$data) {
        $this->processHeader(trim($data));
        return strlen($data);
    }
}
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
