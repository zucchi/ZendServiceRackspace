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
use Zend\Http\Client\Adapter\Test as HttpTest;

/**
 * @category   Zend
 * @package    ZendService\Rackspace\Files
 * @subpackage UnitTests
 * @group      Zend\Service
 * @group      ZendService\Rackspace
 * @group      ZendService\Rackspace\Files
 */
class IdentityOfflineTest extends \PHPUnit_Framework_TestCase
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
        $this->service = new Identity('username', 'api-key', Identity::US_API_ENDPOINT);
        $this->service->setToken('dummytokendummytokendummytoken');
        $tokenExpiry = new \DateTime();
        $tokenExpiry->sub(new \DateInterval('PT1H'));
        $this->service->setTokenExpiry($tokenExpiry);
        $this->service->setTenantId('00000000');
        
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
    
    
    public function testCanGetUsername()
    {
        $this->assertEquals('username', $this->service->getUsername());
    }
    
    public function testCanGetApiKey()
    {
        $this->assertEquals('api-key', $this->service->getApiKey());
    }
    
    public function testCanGetApiEndpoint()
    {
        $this->assertEquals(Identity::US_API_ENDPOINT, $this->service->getApiEndpoint());
    }
    
    public function testCanGetToken()
    {
        $this->assertEquals('dummytokendummytokendummytoken', $this->service->getToken());
    }
    
    public function testCanGetTokenExpiry()
    {
        $this->assertInstanceOf('DateTime', $this->service->getTokenExpiry());
    }
    
    
    public function testCanGetServiceCatalog()
    {
        $this->assertInternalType('array', $this->service->getServiceCatalog());
    }
    
    public function testCanSetServiceCatalog()
    {
        
    }
    
    public function testCanCheckHasAccessToService()
    {
        
    }
    
    public function testCanCheckHasNoAccessToService()
    {
        
    }
    
    public function testCanGetService()
    {
        
    }
    
    public function testCannotGetService()
    {
        
    }
    
    public function testCanGetUser()
    {
        $this->assertInternalType('array', $this->service->getUser());
    }
    
    public function testCanAuthenticate()
    {
        
    }
    
    public function testCanClearIdentity()
    {
        
    }
    
    public function testCanTestIdentityIsValid()
    {
        
    }
    
    public function testCanTestIdentityIsNotValid()
    {
        
    }
    
}