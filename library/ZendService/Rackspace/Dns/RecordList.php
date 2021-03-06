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
 * @subpackage Dns
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendService\Rackspace\Dns;

use ZendService\Rackspace\Dns;
use ZendService\Rackspace\Dns\Record;
use ZendService\Rackspace\AbstractService;
use ZendService\Rackspace\AbstractList;

/**
 * List of records 
 *
 * @category   ZendService
 * @package    ZendService\Rackspace
 * @subpackage Dns
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class RecordList extends AbstractList
{
    /**
     * the Rackspace DNS service
     * @var Dns
     */
    protected $key = 'records';
    
    /**
     * the class to use for each entry in the list
     * @var string
     */
    protected $class = "\ZendService\Rackspace\Dns\Record";
    
    /**
     * the collective records
     * @var array
     */
    public $records = array();
    
}