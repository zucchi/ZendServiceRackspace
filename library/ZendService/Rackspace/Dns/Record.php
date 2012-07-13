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
use ZendService\Rackspace\Dns;

/**
 * Record Entity for Rackspace Could DNS service
 * 
 * @todo: implement validation
 * @category   ZendService
 * @package    ZendService\Rackspace
 * @subpackage Dns
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Record  extends AbstractEntity
{
    // constants of supported record types
    const TYPE_A     = 'A';
    const TYPE_AAAA  = 'AAAA';
    const TYPE_CNAME = 'CNAME';
    const TYPE_MX    = 'MX';
    const TYPE_NS    = 'NS';
    const TYPE_PTR   = 'PTR';
    const TYPE_SRV   = 'SRV';
    const TYPE_TXT   = 'TXT';
    const TYPE_DKIM  = 'TXT';
    const TYPE_SPF   = 'TXT';
    
    /**
     * fields required for context based on type
     * 
     * follows format of array($fieldname => $required) where $required is a boolean
     * 
     * @var array
     */
    protected $contextFields = array(
        self::TYPE_A => array('name' => true, 'type' => true, 'data' => true, 'ttl' => false, 'comment' => false),
        self::TYPE_AAAA => array('name' => true, 'type' => true, 'data' => true, 'ttl' => false, 'comment' => false),
        self::TYPE_CNAME => array('name' => true, 'type' => true, 'data' => true, 'ttl' => false, 'comment' => false),
        self::TYPE_MX => array('priority' => true, 'name' => true, 'type' => true, 'data' => true, 'ttl' => false, 'comment' => false),
        self::TYPE_NS => array('name' => true, 'type' => true, 'data' => true, 'ttl' => false, 'comment' => false),
        self::TYPE_PTR => array('name' => true, 'type' => true, 'data' => true, 'ttl' => false, 'comment' => false),
        self::TYPE_SRV => array('priority' => true, 'name' => true, 'type' => true, 'data' => true, 'ttl' => false, 'comment' => false),
        self::TYPE_TXT => array('name' => true, 'type' => true, 'data' => true, 'ttl' => false, 'comment' => false),
    );
    
    /**
     * Specifies the name for the domain or subdomain. Must be a valid domain name.
     * @var string
     */
    public $name;
    
    /**
     * Rackspace ID for record
     * @var string
     */
    public $id;
    
    /**
     * type of record
     * @var string`
     */
    public $type;
    
    /**
     * The data field for PTR, A, and AAAA records must be a valid IPv4 or IPv6 IP address
     * @var string
     */
    public $data;
    
    /**
     * If specified, must be greater than 300. Defaults to the domain TTL 
     * if available, or 3600 if no TTL is specified.
     * @var int
     */
    public $ttl;
    
    /**
     * Required for MX and SRV records, but forbidden for other record types. 
     * If specified, must be between 0 and 65535 (inclusive).
     * @var unknown_type
     */
    public $priority;
    
    /**
     * If included, its length must be less than or equal to 160 characters.
     * @var string
     */
    public $comment;
    
    /**
     * last updated date of record
     * @var DateTime
     */
    protected $updated;
    
    /**
     * date record created
     * @var DateTime
     */
    protected $created;
    
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
            $date = new \DateTime($date);
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
            $date = new \DateTime($date);
        }
        $this->created = $date;
        return $this;
    }   
    
    /**
     * return array of data
     * 
     * @return array
     */
    public function toArray($deep = true)
    {
        $data = array();
        if ($this->type) {
            $fields = $this->contextFields[$this->type];
            
            foreach ($fields as $field => $required) {
                if ($required) {
                    $data[$field] = $this->{$field};
                } else if ($this->{$field}) {
                    $data[$field] = $this->{$field};
                }
            }
        }
        
        if ($this->id) {
            $data['id'] = $this->id;
        }
        
        return $data;
    }
}