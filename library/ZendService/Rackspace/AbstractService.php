<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    ZendService
 * @subpackage Rackspace
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendService\Rackspace;

use ZendService\Rackspace\Exception;
use Zend\Http\Client as HttpClient;

abstract class AbstractService
{
    const API_FORMAT             = 'json';
    const USER_AGENT             = 'ZendService\Rackspace';
    const HEADER_TOKEN           = 'X-Auth-Token';
    
    
    // messages
    const RESPONSE_FAILED_DECODE = 'responseFailedDecode';
    const SERVICE_ACCESS_DENIED = 'serviceAccessDenied';
    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::RESPONSE_FAILED_DECODE => "Unable to decode json",
        self::SERVICE_ACCESS_DENIED => 'You do not have permission to access this service',
    
    
    );
    
    
    
    /**
     * the Rackspace Identity to use 
     * @var Identity
     */
    protected $identity;
    
    
    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * Constructor
     *
     * @param Identity $identity
     * @throws Exception\InvalidArgumentException
     */
    public function __construct(Identity $identity)
    {
        $this->setIdentity($identity);
    }
    /**
     * Get the set Identity
     *
     * @return Identity
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * set the identity to use
     * 
     * @param Identity $identity
     * @return $this
     */
    public function setIdentity(Identity $identity)
    {
        if (!$identity->isValid()) {
            $identity->authenticate();
        }
        
        $this->identity = $identity;
        return $this;
    }
    
    /**
     * get the HttpClient instance
     *
     * @return HttpClient
     */
    public function getHttpClient()
    {
        if (empty($this->httpClient)) {
            $this->httpClient = new HttpClient();
        }
        return $this->httpClient;
    }
    /**
     * Return true is the last call was successful
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return (empty($this->errorMsg));
    }
    /**
     * HTTP call
     *
     * @param string $url
     * @param string $method
     * @param array $params
     * @param array $body
     * @param array $headers
     * @return Zend\Http\Response
     */
    protected function httpCall($url,$method,array $params = array(), array $body=array(),array $headers=array())
    {
        $client = $this->getHttpClient();
        $client->resetParameters();
        
        $client->setMethod($method);
        
        $client->setParameterGet($params);
        
        if (!empty($body)) {
            $client->setRawBody(json_encode($body));
        }
        
        $headers['Content-Type'] = 'application/json';
        if ($identity = $this->getIdentity()) {
            if (!$identity->isValid()) {
                $identity->authenticate();
            }
            $headers[self::HEADER_TOKEN] = $this->getIdentity()->getToken();
            
            // check for service url
            if (property_exists($this, 'serviceKey')) {
                $service = $identity->getService($this->serviceKey);
                $url = $service->endpoints[0]->publicURL . $url;
            }
        }
        $client->setHeaders($headers);
        $client->setUri($url);
        
        $response =  $client->send();
        
        if (!$response->isSuccess()) {
            $messages = false;
            
            // decode body for error messages
            $error = $this->decodeBody($response);
            $code = (isset($error->code)) ? $error->code : false;
\Zend\Debug::dump($client->getRequest()->getContent());
            \Zend\Debug::dump($response->getBody());exit();

            if (isset($error->validationErrors->messages)) {
                $messages = implode(PHP_EOL, $error->validationErrors->messages);
            }
            
            
            throw new Exception\RuntimeException(
                $messages ?: $response->getReasonPhrase(), 
                $code ?: $response->getStatusCode()
            );
        }
        return $response;
    }
    
    /**
     * decode response body
     * 
     * @param \Zend\Http\Response $response
     * @throws Exception\RuntimeException
     * @return array
     */
    public function decodeBody(\Zend\Http\Response $response, $assoc = false) 
    {
        $json = json_decode($response->getBody(), $assoc);
        
        $error = false;
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $error = false;
                break;
            case JSON_ERROR_DEPTH:
                $error = 'json_decode: Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $error = 'json_decode: Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $error = 'json_decode: Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                $error = 'json_decode: Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                $error = 'json_decode: Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                $error = 'json_decode: Unknown error';
                break;
        }
        
        if ($error) {
            throw new Exception\RuntimeException($error);
        }
        
        return $json;
    }
    
    /**
     * check async job status
     * @param string $jobId
     * @param bool $showDetails
     * @return array()
     */
    public function jobStatus($jobId = null, $showDetails = true)
    {
        $uri = '/status';
        if ($jobId) {
            $uri .= '/' . $jobId;
        }
        
        $result = $this->httpCall($uri, 'GET', array(
            'showDetails' => ($showDetails ? 'true' : 'false'),
        ));
        
        $data = $this->decodeBody($result);
        
        return $data;
    }
}
