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

    public $userId = ['professor'];
    public $commonName = 'cn=TEST T. TESTO';
    public $surname = ['test'];
    public $email = ["TEST@planetexpress.com", "testo@planetexpress.com"];
    public $description = ['test'];

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

        return $client;
    }

    public function buildLdapMock()
    {
        $this->ldapQueryMock = $this->getMockBuilder(QueryInterface::class)
            ->disableOriginalClone()
            ->disableProxyingToOriginalMethods()
            ->disableOriginalConstructor()
            ->setMethods(['execute'])
            ->getMock();

        $this->ldapAdapterMock = $this->getMockBuilder(AdapterInterface::class)
            ->disableOriginalClone()
            ->disableProxyingToOriginalMethods()
            ->disableOriginalConstructor()
            ->setMethods(['getConnection', 'createQuery', 'getEntryManager', 'escape'])
            ->getMock();

        $this->ldapEntryManagerMock = $this->getMockBuilder(EntryManagerInterface::class)
            ->disableOriginalClone()
            ->disableProxyingToOriginalMethods()
            ->disableOriginalConstructor()
            ->setMethods(['add','update','rename','remove'])
            ->getMock();

        $this->ldapConnectionMock = $this->getMockBuilder(ConnectionInterface::class)
            ->disableOriginalClone()
            ->disableProxyingToOriginalMethods()
            ->disableOriginalConstructor()
            ->setMethods(['isBound', 'bind'])
            ->getMock();
    }

    public function testGetLdapEntries()
    {
        $this->buildLdapMock();

        $this->ldapConnectionMock->expects($this->exactly(0))
            ->method('isBound')
            ->willReturn(true);

        $this->ldapQueryMock->expects($this->any())
            ->method('execute')
            ->willReturn([
                new Entry(
                    "dn=test,testbasdn",
                    [
                        'uid' => $this->userId,
                        'mail' => $this->email,
                        'sn' => $this->surname,
                        'description' => $this->description
                    ]
                )
            ]);

        $this->ldapConnectionMock->expects($this->once())
            ->method('bind');

        $this->ldapAdapterMock->expects($this->once())
            ->method('getConnection')
            ->willReturn($this->ldapConnectionMock);

        $this->ldapAdapterMock->expects($this->once())
            ->method('createQuery')
            ->willReturn($this->ldapQueryMock);

        // TODO Mock the LDAP Adapter service.
        $container = self::$container;
        $container->set('Symfony\Component\Ldap\Adapter\ExtLdap\Adapter', $this->ldapAdapterMock);
        //$container->set('test.Symfony\Component\Ldap\Adapter\ExtLdap\Adapter', $this->ldapAdapterMock);

        var_dump($container->initialized('Symfony\Component\Ldap\Adapter\ExtLdap\Adapter'));

        $query = '(&(description=Human)(objectClass=inetOrgPerson))';
        $attr = ["cn","sn"];

        $this->client->request('GET', '/api/ldap', ['query'=>$query,'attributes'=>$attr]);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = $this->client->getResponse()->getContent();
        //var_dump(json_decode($responseContent));
    }

    /*
    public function testCreateLdapEntryByQuery()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
        $attr =  $this->serializer->serialize(new Entry(
            'cn=Test Test,ou=people,dc=planetexpress,dc=com',
            [
                "sn"=>["ts"],
                "objectClass"=>["inetOrgPerson"]
            ]
        ), 'json');
        //var_dump($attr);
        $this->client->request('POST', '/api/admin/ldap',[],[],[],$attr);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = $this->client->getResponse()->getContent();
        var_dump($responseContent);
    }*/

    /*
    public function testEditLdapEntryByQuery()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
        $attr =  $this->serializer->serialize(new Entry(
            '',
            [
                'description' => ["Decapodian"]
            ]
        ), 'json');

        $this->client->request('PUT', '/api/admin/ldap/cn=Hubert J. Farnsworth',[],[],[],$attr);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = $this->client->getResponse()->getContent();
        var_dump($responseContent);
    }*/

    /*
    public function testDeleteLdapEntryByQuery()
    {
        $fullDn = 'cn=Test Test,ou=people,dc=planetexpress,dc=com';
        $this->client->request('DELETE', "/api/admin/ldap/{$fullDn}");
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = $this->client->getResponse()->getContent();
        var_dump($responseContent);
    }*/

}