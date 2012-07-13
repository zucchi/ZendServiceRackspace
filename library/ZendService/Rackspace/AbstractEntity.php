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
 * @package    ZendService\Rackspace\
 * @subpackage Servers
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendService\Rackspace;

use ZendService\Rackspace;
use ZendService\Rackspace\AbstractService;

/**
 * Abstract List of rackspace entities 
 *
 * @category   Zend
 * @package    ZendService\Rackspace
 * @subpackage Servers
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractEntity // \JsonSerializable
{
    /**
     * the Rackspace DNS service
     * @var Dns
     */
    protected $service;
    
    /**
     * set service and construct from array
     * 
     * @param array $data
     */
    public function __construct(AbstractService $service, array $data = array())
    {
        $this->setService($service);
        $this->fromArray($data);
    }
    
    /**
     * magic method for getting protected variables
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->{$key};
    }
    
    /**
     * magic method for setting protected variables
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $method = 'set' . ucfirst($key);
        if (method_exists($this, $method)) {
            $this->{$method}($value);
            
        } else if (property_exists($this, $key)) {
            $this->{$key} = $value;
        }
    }
    
    /**
     * Set the Rackspace service to use with the domain
     * 
     * @param Dns $service
     * @throws Exception\RuntimeException
     * @return \ZendService\Rackspace\Dns\DomainList
     */
    public function setService(Dns $service)
    {
        $this->service = $service;
        return $this;
    }
    
    /**
     * get the currently set service
     * @return Dns
     */
    public function getService()
    {
        return $this->service;
    }
    
    /**
     * populate entity from array
     * 
     * @param array $data
     */
	public function fromArray(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->{$method}($value);
                
            } else if (property_exists($this, $key)) {
                $this->{$key} = $value;
            } 
        }
    }
    
    /**
     * return array of data
     * 
     * @return array
     */
    public function toArray($deep = true)
    {
        return get_object_vars($this);
    }
    
    /**
     * return array of data for json serialization
     * 
     * in prep for JsonSerializable interface
     * 
     * @return Ambigous <multitype:, multitype:NULL >
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
    
    /**
     * Validate the domain
     * 
     * @return bool
     */
    public function isValid()
    {
        return true;
    }
    
}