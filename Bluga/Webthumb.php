<?php
/**
 * Wrapper for Webthumb.
 *
 * Wrapper for the WebThumb API by Joshua Eichorn
 * http://bluga.net/webthumb/
 *
 * Currently this requires that allow_url_fopen be set to true to retrieve the
 * image. The next version won't.
 *
 * @package Webthumb
 * @author Cal Evans <cal@zend.com>
 * @author Joshua Eichorn <josh@bluga.net>
 * @copyright 2006 Cal Evans, Joshua Eichorn
 * @license lgpl v2.1
 * @version 1.2
 * @example usage.php
 * @todo Implement calls to jobs we did not initiate.
 * @todo Implement Job class
 *
 * Version History:
 * 1.0 - 09/28/2006 - Cal
 * Initial Release
 *
 * 1.1 - 10/01/2006 - Cal
 * re-implemented fetchToFile to work with the fetch image protocol. This
 * eliminates the need for allow_utl_fopen to be set to true.
 *
 * 1.2 - 04/30/2007 - Josh
 * This wrapper is becoming the offical wrapper, rename to Bluga_Webthumb
 * add support/configure for new webthumb server
 *
 * 1.3 - 05/15/2007 - Josh
 * Add support for Chunked Http Responses
 *
 * 1.4 - 04/??/2008 - Josh
 * Reorganize code to use PEAR2_Http_Request and an autoloader
 *
 * 1.5 - 04/18/2008 0 Josh
 * Change how XML is generated, loose lots of backwards compatability support the entire api
 *
 * 1.6 - 04/25/2008 - Josh
 * Fix all the bugs and make things work with any PEAR2 adapter
 *
 * 1.7 - 02/09/2009 - Josh
 * Lots of bug fixes since the last changelog entry, added failedJobs array for tracking jobs 
 * that don't submit properly
 * 
 * 1.8 - 09/09/2009 - Josh
 * Bug fixes and bring PEAR2 code back in the Bluga namespace
 */

class Bluga_Webthumb {
    /**
     * Jobs that are going to be submitted
     */
    public $jobs = array();

    /**
     * Jobs that were submitted but didn't succeed
     * Normally caused by a bad dns entry
     */
    public $failedJobs = array();

    /**
     * the webthumb api key.
     *
     * @var string 32 char md5 hash
     */
    public $apiKey;

    /**
     * Adapter for PEAR2_HTTP_Request to use, you can use this to override the automatic backend
     * choice that PEAR2_HTTP_Request is using
     */
    public $httpRequestAdapter = null;

    /**
     * Enable extra debug message
     */
    public $debug = false;

    /**
     * The url of the webthumb api endpoint
     *
     * @var string
     */
    protected $webthumbApiEndpoint = 'http://webthumb.bluga.net/api.php';

    /**
     * the base URL to download the images from.
     *
     * @var string
     */
    protected $imageUrl = 'http://webthumb.bluga.net/data/';

    /**
     * The api version to send to the endpoint
     */
    protected $apiVersion = 2;

    /**
     * constructor
     *
     */
    function __construct($apikey = null)
    {
	    if (!is_null($apikey)) {
	    	$this->apiKey = $apikey;
	    }
    } // function __construct()


    /**
     * set the webthumb api key.
     *
     * @param unknown_type $newKey
     */
    public function setApiKey($newKey)
    {
        if (is_string($newKey)) {
            $this->apiKey = $newKey;
        } // if (is_string($newKey))
    } // public function setApiKey($newKey)


    /**
     * add a url to the stack and set the properties for the request.
     *
     * @param String  $url
     * @param string  $size
     * @param Integer $width
     * @param Integer $height
     * @return bool
     */
    public function addUrl($url, $size='small', $width=300, $height=300)
    {
		$url = trim($url);
		if (!preg_match('/^http/i',$url)) {
			$url = 'http://'.$url;
		}
    	if (!$this->validateUrl($url)) {
	    	//throw new Exception('Invalid Url: '.$url);
    	}

	    $job = new Bluga_Webthumb_Job();
    	$job->options->width = $width;
    	$job->options->height = $height;
    	$job->options->url = $url;

    	$job->size = $size;

    	$this->jobs[$job->options->url] = $job;

        return $job;
    } // public function addUrl($url, $size='small',$width=300, $height=300)

    /**
     * Add a job object to the request pool
     */
    public function addJob(Bluga_Webthumb_Job $job) {
	    $this->jobs[$job->options->url] = $job;
    }

    /**
     * submit the queued URLS to webthumb.
     * @throws Exception
     *
     */
    public function submitRequests()
    {

        if (count($this->jobs)<1) {
            throw new Exception('No URLSs to image');
        } // if (count($this->urlsToImage)<1)

        $request = new Bluga_Webthumb_Request_Submit();
        $request->apikey = $this->apiKey;
        $request->jobs = $this->jobs;
        $request->apiversion = $this->apiVersion;
	
        $payload  = $request->asXml();
        $response = $this->_transmitRequest($payload);

        $this->_parseSubmitResponse($response);
        return;
    } // public function submitRequests()


    /**
     * prepares and sends the check status payload for all submitted jobs.
     *
     */
    public function checkJobStatus()
    {
        if (count($this->jobs)<1) {
            throw new Exception('No jobs to check');
        } // if (count($this->urlsToImage)<1)

        $status = new Bluga_Webthumb_Request_Status();
        $status->apikey = $this->apiKey;
        foreach($this->jobs as $job) {
            $status->jobs[] = $job->status->id;
        }
        $xml = $status->asXML();

        if (strlen($xml)>0) {
            $response = $this->_transmitRequest($xml);
            $this->_parseStatusResponse($response);
        } // if (strlen($xml)<1)
        return;
    } // public function checkJobStatus()

    /**
     * handles the actual transmission of the payload.
     *
     * @param unknown_type $request
     * @return String the response from the server.
     */
    protected function _transmitRequest($request)
    {
	    $http = new Bluga_HTTP_Request($this->webthumbApiEndpoint,$this->httpRequestAdapter);
        $http->requestTimeout = 200;
	    $http->verb = "POST";
	    $http->body = $request;

        if ($this->debug) {
            echo "Using Adapter ".$http->getAdapterName()."\n";
            echo "Making request to :".$this->webthumbApiEndpoint."\n";
            echo "Request Body is\n";
            echo "#################\n";
            echo $http->body;
            echo "#################\n\n";
        }

		$response = $http->sendRequest();
        if ($response->code == 302)
        {
            if ($this->debug) {
                echo "We have a redir, lets figure out where the ApiEndpoint has moved too and try again\n";
                echo "New endoing is: ".$response->headers->Location."\n";
            }
            $this->webthumbApiEndpoint = $response->headers->Location;
            return $this->_transmitRequest($request);
        }
        if ($response->code != 200) {
            if ($this->debug)
            {
                echo "We had an error the response object is\n";
                echo "#################\n";
                echo var_dump($response);
                echo "#################\n";
            }
            throw new Exception('None 200 http response code from the API Endpoint ('.$response->code.')'."\n$http->body");
        }
        return $response;

    } // protected function _transmitRequest($request)


    /**
     * Takes the response from submitting urls for imaging and parses it out.
     * Places the responses in the appropriate places in the urlsToImage
     * array.
     *
     * @param string $response the response to process
     *
     */
    protected function _parseSubmitResponse($response)
    {
        $this->_checkContentType($response);

        if ($this->debug) {
            echo "Submit Response Body is\n";
            echo "#################\n";
            echo $response->body;
            echo "#################\n";
        }

        libxml_clear_errors();
        libxml_use_internal_errors(true);
        $xml = new SimpleXMLElement($response->body);

        $err = libxml_get_last_error();
        if ($err) {
            throw new Exception('Invalid XML Payload Returned: '.$err->message);
        }

        // version 2 api returns errors as xml check for an xml error message
        if (isset($xml->error))
        {
            throw new Exception('Error message returned from API endpoint: '.$xml->error.'('.$xml->error['type'].')');
        }

        if (!isset($xml->jobs))
        {
            throw new Exception('Unknown response');
        }

        foreach ($xml->jobs->job as $thisJob) {
            $thisUrl = (String)$thisJob['url'];
            $this->jobs[$thisUrl]->status->id = (String)$thisJob;
            $this->jobs[$thisUrl]->status->start_time = (String)$thisJob['time'];
            $this->jobs[$thisUrl]->status->est_time = (String)$thisJob['estimate'];
            $this->jobs[$thisUrl]->status->cost = (String)$thisJob['cost'];
            $this->jobs[$thisUrl]->status->id = (String)$thisJob;
            $this->jobs[$thisUrl]->status->status = 'Transmited';
        } // foreach ($xml->jobs as $thisJob)

        foreach($this->jobs as $url => $job)
        {
            if ($job->status->id == '')
            {
                $this->failedJobs[$url] = $job;
                unset($this->jobs[$url]);
            }
        }

        return;
    } // protected function _parseSubmitResponse($response)


    /**
     * Takes the response from a status check request and parses it out. Places
     * the relevant information in the urlsToImage array.
     *
     * @param String $response the response to process.
     *
     */
    protected function _parseStatusResponse($response)
    {
        // this throws an exception
        $this->_checkContentType($response);

        if ($this->debug) {
            echo "Status Response Body is\n";
            echo "#################\n";
            echo $response->body;
            echo "#################\n";
        }

        libxml_use_internal_errors(true);
        $xml = new SimpleXMLElement($response->body);
        $err = libxml_get_last_error();
        if ($err) {
            throw new Exception('Invalid XML Payload Returned: '.$err->getMessage());
        }

        // version 2 api returns errors as xml check for an xml error message
        if (isset($xml->error))
        {
            $error = $xml->error;
            if (count($error) > 1)
            {
                $error = $xml->error[1];
            }
            throw new Exception('Error message returned from API endpoint: '.$error.'('.$error['type'].')');
        }

        if (!isset($xml->jobStatus))
        {
            throw new Exception('Unknown response to a job status request');
        }

        foreach ($xml->jobStatus->status as $thisJob) {
            $thisId      = (String)$thisJob['id'];
            $thisUrl     = $this->_findUrlByJobId($thisId);

            $this->jobs[$thisUrl]->status->status = (String)$thisJob;
            $this->jobs[$thisUrl]->status->pickup = (String)$thisJob->status['pickup'];
        } // foreach ($xml->jobs as $thisJob)

        return;
    } // protected function _parseStatusResponse()


    /**
     * finds the Content-Type in the response.
     *   XML  == Good
     *   HTML == Error.
     *
     * @param string $response
     * @return boolean
     * @throws Exception
     */
    protected function _checkContentType($response)
    {
        if (!isset($response->headers['Content-Type'])) {
            throw new Exception('No Content-Type in response.');
        }
        if ($response->headers['Content-Type'] != 'text/xml') {
            throw new Exception('There was an error. Content-Type returned was '.$response->headers['Content-Type']."\n".$response);
        }

        return true;
    } // protected function _checkContentType($response)


    /**
     * Given a jobID, this finds the URL for it. Used in the status parser to
     * match up the information to the proper array.
     *
     * @param string $jobId
     * @return string The URL found.
     */
    protected function _findUrlByJobId($jobId)
    {
        $thisUrl='';
        foreach($this->jobs as $url=>$job) {
            if (isset($job->status->id) && $job->status->id===$jobId) {
                $thisUrl = $url;
                break;
            } // if ($params['job']===$job_id)
        } // foreach($this->urlsToImage as $url=>$params)
        return $thisUrl;
    }


    /**
     * check to see if all images are ready for download.
     *
     * @return boolean
     */
    public function readyToDownload()
    {
        $returnValue = true;
        foreach($this->jobs as $job) {
            $returnValue = ($returnValue AND $job->status->status==='Complete');
        } // foreach($this->urlsToImage as $url=>$params)
        return $returnValue;
    } // public function readyToDownload()


    /**
     * If all images are complete then it will send a request for each image.
     *
     */
    public function fetchAll($dir = null)
    {
        if (count($this->jobs)<1) {
            throw new Exception('No URLSs to image');
        } // if (count($this->urlsToImage)<1)

        if (!$this->readyToDownload()) {
            throw new Exception('No images ready to download.');
        } // if (!$this->readyToDownload())

        foreach ($this->jobs as $job) {
            if($job->status->status == 'Complete') {
                $this->fetchToFile($job,null,null,$dir);
            } // if($this->urlsToImage['status'==='Complete'])
        } // foreach ($this->urlsToImage as $params)

        return;
    } // public function fetchAll()


    /**
     * fetches the given jobid and stores it on the filesystem in the filename
     * specified.
     *
     * @param string $job
     * @param string $filename
     * @param string $size
     *
     */
    public function fetchToFile(Bluga_Webthumb_Job $job, $filename=null, $size=null, $outDir = null )
    {
        if (is_null($filename)) {
            if (isset($job->file)) {
                $filename = $job->file;
            }
            else {
                $filename = $job->status->id;
            }
            $ext = 'jpg';
            if ($job->options->outputType == 'png' || $job->options->outputType == 'png8') {
                $ext = 'png';
            }
            $filename .= '.'.$ext;
        } 

        if (!is_null($outDir)) {
            $filename = "$outDir/$filename";
        }

        if (is_null($size)) {
            $size = 'small';
            if (isset($job->size)) {
                $size = $job->size;
            }
        }

        $request = new Bluga_Webthumb_Request_Fetch($job,$size);
        $request->apikey = $this->apiKey;
        $payload = $request->asXML();

        $image   = $this->_transmitRequest($payload);
        file_put_contents($filename, $image->body);
        return;
    } // public function fetchToFile($job='', $filename='', $size='small' )

	public function validateUrl($url) {
		$bad = false;
                if (@$pieces = parse_url($url)) {
                        if ($pieces == false) {
                                $bad = true;
                        }

                        if (!in_array($pieces['scheme'],array('http','https'))) {
                                $bad = true;
                        }

                        if (gethostbyname($pieces['host']) == $pieces['host']) {
                                $bad = true;
                        }
                }

		return !$bad;
	}


    /**
     * sets the baseDir
     *
     * @param string $newValue
     * @return boolean
     */
    public function setBaseDir($newValue='')
    {
        $returnValue = false;

        if (!empty($newValue)) {
            $newValue = trim($newValue);
            $newValue .= substr($newValue, -1)!='/'?'/':'';
            $this->baseDir = $newValue;
        } // if (!empty($newValue))

        return $returnValue;
    } // public function setBaseDir($newValue='')


    /**
     * retrieves the baseDir
     *
     * @return string
     */
    public function getBaseDir()
    {
        return $this->baseDir;
    } // public function getBaseDir()

}// class Bluga_Webthumb
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
