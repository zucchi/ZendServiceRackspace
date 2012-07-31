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

use ZendTest\Form\Element\DateTest;

use Zend\Http\Client as HttpClient;
use ZendService\Rackspace\Exception;
use ZendService\Rackspace\Identity\User;

class Identity extends AbstractService
{
    const VERSION = 'v2.0';
    const US_API_ENDPOINT = 'https://identity.api.rackspacecloud.com/';
    const UK_API_ENDPOINT = 'https://lon.identity.api.rackspacecloud.com/';
    
    const REQUIRE_API_DETAILS = 'identityRequiresApiDetails';
    const SERVICE_NOT_AVAILABLE = 'identityServiceNotAvailable';
    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::REQUIRE_API_DETAILS   => "You must define both a username and API key for this identity",
        self::SERVICE_NOT_AVAILABLE => "This service is not available to this identity",
    );
    
    protected $username;
    
    protected $apiKey;
    
    protected $apiEndpoint; 
    
    protected $token;
    
    protected $tokenExpiry;
    
    protected $tenantId;
    
    protected $serviceCatalog = array();
    
    protected $user = array();
    
	public function __construct($username = null, $apiKey = null, $apiEndpoint = self::US_API_ENDPOINT)
    {
        if ($username) {
            $this->setUsername($username);
        }
        if ($apiKey) {
            $this->setApiKey($apiKey);
        }
        $this->setApiEndpoint($apiEndpoint);
    }
    
    /**
     * get the currently set username
     * 
     * @return the $username
     */
    public function getUsername ()
    {
        return $this->username;
    }

	/**
	 * set the username to use
	 * 
     * @param string $username@
     * @return Identity
     */
    public function setUsername ($username)
    {
        $this->username = $username;
        return $this;
    }

	/**
	 * get the currently set API key
	 * 
     * @return the $apiKey
     */
    public function getApiKey ()
    {
        return $this->apiKey;
    }

	/**
	 * set the API key to use
	 * 
     * @param string $apiKey
     * @return Identity
     */
    public function setApiKey ($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

	/**
	 * get the currently set API endpoint
	 * 
     * @return the $apiEndpoint
     */
    public function getApiEndpoint ()
    {
        return $this->apiEndpoint;
    }

	/**
	 * set the API endpoint to use
	 * 
     * @param string $apiEndpoint
     * @return Identity
     */
    public function setApiEndpoint ($apiEndpoint)
    {
        $this->apiEndpoint = $apiEndpoint;
        return $this;
    }

	/**
	 * get the currently set token
	 * 
     * @return the $token
     */
    public function getToken()
    {
        return $this->token;
    }
    
    /**
     * set the token tfor this identity
     * @param string $token
     */
    public function setToken ($token)
    {
        $this->token = $token;
    }

    /**
     * get the token expiry
     * 
     * @return the $tokenExpiry
     */
    public function getTokenExpiry ()
    {
        return $this->tokenExpiry;
    }
    
    /**
     * set the token expiry
     * 
     * @param DateTime $tokenExpiry
     */
    public function setTokenExpiry (\DateTime $tokenExpiry)
    {
        $this->tokenExpiry = $tokenExpiry;
    }

	/**
	 * get the current tennant Id
	 * 
     * @return the $tenantId
     */
    public function getTenantId ()
    {
        return $this->tenantId;
    }

	/**
	 * set the current tenant id
	 * 
     * @param string $tenantId
     */
    public function setTenantId ($tenantId)
    {
        $this->tenantId = $tenantId;
    }
    
    /**
	 * get the currently defined service catalog
	 * 
     * @return the $serviceCatalog
     */
    public function getServiceCatalog ()
    {
        return $this->serviceCatalog;
    }
    
     /**
	 * set the service catalog
	 * 
	 * @param array
     * @return $this
     */
    public function setServiceCatalog(array $serviceCatalog)
    {
        $this->serviceCatalog = array();
        foreach ($serviceCatalog as $service) {
            $this->serviceCatalog['services'][] = $service->name;
            $this->serviceCatalog['catalog'][$service->name] = $service;
        }
        
        return $this;
    }
    
    public function hasService($serviceKey)
    {
        return in_array($serviceKey, $this->serviceCatalog['services']);
    }
    
    /**
     * get details for specific jey from service catalog
     * 
     * @param string $serviceKey
     */
    public function getService($serviceKey)
    {
        if (!$this->hasService($serviceKey)) {
            throw new Exception\RuntimeException(
                $this->messageTemplates[self::SERVICE_NOT_AVAILABLE], 
                500
            );
        }
        
        return $this->serviceCatalog['catalog'][$serviceKey];
    }
    
    /**
     * get the currently defined user
     * 
     * @return the $user;
     */
    public function getCurrentUser()
    {
        return $this->user;
    }
    
    /**
     * set the currently defined user
     * 
     * @param array user data
     * @return $this;
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Authenticate Identity and get data
     * 
     * @return Identity
     */
    public function authenticate()
    {
        if (!$this->getUsername() || !$this->getApiKey()) {
            throw new Exception\RuntimeException(
                $this->messageTemplates[self::REQUIRE_API_DETAILS], 
                500
            );
        }
        
        $uri = self::VERSION . '/tokens';
        
        $response = $this->httpCall(
            $this->getApiEndpoint() . $uri,
            'POST', 
            array(), // $params
            array( // $body
                'auth' => array(
                    'RAX-KSKEY:apiKeyCredentials' => array(
                        'username' => $this->getUsername(),
                        'apiKey' => $this->getApiKey(),
                    )
                ),
            )
        );
        
        $data = $this->decodeBody($response);
        
        // set some data
        $this->token = $data->access->token->id;
        // maybe use this for some caching
        $this->tokenExpiry = new \DateTime($data->access->token->expires); 
        $this->tenantId = $data->access->token->tenant->id;
        
        $this->setServiceCatalog($data->access->serviceCatalog);
        $this->setUser($data->access->user);
        
        return $this;
    }
    
    /**
     * clear the identity of ALL data
     * @return Identity
     */
    protected function clearIdentity()
    {
        $this->token = null;
        $this->tokenExpiry = null;
        $this->tenantId = null;
        $this->serviceCatalog = null;
        $this->user = null;
        
        return $this;
    }
    
    /**
     * simple test for authentication
     * 
     * @return boolean
     */
    public function isValid()
    {
        if (!$this->getToken()) {
            return false;
        }
        
        if (!$this->getTokenExpiry()) {
            return false;
        }
        
        if ($this->getTokenExpiry() >= new \DateTime()) {
            return false;
        }
        
        if (!$this->tenantId) {
            return false;
        }
        
        return true;
    }
    /**
     * List Users
     * @return Ambigous <multitype:, mixed>
     */
    public function listUsers()
    {
        $uri = self::VERSION . '/users';
        $result = $this->httpCall($uri, 'GET');
        
        $data = $this->decodeBody($result);
                
        return $data;
    }
    
    /**
     * Return detailed information about a specific user, by user name.
     * @param string $name
     * @return Ambigous <multitype:, mixed>
     */
    public function getUserByName($name)
    {
        $uri = self::VERSION . '/users';
        $result = $this->httpCall($uri, 'GET', array(
            'name' => $name
        ));
        
        $data = $this->decodeBody($result);
                
        return $data;
    }
    
    /**
     * Return detailed information about a specific user, by user ID.
     * @param string $id
     * @return Ambigous <multitype:, mixed>
     */
    public function getUserById($id)
    {
        $uri = self::VERSION . '/users/' . $id;
        $result = $this->httpCall($uri, 'GET');
        
        $data = $this->decodeBody($result);
                
        return $data;
    }
    
    /**
     * Add a user
     * 
     * N.B. This call is available only to users who hold the admin role 
     * (identity:user-admin). Using the admin role, you can create a user who 
     * holds the user role (identity:default).
     *
     * @param array $user
     * @return Ambigous <multitype:, mixed>
     */
    public function addUser(User $user)
    {
        $uri = self::VERSION . '/users';
        $result = $this->httpCall($uri, 'POST', array(), array(
            'user' => $user
        ));
        
        $data = $this->decodeBody($result);
                
        return $data;
    }
    
    /**
     * Update a user
     * 
     * N.B. Users who hold the admin role can update users who hold the user 
     * role (identity:default) or the admin role (identity:user-admin) for the 
     * same tenant.
     * 
     * @param User £userId
     * @return Ambigous <multitype:, mixed>
     */
    public function updateUser(User $user)
    {
        $uri = self::VERSION . '/users/' . $user->id;
        $result = $this->httpCall($uri, 'POST', array(), array(
            'user' => $user
        ));
        
        $data = $this->decodeBody($result);
                
        return $data;
    }
    
    /**
     * delete a user
     * 
     * 
     * N.B. This call is available only to users who hold the admin role 
     * (identity:user-admin). Using the admin role, you can delete a user who 
     * holds the user role (identity:default) for the same tenant.
     * 
     * @param User $user
     * @return unknown
     */
    public function deleteUser(User $user)
    {
        $uri = self::VERSION . '/users/' . $user->id;
        $result = $this->httpCall($uri, 'DELETE');
        
        $data = $this->decodeBody($result);
                
        return $data;
    }
    
    /**
     * List credentials, other than passwords, for all authentication methods.
     * 
     * @param User $user
     * @return Ambigous <multitype:, mixed>
     */
    public function listCredentials(User $user)
    {
        $uri = self::VERSION . '/users/' . $user->id . '/OS-KSADM/credentials';
        $result = $this->httpCall($uri, 'GET');
        
        $data = $this->decodeBody($result);
                
        return $data;
    }
    
    /**
     * Get user credentials
     * 
     * @param User $user
     * @return Ambigous <multitype:, mixed>
     */
    public function getUserCredentials(User $user) 
    {
        $uri = self::VERSION . '/users/' . $user->id . '/OS-KSADM/credentials/RAX-KSKEY:apiKeyCredentials';
        $result = $this->httpCall($uri, 'GET');
        
        $data = $this->decodeBody($result);
                
        return $data;
    }
    
    /**
     * Return global roles for a specific user
     * 
     * @param User $user
     * @return Ambigous <multitype:, mixed>
     */
    public function listUserGlocalRoles(User $user)
    {
        $uri = self::VERSION . '/users/' . $user->id . '/roles';
        $result = $this->httpCall($uri, 'GET');
        
        $data = $this->decodeBody($result);
                
        return $data;
    }
    
    /**
     * Get a list of tenants
     * 
     * @return Ambigous <multitype:, mixed>
     */
    public function getTenants()
    {
        $uri = self::VERSION . '/tenants';
        $result = $this->httpCall($uri, 'GET');
        
        $data = $this->decodeBody($result);
                
        return $data;
    }
}