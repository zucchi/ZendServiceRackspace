<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendServiceTest\Rackspace;

use ZendService\Rackspace\Identity;
use ZendService\Rackspace\Dns;
use Zend\Http\Client\Adapter\Test as HttpTest;

/**
 * @category   Zend
 * @package    ZendService\Rackspace\Files
 * @subpackage UnitTests
 * @group      Zend\Service
 * @group      ZendService\Rackspace
 * @group      ZendService\Rackspace\Files
 */
class DnsOfflineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reference to RackspaceFiles
     *
     * @var ZendService\Rackspace\Identity
     */
    protected $service;

    /**
     * HTTP client adapter for testing
     *
     * @var Zend\Http\Client\Adapter\Test
     */
    protected $httpClientAdapterTest;

    /**
     * Path to test data files
     *
     * @var string
     */
    protected $responsePath;
    
    /**
     * Sets up this test case
     *
     * @return void
     */
    public function setUp()
    {
        $identity = new Identity();
        $identity->setUsername('username');
        $identity->setApiKey('api-key');
        $identity->setToken('dummytokendummytokendummytoken');
        $tokenExpiry = new \DateTime();
        $tokenExpiry->sub(new \DateInterval('PT1H'));
        $identity->setTokenExpiry($tokenExpiry);
        $identity->setTenantId('00000000');
        
        // @todo: mock identity
        $this->service = new Dns($identity);
        $this->filesPath   = __DIR__ . '/_files';
        $this->httpClientAdapterTest = new HttpTest();
    }
    
    /**
     * Utility method for returning a string HTTP response, which is loaded from a file
     *
     * @param  string $name
     * @return string
     */
    protected function _loadResponse($name)
    {
        return file_get_contents("$this->filesPath/$name.response");
    }
    
    public function testCanConstruct()
    {
        
    } 
    
    public function testCannotConstruct()
    {
        
    }
    
    public function testCanListAllLimits()
    {
        
    }
    
    public function testCanlistLimitTypes()
    {
        
    }
    
    public function testCanListSpecificLimit()
    {
        
    }
    
    public function testCanListDomains()
    {
        
    }
    
    public function testCanListDomainDetails()
    {
        
    }
    
    public function testCanListDomainChanges()
    {
        
    }
    
    public function testCanExportDomain()
    {
        
    }
    
    public function testCanCreateDomnains()
    {
        
    }
    
    public function testCanImportDomain()
    {
        
    }
    
    public function testCanModifyDomains()
    {
        
    }
    
    public function testCanRemoveDomains()
    {
        
    }
    
    public function canListSubdomains()
    {
        
    }
    
    public function testCanListReords()
    {
        
    } 
    
    public function testCanListRecordDetails()
    {
        
    }
    
    public function testCanAddRecords()
    {
        
    }
    
    public function testCanModifyRecords()
    {
        
    }
    
    public function testCanRemoveRecords()
    {
        
    }
    
    public function testCanListPtrRecords()
    {
        
    }
    
    public function testCanListPtrRecordDetails()
    {
        
    }
    
    public function testCanAddPtrRecords()
    {
        
    }
    
    public function testCanModiftPtrRecords()
    {
        
    } 
    
    public function testCanRemovePtrRecords()
    {
        
    }
    
    public function testCanAccessPtrRecords()
    {
        
    }
}