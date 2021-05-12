<?php

namespace App\Tests\Service\Ldap;

use App\Service\Ldap\Client;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Exception\LdapException;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\Ldap\Adapter\AdapterInterface;
use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;

class ClientMock extends Client
{
    public const ADAPTER_TEST_CONFIG = array(
        'host' => 'localhost',
        'port' => '389',
        'host' => 'none'
    );

    public const TEST_CONFIG = array(
        'uid_key' => 'uid',
        'mail_key' => 'mail',
        'base_dn' => 'ou=people,dc=planetexpress,dc=com',
        'is_ad' => false,
        'ad_domain' => '',
        'query' => '()',
        'search_dn' => 'cn=admin,dc=planetexpress,dc=com',
        'search_password' => 'GoodNewsEveryone'
    );

    public function __construct()
    {
        $ldapAdapterMock = new AdapterMock(static::ADAPTER_TEST_CONFIG);
        $ldap = new Ldap($ldapAdapterMock);
        parent::__construct($ldap, static::TEST_CONFIG);
    }
}
