<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;

/**
 * This will suppress all the PMD warnings in
 * this class.
 *
 * @SuppressWarnings(PHPMD)
 */
class LdapControllerTest extends AuthenticatedWebTestCase
{
    /**
     * @var KernelBrowser
     */
    private $client = null;

    public $fullDn = 'cn=Hermes Conrad,ou=people,dc=planetexpress,dc=com';
    public $query = '(cn=Hermes Conrad)';

    public function setUp(): void
    {
        $this->client = $this->createAuthenticatedClient();
    }

    /**
     * Function test getLdapEntries normal use
     */
    public function testGetLdapEntries()
    {
        $this->client->request('GET', '/api/ldap', ['base'=>'uid=john.doe,ou=people,ou=example,ou=com','query'=>$this->query]);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = $this->client->getResponse()->getContent();
        $responseEntries = json_decode($responseContent, true);
        $this->assertCount(2, $responseEntries);
        $this->assertNotEmpty($responseEntries['items']);
    }

    
    /**
     * Function test getLdapEntryByDn normal use
     */
    public function testgetLdapEntryByDn()
    {
        $param = urlencode($this->fullDn);
        $this->client->request('GET', "/api/ldap/$param");

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = $this->client->getResponse()->getContent();
        $responseEntries = json_decode($responseContent, true);
        $this->assertCount(2, $responseEntries);
        $this->assertNotEmpty($responseEntries['attributes']);
    }

    /**
     * Function test getLdapEntryByDn Entry doesn't exist
     */
    public function testgetLdapEntryByDnNoEntry()
    {
        $param = 'not-exist';
        $this->client->request('GET', "/api/ldap/$param");
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Function test createLdapEntry normal use
     */
    public function testCreateLdapEntry()
    {
        $attr = '{
            "dn":"cn=Cubert Farnsworth,ou=people,dc=planetexpress,dc=com",
            "attributes":{
                "sn": ["Farnsworth"],
                "objectClass": ["inetOrgPerson"]
            }
        }';

        $this->client->request('POST', '/api/admin/ldap', [], [], [], $attr);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = $this->client->getResponse()->getContent();
        $this->assertEquals("true", $responseContent);
    }

    /**
     * Function test createLdapEntry throw Ldap error Empty entry
     */
    public function testCreateLdapEntryEmptyEntry()
    {
        $attr = '{
            "dn":"",
            "attributes":{}
        }';

        $this->client->request('POST', '/api/admin/ldap', [], [], [], $attr);
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Function test editLdapEntryByQuery normal use
     */
    public function testEditLdapEntry()
    {
        $param = urlencode($this->fullDn);

        $attr = '{
            "dn":"cn=Hermes Conrad,ou=people,dc=planetexpress,dc=com",
            "attributes":{
                "sn": ["Con"],
                "objectClass": ["inetOrgPerson"],
                "description": ["Decapodian"]
            }
        }';

        $this->client->request('PUT', "/api/admin/ldap/$param", [], [], [], $attr);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = $this->client->getResponse()->getContent();
        $this->assertEquals("true", $responseContent);
    }

    /**
     * Function test editLdapEntryByQuery using query
     */
    public function testEditLdapEntryByQuery()
    {
        $query = '(description=Human)';
        $param = urlencode($this->fullDn);

        $attr = '{
            "dn":"cn=Hermes Conrad,ou=people,dc=planetexpress,dc=com",
            "attributes":{
                "sn": ["Con"],
                    "objectClass": ["inetOrgPerson"],
                    "description": ["Decapodian"]
            }
        }';

        $this->client->request('PUT', "/api/admin/ldap/$param", ['query'=>$query], [], [], $attr);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = $this->client->getResponse()->getContent();
        $this->assertEquals("true", $responseContent);
    }

        /**
     * Function test editLdapEntryByQuery Entry to update don't exist
     */
    public function testEditLdapEntryNoEntry()
    {
        $query = '(description=Human)';
        $param = 'not-exist';

        $attr = '{
            "dn":"not-exist",
            "attributes":{
                "sn": ["Con"],
                    "objectClass": ["inetOrgPerson"],
                    "description": ["Decapodian"]
            }
        }';

        $this->client->request('PUT', "/api/admin/ldap/$param", ['query'=>$query], [], [], $attr);
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Function test deleteLdapEntry normal use
     */
    public function testDeleteLdapEntry()
    {
        $param = urlencode($this->fullDn);
        $this->client->request('DELETE', "/api/admin/ldap/$param");
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = $this->client->getResponse()->getContent();
        $this->assertEquals("true", $responseContent);
    }

    /**
     * Function test deleteLdapEntry delete non existing entry
     */
    public function testDeleteLdapEntryNotExisting()
    {
        $this->client->request('DELETE', '/api/admin/ldap/not-exist');
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode());
    }
}
