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



use Zend\Http\Client as HttpClient;
use Zend\Validator\Ip as IpValidator;
use ZendService\Rackspace\Dns\DomainList;
use ZendService\Rackspace\Dns\RecordList;
use ZendService\Rackspace\Dns\Domain;
use ZendService\Rackspace\Dns\Record;
/**
 * DNS API wrapper
 * 
 * @todo: implement better handling for async requests
 * @category   ZendService
 * @package    ZendService\Rackspace
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dns extends AbstractService
{
    /**
     * service key for this API
     * 
     * @var string
     */
    protected $serviceKey = 'cloudDNS';
    
    /**
     * List all applicable limits.
     * 
     * @return array
     */
    public function listAllLimits()
    {
        $uri = '/limits';
        $result = $this->httpCall($uri, 'GET');
        
        $data = $this->decodeBody($result);
                
        return $data;
    }
    
    /**
     * List the types of limits
     * 
     * @todo: fix as not found
     * @return array
     */
    public function listLimitTypes()
    {
        $uri = '/limits/types';
        $result = $this->httpCall($uri, 'GET');
        
        $data = $this->decodeBody($result);
        
        return $data;
    }
    
    /**
     * List assigned limits of a specific type
     * 
     * @todo: implement
     * @param string $type
     * @return array
     */
    public function listSpecificLimit($type)
    {
        $uri = '/limits/' . $type;
        $result = $this->httpCall($uri, 'GET');
        
        $data = $this->decodeBody($result);
        
        return $data;
    }
    
    /**
     * List domains all available domains with optional filtering
     * 
     * @param array $name
     * @return array
     */
    public function listDomains($filters = array())
    {
        
        $uri = '/domains';
        $result = $this->httpCall($uri, 'GET', $filters);
        
        $data = $this->decodeBody($result, true);
        
        return $data;
    }
    
    /**
     * list details for a specific domain
     * 
     * @param integer $domainId
     * @param bool $showRecords
     * @param bool $showSubdomains
     * @return array
     */
    public function listDomainDetails($domainId, $showRecords = true, $showSubdomains = true)
    {
        $uri = '/domains/' . $domainId;
        $result = $this->httpCall($uri, 'GET', array(
            'showRecords' => $showRecords ? 'true' : 'false',
            'showSubdomains' => $showSubdomains ? 'true' : 'false',
        ));
        
        $data = $this->decodeBody($result, true);
        
        $domain = new Dns\Domain($this, $data);
        
        return $domain;
    }
    
    /**
     * list all changes to domain since teh specified Datetime
     * 
     * @param integer $domainId
     * @param \DateTime $datetime
     * @return array
     */
    public function listDomainChanges($domainId, \DateTime $datetime)
    {
        $uri = '/domains/' . $domainId . '/changes';
        $result = $this->httpCall($uri, 'GET', array(
            'since' => $datetime->format(\DateTime::ISO8601)
        ));
        
        $data = $this->decodeBody($result, true);
        
        return $data;
    }
    
    /**
     * Export the specified domain
     * 
     * is async request so will return request details
     * 
     * @param integer $domainId
     * @return array
     */
    public function exportDomain($domainId)
    {
        $uri = '/domains/' . $domainId . '/export';
        $result = $this->httpCall($uri, 'GET');
        
        $data = $this->decodeBody($result, true);
        
        return $data;
    }
    
    /**
     * Create one or more new domains
     * 
     * is async request so will return request details
     * 
     * @param array $data
     * @return array
     */
    public function createDomains(DomainList $data)
    {
        $uri = '/domains';
        $result = $this->httpCall($uri, 'POST', array(), $data->toArray());
        
        $data = $this->decodeBody($result, true);
        
        return $data;
    }
    
    /**
     * Import BIND9 data as new domain
     * 
     * is async request so will return request details
     * 
     * @param array $domain
     * @return array
     */
    public function importDomain($data)
    {
        $uri = '/domains/import';
        $result = $this->httpCall($uri, 'POST', array(), $data);
        
        $data = $this->decodeBody($result, true);
        
        return $data;
    }
    
    /**
     * modify one or more domains, works on principle of only passing 
     * the values that have been altered
     * 
     * cannot change id or domain name
     * 
     * is async request so will return request details
     * 
     * @param DomainList $domains
     * @return
     */
    public function modifyDomains(DomainList $data)
    {
        $uri = '/domains';
        
        $data = $data->toArray(false);
        
        // lets iterate over and remove domain names as these cannot be altered
        foreach ($data['domains'] AS $k => $v) {
            unset($data['domains'][$k]['name']);
        }
        
        $result = $this->httpCall($uri, 'PUT', array(), $data);
        
        $data = $this->decodeBody($result, true);
        
        return $data;
    }
    
    
    /**
     * remove one or more domains
     * 
     * due to how HTTP client works we have to manually build the query string
     * 
     * is async request so will return request details
     * 
     * @param array $domainIds
     * @param bool $removeSubdomains
     * @return array
     */
    public function removeDomains(array $domainIds, $removeSubdomains = false)
    {
        $uri = '/domains?id=' . (implode('&id=', $domainIds));
        $uri .= '&removeSubdomains=' . ($removeSubdomains ? 'true' : 'false');
        
        $result = $this->httpCall($uri, 'DELETE');
        
        $data = $this->decodeBody($result, true);
        
        return $data;
    }
    
    /**
     * list subdomains of specifed domain
     * 
     * @param int $domainId
     * @return DomainList
     */
    public function listSubdomains($domainId)
    {
        $uri = '/domains/' . $domainId . '/subdomains';
        
        $result = $this->httpCall($uri, 'GET');
        
        $data = $this->decodeBody($result, true);
        
        $domains = new DomainList($this, $data);
        
        return $domains;
    }
    
    /**
     * List all records configured for the specified domains
     * N.B. SOA cannot be modified
     * 
     * @param integer $domainId
     * @param arrays $filters
     * @return RecordList
     */
    public function listRecords($domainId, array $filters = array())
    {
        $uri = '/domains/' . $domainId . '/records';
        
        $result = $this->httpCall($uri, 'GET', $filters);
        
        $data = $this->decodeBody($result, true);
        
        $records = new RecordList($this, $data);
        
        return $records;
    }
    
    /**
     * list details for a specific record
     * 
     * @param integer $domainId
     * @param integer $recordId
     * @return Record
     */
    public function listRecordDetails($domainId, $recordId)
    {
        $uri = '/domains/' . $domainId . '/records/' . $recordId;
        
        $result = $this->httpCall($uri, 'GET');
        
        $data = $this->decodeBody($result, true);

        $record = new Record($this, $data);
        
        return $record;
    }
    
    /**
     * add one or more records to a domain
     * 
     * is async request so will return request details
     * 
     * @param integer $records
     * @param RecordList $records
     * @return array
     */
    public function addRecords($domainId, RecordList $records)
    {
        $uri = '/domains/' . $domainId . '/records';
        
        $result = $this->httpCall($uri, 'POST', array(), $records->toArray());
        
        $data = $this->decodeBody($result, true);

        return $data;
    }
    
    /**
     * modify the configuration of one or more records for a specific domain
     * 
     * is async request so will return request details
     * 
     * @param RecordList $records
     * @return array
     */
    public function modifyRecords($domainId, RecordList $records)
    {
        $uri = '/domains/' . $domainId . '/records';
        
        $result = $this->httpCall($uri, 'PUT', array(), $records->toArray());
        
        $data = $this->decodeBody($result, true);

        return $data;
    }
    
    /**
     * remove one or more records for a specified domain
     * 
     * @param array $recordIds
     * @return array
     */
    public function removeRecords($domainId, array $recordIds)
    {
        $uri = '/domains/'.$domainId.'/records?id=' . (implode('&id=', $recordIds));
        
        $result = $this->httpCall($uri, 'DELETE');
        
        $data = $this->decodeBody($result, true);
        
        return $data;
    }
    
    /**
     * List all PTR records configured for a Rackspace Cloud device
     * 
     * only supports the services cloudServersOpenStack & cloudLoadBalancers
     * 
     * @param string $serviceName
     * @param string $deviceResourceUrl
     * @return RecordList
     */
    public function listPtrRecords($serviceName, $deviceResourceUrl)
    {
        $this->canAccessPtrRecords($serviceName);
        
        $uri = '/rdns/'.$serviceName;
        
        $result = $this->httpCall($uri, 'GET', array(
            'href' => $deviceResourceUrl
        ));
        
        $data = $this->decodeBody($result, true);
        
        $list = new RecordList($this, $data);
        
        return $list;
        
    }
    
    /**
     * List details for a specific PTR record associated with a 
     * Rackspace Cloud device.
     * 
     * only supports the services cloudServersOpenStack & cloudLoadBalancers
     * 
     * @todo: implement
     * @param string $serviceName
     * @param string $recordId
     * @param string $deviceResourceUrl
     * @return Record
     */
    public function listPtrRecordDetails($serviceName, $deviceResourceUrl, $recordId)
    {
        $this->canAccessPtrRecords($serviceName);
        
        $uri = '/rdns/'.$serviceName . '/' . $recordId;
        
        $result = $this->httpCall($uri, 'GET', array(
            'href' => $deviceResourceUrl
        ));
        
        $data = $this->decodeBody($result, true);
        
        $list = new Record($this, $data);
        
        return $list;
        
    }
    
    /**
     * Add new PTR record(s) for a Rackspace Cloud device.
     * 
     * only supports the services cloudServersOpenStack & cloudLoadBalancers
     * 
     * @param string $serviceName
     * @param string $deviceResourceUrl
     * @param RecordList $records
     * @return array
     */
    public function addPtrRecords($serviceName, $deviceResourceUrl, RecordList $records)
    {
        $this->canAccessPtrRecords($serviceName);
        
        $uri = '/rdns';
        
        $result = $this->httpCall($uri, 'POST', array(), array (
            'recordsList' => $records,
            'link' => array(
                'href' => $deviceResourceUrl,
                'rel' => $serviceName,
            )
        ));
        
        $data = $this->decodeBody($result, true);
        
        return $data;
    }
    
    /**
     * Modify one or more PTR records associated with a Rackspace Cloud device
     * 
     * only supports the services cloudServersOpenStack & cloudLoadBalancers
     * 
     * @param string $serviceName
     * @param string $deviceResourceUrl 
     * @param RecordList $records
     * @return array
     */
    public function modifyPtrRecords($serviceName, $deviceResourceUrl, RecordList $records)
    {
        $this->canAccessPtrRecords($serviceName);
        
        $uri = '/rdns';
        
        $result = $this->httpCall($uri, 'PUT', array(), array (
            'recordsList' => $records,
            'link' => array(
                'href' => $deviceResourceUrl,
                'rel' => $serviceName,
            )
        ));
        
        $data = $this->decodeBody($result, true);
        
        return $data;
    }
    
    /**
     * Remove one or all PTR records associated with a Rackspace Cloud device. 
     * Use the optional ip query parameter to specify a specific record to delete. 
     * Omitting this parameter removes all PTR records associated with the 
     * specified device.
     * 
     * only supports the services cloudServersOpenStack & cloudLoadBalancers
     * 
     * @param string $serviceName
     * @param string $deviceResourceUrl
     * @param string $optionalIpAddress
     * @return array
     */
    public function removePtrRecords($serviceName, $deviceResourceUrl, $optionalIpAddress = null)
    {
        $this->canAccessPtrRecords($serviceName);
        
        $uri = '/rdns/' . $serviceName;
        
        $params = array(
            'href' => $deviceResourceUrl,
        );
        if ($optionalIpAddress) {
            $params['ip'] = $optionalIpAddress;
        }
        
        $result = $this->httpCall($uri, 'DELETE', $params);
        
        $data = $this->decodeBody($result, true);
        
        return $data;
    }
    
    /**
     * test if access to PTR records possible
     * 
     * currently only ptr access available for the cloud services 
     * cloudServersOpenStack & cloudLoadBalancers 
     * 
     * @param string $serviceName
     * @throws Exception\RuntimeException
     * @return bool
     */
    protected function canAccessPtrRecords($serviceName)
    {
        if (in_array($serviceName, array('cloudServersOpenStack', 'cloudLoadBalancers'))) {
            if ($this->getIdentity()->hasService($serviceName)) {
                return true;
            }
        }
        
        throw new Exception\RuntimeException(
            $this->messageTemplates[self::SERVICE_ACCESS_DENIED],
            500
        );
    }
}