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

/**
 * Monitoring API wrapper
 * 
 * @todo: implement better handling for async requests
 * @category   ZendService
 * @package    ZendService\Rackspace
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ServersFirstGen extends AbstractService
{
    /**
     * service key for this API
     * 
     * @var string
     */
    protected $serviceKey = 'cloudServers';
    
    
}