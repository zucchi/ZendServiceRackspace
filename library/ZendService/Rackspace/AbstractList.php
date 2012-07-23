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
abstract class AbstractList implements \Countable, \Iterator, \ArrayAccess // \JsonSerializable
{
    /**
     * the Rackspace DNS service
     * @var Dns
     */
    protected $service;
    
    /**
     * the key for the containing property
     * @var string
     */
    protected $key = 'container';
    
    /**
     * class to instatiate each item as 
     * @var string
     */
    protected $class;
    
    
    /**
     * Construct
     *
     * @param  RackspaceServers $service
     * @param  array $list
     * @return void
     */
    public function __construct($data = array(),AbstractService $service = null)
    {
        if ($service) {
            $this->setService($service);
        }
        $this->fromArray($data);
    }
    
    /**
     * Set the Rackspace service to use with the domainlist
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
     * Transforms the array to array of Domain
     *
     * @param  array $list
     * @return void
     */
    public function fromArray(array $data)
    {
        if (isset($data[$this->key])) {
            $data = $data[$this->key];
        }    
        
        foreach ($data as $entry) {
            if ($this->class) {
                if (!$entry instanceof $this->class) {
                    $class = $this->class;
                    $entry = new $class($entry, $this->service);
                }
            }
            $this->{$this->key}[] = $entry;
        }
    }
    
    /**
     * return list as an array
     * 
     * @return array
     */
    public function toArray($deep = true)
    {
        $data = array();
        
        foreach ($this->{$this->key} as $entry) {
            if (is_object($entry) && method_exists($entry, 'toArray')) {
                $data[$this->key][] = $entry->toArray($deep);
                
            } else {
                $data[$this->key][] = $entry;
            }
        }
        
        return $data;
    }
    
    /**
     * validate that the entries in the list are valid
     * 
     * @return bool
     */
    public function isValid()
    {
        $valid = true;
        
        foreach ($this->{$this->key} AS $entry) {
            if (!$entry->isValid) {
                $valid = false;
            }
        }
        
        return $valid;
    }
    
    /**
     * Return number of servers
     *
     * Implement Countable::count()
     *
     * @return int
     */
    public function count()
    {
        return count($this->{$this->key});
    }
    /**
     * Return the current element
     *
     * Implement Iterator::current()
     *
     * @return ZendService\Rackspace\Servers\Server
     */
    public function current()
    {
        return $this->{$this->key}[$this->iteratorKey];
    }
    /**
     * Return the key of the current element
     *
     * Implement Iterator::key()
     *
     * @return int
     */
    public function key()
    {
        return $this->iteratorKey;
    }
    /**
     * Move forward to next element
     *
     * Implement Iterator::next()
     *
     * @return void
     */
    public function next()
    {
        $this->iteratorKey += 1;
    }
    /**
     * Rewind the Iterator to the first element
     *
     * Implement Iterator::rewind()
     *
     * @return void
     */
    public function rewind()
    {
        $this->iteratorKey = 0;
    }
    /**
     * Check if there is a current element after calls to rewind() or next()
     *
     * Implement Iterator::valid()
     *
     * @return bool
     */
    public function valid()
    {
        $numItems = $this->count();
        if ($numItems > 0 && $this->iteratorKey < $numItems) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Whether the offset exists
     *
     * Implement ArrayAccess::offsetExists()
     *
     * @param   int     $offset
     * @return  bool
     */
    public function offsetExists($offset)
    {
        return isset($this->{$this->key}[$offset]);
    }
    /**
     * Return value at given offset
     *
     * Implement ArrayAccess::offsetGet()
     *
     * @param   int     $offset
     * @throws  OutOfBoundsException
     * @return  ZendService\Rackspace\Servers\Server
     */
    public function offsetGet($offset)
    {
         return isset($this->{$this->key}[$offset]) ? $this->{$this->key}[$offset] : null;
    }

    /**
     * Implement ArrayAccess::offsetSet()
     *
     * @param   int     $offset
     * @param   string  $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->{$this->key}[] = $value;
        } else {
            $this->{$this->key}[$offset] = $value;
        }
    }

    /**
     * Implement ArrayAccess::offsetUnset()
     *
     * @param   int     $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->{$this->key}[$offset]);
    }
}