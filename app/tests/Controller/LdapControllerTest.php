<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\Adapter\AdapterInterface;
use Symfony\Component\Ldap\Adapter\ConnectionInterface;
use Symfony\Component\Ldap\Adapter\EntryManagerInterface;
use Symfony\Component\Ldap\Adapter\QueryInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class LdapControllerTest extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    private $client = null;

    /**
     * @var  Serializer
     */
    private $serializer;

    public $fullDn = 'uid=john.doe,ou=people,ou=example,ou=com';
    public $userId = ['cubert'];
    public $query = '(cn=Hermes Conrad)';
    public $surname = ['Farnsworth'];
    public $email = ['cubert@planetexpress.com', 'clone@planetexpress.com'];
    public $description = ['Human'];

    public function setUp(): void
    {
        $this->client = $this->createAuthenticatedClient();
    }

    /**
     * Create a client with a default Authorization header.
     *
     * @param string $username
     * @param string $password
     *
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function createAuthenticatedClient($username = 'username', $password = 'pa$$word')
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode(array(
            'username' => $username,
            'password' => $password,
            ))
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));
        //$client->catchExceptions(false);
        return $client;
    }

    //Get Function test : getLdapEntryByDn

    public function testGetLdapEntries()
    {
        $this->client->request('GET', '/api/ldap', ['base'=>$this->fullDn,'query'=>$this->query]);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = $this->client->getResponse()->getContent();
        $responseEntries = json_decode($responseContent, true);
        $this->assertCount(2, $responseEntries);
        $this->assertNotEmpty($responseEntries['items']);
    }

    //Create Function test : createLdapEntry

    public function testCreateLdapEntry()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
        $attr =  $this->serializer->serialize(new Entry(
            'cn=Cubert Farnsworth,ou=people,dc=planetexpress,dc=com',
            [
                "sn"=>["Farnsworth"],
                "objectClass"=>["inetOrgPerson"]
            ]
        ), 'json');

        $this->client->request('POST', '/api/admin/ldap', [], [], [], $attr);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = $this->client->getResponse()->getContent();
        $this->assertEquals("true", $responseContent);
    }

    public function testCreateLdapEntryWithEmptyContent()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);

        $attr =  $this->serializer->serialize(new Entry('', []), 'json');

        $this->client->request('POST', '/api/admin/ldap', [], [], [], $attr);
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateLdapEntryWithoutContent()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);

        $this->client->request('POST', '/api/admin/ldap', [], [], [], null);
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateLdapEntryWithoutQueryAndContent()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);

        $this->client->request('POST', '/api/admin/ldap', [], [], [], null);

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode());
    }

    //Update Function test : editLdapEntryByQuery

    public function testEditLdapEntry()
    {
        $uri = '/api/admin/ldap/cn=Hermes Conrad,ou=people,dc=planetexpress,dc=com';
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
        $attr =  $this->serializer->serialize(new Entry(
            'cn=Hermes Conrad,ou=people,dc=planetexpress,dc=com',
            [
                'sn' => ['Con'],
                'objectClass' => ['inetOrgPerson'],
                'description' => ['Decapodian']
            ]
        ), 'json');
       
        $this->client->request('PUT', $uri, [], [], [], $attr);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = $this->client->getResponse()->getContent();
        $this->assertEquals("true", $responseContent);
    }

    public function testEditLdapEntryByQuery()
    {
        $query = "(description=Human)";
        $uri = '/api/admin/ldap/cn=Hermes Conrad,ou=people,dc=planetexpress,dc=com';
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
        $attr =  $this->serializer->serialize(new Entry(
            'cn=Hermes Conrad,ou=people,dc=planetexpress,dc=com',
            [
                'sn' => ['Con'],
                'objectClass' => ['inetOrgPerson'],
                'description' => ['Decapodian']
            ]
        ), 'json');
       
        $this->client->request('PUT', $uri, [$query], [], [], $attr);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = $this->client->getResponse()->getContent();
        $this->assertEquals("true", $responseContent);
    }

    public function testEditLdapEntryWithoutParameters()
    {
        $uri = '/api/admin/ldap';
        $query = "(description=Human)";

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);

        $attr =  $this->serializer->serialize(new Entry(
            'cn=Hermes Conrad,ou=people,dc=planetexpress,dc=com',
            [
                'sn' => ['Con'],
                'objectClass' => ['inetOrgPerson'],
                'description' => ['Decapodian']
            ]
        ), 'json');

        $this->client->request('PUT', $uri, [$query], [], [], $attr);
        $this->assertSame(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    public function testEditLdapEntryWithoutContent()
    {
        $uri = '/api/admin/ldap/cn=Hermes Conrad,ou=people,dc=planetexpress,dc=com';
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);

        $this->client->request('PUT', $uri, [], [], [], null);
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode());
    }

    //Delete Function test : deleteLdapEntry

    public function testDeleteLdapEntry()
    {
        $this->client->request('DELETE', "/api/admin/ldap/cn=Test Test,ou=people,dc=planetexpress,dc=com");
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = $this->client->getResponse()->getContent();
        $this->assertEquals("true", $responseContent);
    }

    public function testDeleteLdapEntryWithoutDn()
    {
        $this->client->request('DELETE', "/api/admin/ldap");
        $this->assertSame(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteLdapEntryNotExisting()
    {
        $this->client->request('DELETE', "/api/admin/ldap/not exist");
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode());
    }
}
