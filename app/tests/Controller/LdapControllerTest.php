<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

/**
 * This will suppress all the PMD warnings in
 * this class.
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class LdapControllerTest extends AuthenticatedWebTestCase
{
    /**
     * @var KernelBrowser
     */
    private $client = null;

    public $fullDn = 'cn=Hermes Conrad,ou=people,dc=planetexpress,dc=com';
    public $query = '(description=Human)';

    public function setUp(): void
    {
        $this->client = $this->createAuthenticatedClient();
    }

    /**
     * Function test getLdapEntries normal use
     */
    public function testGetLdapEntries()
    {
        $this->client->request(
            'GET',
            '/api/ldap',
            [
                'base'=>$this->fullDn,
                'query'=>$this->query,
                'filters'=>'[]',
                'attributes'=>['dn'],
                'size'=>0,
                'max'=>0
            ]
        );

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = $this->client->getResponse()->getContent();
        $responseEntries = json_decode($responseContent, true);
        $this->assertNotEmpty($responseEntries['items']);
    }

    /**
     * Function test getLdapEntries without parameter
     */
    public function testGetLdapEntriesWithoutParameters()
    {
        $this->client->request('GET', '/api/ldap', []);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = $this->client->getResponse()->getContent();
        $responseEntries = json_decode($responseContent, true);
        $this->assertNotEmpty($responseEntries['items']);
    }

    /**
     * Function test getLdapEntries Entry doesn't exist
     */
    public function testGetLdapEntriesNoEntry()
    {
        $this->client->request(
            'GET',
            '/api/ldap',
            [
                'base'=>'not-exist',
                'query'=>'',
                'filters'=>'[]',
                'attributes'=>['dn'],
                'size'=>0,
                'max'=>0
            ]
        );

        $this->assertSame(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            $this->client->getResponse()->getStatusCode()
        );
    }

    /**
     * Function test getLdapEntries Error
     */
    public function testGetLdapEntriesWError()
    {
        $this->client->request('GET', '/api/ldap', ['base'=>'empty',]);

        $this->assertSame(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            $this->client->getResponse()->getStatusCode()
        );
    }

    /**
     * Function test getLdapEntryByDn normal use
     */
    public function testGetLdapEntryByDn()
    {
        $this->client->request('GET', "/api/ldap/$this->fullDn");

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = $this->client->getResponse()->getContent();
        $responseEntries = json_decode($responseContent, true);
        $this->assertCount(2, $responseEntries);
        $this->assertNotEmpty($responseEntries['attributes']);
    }

    /**
     * Function test getLdapEntryByDn Entry doesn't exist
     */
    public function testGetLdapEntryByDnNoEntry()
    {
        $param = 'not-exist';
        $this->client->request('GET', "/api/ldap/$param");
        $this->assertSame(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            $this->client->getResponse()->getStatusCode()
        );
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
        $this->assertSame(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            $this->client->getResponse()->getStatusCode()
        );
    }

    /**
     * Function test editLdapEntryByQuery normal use
     */
    public function testEditLdapEntry()
    {

        $attr = '{
            "dn":"cn=Hermes Conrad,ou=people,dc=planetexpress,dc=com",
            "attributes":{
                "sn": ["Con"],
                "objectClass": ["inetOrgPerson"],
                "description": ["Decapodian"]
            }
        }';

        $this->client->request('PUT', "/api/admin/ldap/$this->fullDn", [], [], [], $attr);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = $this->client->getResponse()->getContent();
        $this->assertEquals("true", $responseContent);
    }

    /**
     * Function test editLdapEntryByQuery using query
     */
    public function testEditLdapEntryByQuery()
    {
        $attr = '{
            "dn":"cn=Hermes Conrad,ou=people,dc=planetexpress,dc=com",
            "attributes":{
                "sn": ["Con"],
                    "objectClass": ["inetOrgPerson"],
                    "description": ["Decapodian"]
            }
        }';

        $this->client->request('PUT', "/api/admin/ldap/$this->fullDn", ['query'=>$this->query], [], [], $attr);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = $this->client->getResponse()->getContent();
        $this->assertEquals("true", $responseContent);
    }

        /**
     * Function test editLdapEntryByQuery Entry to update don't exist
     */
    public function testEditLdapEntryNoEntry()
    {
        $param = 'not-exist';
        $attr = '{
            "dn":"not-exist",
            "attributes":{
                "sn": ["Con"],
                    "objectClass": ["inetOrgPerson"],
                    "description": ["Decapodian"]
            }
        }';

        $this->client->request('PUT', "/api/admin/ldap/$param", ['query'=> $this->query], [], [], $attr);
        $this->assertSame(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            $this->client->getResponse()->getStatusCode()
        );
    }

    /**
     * Function test deleteLdapEntry normal use
     */
    public function testDeleteLdapEntry()
    {
        $this->client->request('DELETE', "/api/admin/ldap/$this->fullDn");
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
        $this->assertSame(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            $this->client->getResponse()->getStatusCode()
        );
    }
}
