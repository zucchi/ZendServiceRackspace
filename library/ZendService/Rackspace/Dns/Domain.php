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
 * @package    ZendService\Rackspace
 * @subpackage Dns
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendService\Rackspace\Dns;

use ZendService\Rackspace\AbstractEntity;
use ZendService\Rackspace\Dns\DomainList;
use ZendService\Rackspace\Dns\RecordList;
use ZendService\Rackspace\Dns;

/**
 * Domain Entity for Rackspace Could DNS service
 * 
 * @todo: implement validation
 * @category   ZendService
 * @package    ZendService\Rackspace
 * @subpackage Dns
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Domain extends AbstractEntity
{
    /**
     * name of domain
     * 
     * @var string
     */
    public $name;
    
    /**
     * id of domain
     * 
     * @var integer
     */
    public $id;
    
    /**
     * optional comment for domain
     * 
     * @var string
     */
    public $comment;
    
    /**
     * email address for domain owner
     * @var string
     */
    public $emailAddress;
    
    /**
     * time to live for domain
     * @var integer
     */
    public $ttl;

    /**
     * records associated to domain
     * @var RecordList
     */
    public $recordsList;
    
    /**
     * subdomains for domain
     * @var DomainList
     */
    public $subdomains;
    
    /**
     * list of nameservers
     * @var array
     */
    protected $nameservers;
    
    
    /**
     * date domain last updated
     * @var \DateTime
     */
    protected $updated;
    
    /**
     * date domain created
     * @var \DateTime
     */
    protected $created; 
    
    /**
     * Account ID
     * @var string
     */
    protected $accountId;
    
    /**
     * construct from array
     * 
     * @param array $data
     */
    public function __construct(Dns $service, array $data = array())
    {
        $this->setService($service);
        
        $this->recordsList = new RecordList($service);
        $this->subdomains = new DomainList($service);
        
        $this->fromArray($data);
    }
    
    /**
     * @return the $records
     */
    public function getRecordsList()
    {
        return $this->recordsList;
    }

	/**
     * @param array|RecordList $records
     * @return Domain
     */
    public function setRecordsList($records)
    {
        if (is_array($records)) {
            $list = new RecordList($this->service, $records);
        }
        
        $this->recordsList = $list;
    }

	/**
     * @return the $subdomains
     */
    public function getSubdomains()
    {
        return $this->subdomains;
    }

	/**
     * @param array|DomainList $subdomains
     * @return Domain
     */
    public function setSubdomains($subdomains)
    {
        if (is_array($subdomains)) {
            $subdomains = new DomainList($this->service, $subdomains);
        }
        
        $this->subdomains = $subdomains;
        return $this;
    }

	/**
     * @return the $nameservers
     */
    public function getNameservers()
    {
        return $this->nameservers;
    }

	/**
     * @return the $updated
     */
    public function getUpdated()
    {
        return $this->updated;
    }
    
    /**
     * set the updated date
     * @param unknown_type $date
     * @return \ZendService\Rackspace\Dns\Domain
     */
    public function setUpdated($date)
    {
        if (is_string($date)) {
            //$date = new \DateTime($date);
        }
        $this->updated = $date;
        return $this;
    }

	/**
     * @return the $created
     */
    public function getCreated()
    {
        return $this->created;
    }
    
    /**
     * set the created date
     * @param string $date
     * @return \ZendService\Rackspace\Dns\Domain
     */
    public function setCreated($date)
    {
        if (is_string($date)) {
            //$date = new \DateTime($date);
        }
        $this->created = $date;
        return $this;
    }

	/**
     * @return the $accountId
     */
    public function getAccountId ()
    {
        return $this->accountId;
    }

    /**
     * return array of data
     * 
     * @return array
     */
    public function toArray($deep = true)
    {
        $data = array(
            'name' => $this->name,
            'comment' => $this->comment,
            'emailAddress' => $this->emailAddress,
            'ttl' => $this->ttl,
        );
        
        if ($this->id) {
            $data['id'] = $this->id;
        }
        
        if ($deep && count($this->recordsList)) {
            $data['recordsList'] = $this->recordsList->toArray();
        }
        
        if ($deep && count($this->subdomains)) {
            $data['subdomains'] = $this->subdomains->toArray();
        }
        
        return $data;
    }
}