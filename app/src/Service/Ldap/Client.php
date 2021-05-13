<?php

namespace App\Service\Ldap;

use Symfony\Component\Ldap\Adapter\CollectionInterface;
use Symfony\Component\Ldap\Adapter\QueryInterface;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Exception\LdapException;
use Symfony\Component\Ldap\Exception\ConnectionException;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class Client
{
    public const REQUIRED = [
        'uid_key',
        'mail_key',
        'base_dn',
        'is_ad',
        'ad_domain',
        'query',
        'search_dn',
        'search_password'
    ];

    /**
     * @var Ldap
     */
    private $ldap;

    /**
     * @var array
     */
    private $config;

    public function __construct(
        Ldap $ldap,
        array $ldapConfig
    ) {
        foreach (self::REQUIRED as $one) {
            if (!isset($ldapConfig[$one])) {
                throw new \RuntimeException("LDAP required config key was not found: $one");
            }
        }
        $this->ldap = $ldap;
        $this->config = $ldapConfig;
    }

    public function check(
        string $login,
        string $password
    ) {

        $username = $this->ldap->escape($login, '', LdapInterface::ESCAPE_FILTER);
        $query = sprintf(
            '(&(|(%s=%s)(%s=%s))%s)',
            $this->config['uid_key'],
            $username,
            $this->config['mail_key'],
            $username,
            $this->config['query']
        );

        if ($this->config['search_dn']) {
            // Login with default credentials and search user
            $this->bind();
            $results = $this->search($query);

            if (1 !== count($results)) {
                throw new BadCredentialsException('The presented username is invalid.');
            }

            $fullDn = $results[0]->getDn();
        } else {
            // Build full DN based on user ID and LDAP config
            $username = $this->ldap->escape($login, '', LdapInterface::ESCAPE_DN);
            $fullDn = sprintf('%s=%s,%s', $this->config['uid_key'], $username, $this->config['base_dn']);
        }

        $this->bind($fullDn, $password);

        $results = $this->executeQuery($query, $fullDn);

        $result = $results[0];

        return $result;
    }

    /**
     * Open a connection bound to the LDAP.
     *
     * If no username / password is given, bind will be done with default search
     * credentials from LDAP configuration.
     *
     * @param string $username A LDAP dn
     * @param string $password A password
     *
     * @throws ConnectionException if username / password could not be bound
     *
     * @return void
     */
    public function bind($username = null, $password = null): void
    {
        if (empty($username) && empty($password)) {
            $username = $this->config['search_dn'];
            $password = $this->config['search_password'];
        }

        $this->ldap->bind($username, $password);
    }

    /**
     * @return Entry[]|CollectionInterface
     *
     * @throws LdapException When option given doesn't match a LDAP entry
     *
     * @$ldappsalm-return CollectionInterface|array<array-key, Entry>
     */
    private function executeQuery(string $query, string $base_dn = null, array $options = [])
    {
        if (empty($base_dn)) {
            $base_dn = $this->config['base_dn'];
        }

        return $this->ldap->query($base_dn, $query, $options)->execute();
    }

    /**
     * @return String|bool
     *
     * @throws LdapException When the query given was not right
     */
    public function create(string $fullDn, array $attributes): String
    {
        $entryManager = $this->ldap->getEntryManager();
        $entry = new Entry($fullDn, $attributes);

        $entryManager->add($entry);

        // XXX Check if its possible to return the saved LDAP entry.
        //TODO IMPROVE THE QUERY
        try {
            $result = $this->search('(objectClass=*)', $fullDn)[0]->getDn();
            return !empty($result) ? $result: false;
        } catch (LdapException $e) {
            throw $e;
        }

        return false;
        //return !empty($entryManager->add($entry));
    }

    /**
     * @return bool
     *
     * @throws LdapException
     */
    public function update(string $fullDn, string $query, array $attributes = []) : bool
    {
        // Finding and updating an existing entry
        $entryManager = $this->ldap->getEntryManager();
        $entry = $this->get($query, $fullDn);

        if (empty($entry)) {
            return false;
        }

        foreach ($attributes as $key => $value) {
            $entry->setAttribute($key, $value);
        }
        // XXX Check if it's possible to return the saved LDAP entry.
        $entryManager->update($entry);

        return true;
    }

    /**
     * Delete an entry from a directory.
     *
     * @return bool
     *
     * @throws LdapException
     */
    public function delete(string $fullDn)
    {
        // Removing an existing entry
        $entryManager = $this->ldap->getEntryManager();
        $entryManager->remove(new Entry($fullDn));

        return true;
    }

    /**
     * Search LDAP tree.
     *
     * @return Entry[]|CollectionInterface
     *
     * @throws LdapException
     *
     * @psalm-return CollectionInterface|array<array-key, Entry>
     */
    public function search(string $query, string $base_dn = null, array $options = ['scope' => QueryInterface::SCOPE_SUB])
    {
        $search_options = array_merge(['scope' => QueryInterface::SCOPE_SUB], $options);
        return $this->executeQuery($query, $base_dn, $search_options);
    }

    /**
     * Single-level search.
     *
     * @return Entry[]|CollectionInterface
     *
     * @throws LdapException
     *
     * @psalm-return CollectionInterface|array<array-key, Entry>
     */
    public function list(string $query, string $base_dn = null, array $options = [])
    {
        $list_options = array_merge(['scope' => QueryInterface::SCOPE_ONE], $options);
        return $this->executeQuery($query, $base_dn, $list_options);
    }

    /**
     * Read an entry.
     *
     * @return Entry
     *
     * @throws LdapException
     *
     * @psalm-return Entry
     */
    public function get(string $query, string $base_dn = null, array $options = [])
    {
        $get_options = array_merge(['scope' => QueryInterface::SCOPE_BASE], $options);
        $results = $this->executeQuery($query, $base_dn, $get_options);

        return !empty($results) && 1 === count($results) ? $results[0] : null;
    }
}
