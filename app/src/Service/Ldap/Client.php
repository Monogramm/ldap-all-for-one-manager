<?php

namespace App\Service\Ldap;

use Symfony\Component\Ldap\Adapter\QueryInterface;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Exception\LdapException;
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
            $this->ldap->bind($this->config['search_dn'], $this->config['search_password']);
            $result = $this->ldap->query($this->config['base_dn'], $query)->execute();
            if (1 !== $result->count()) {
                throw new BadCredentialsException('The presented username is invalid.');
            }

            $fullDn = $result[0]->getDn();
        } else {
            $username = $this->ldap->escape($login, '', LdapInterface::ESCAPE_DN);
            $fullDn = sprintf('%s=%s,%s', $this->config['uid_key'], $username, $this->config['base_dn']);
        }

        $this->ldap->bind($fullDn, $password);

        $results = $this->ldap->query($fullDn, $query)->execute();
        // TODO Do not bind in executeQuery
        //$results = $this->executeQuery($query, $fullDn);

        $result = $results[0];

        return $result;
    }

    /**
     * @return Entry[]|\Symfony\Component\Ldap\Adapter\CollectionInterface
     *
     * @throws LdapException When option given doesn't match a ldap entry
     *
     * @$ldappsalm-return \Symfony\Component\Ldap\Adapter\CollectionInterface|array<array-key, Entry>
     */
    private function executeQuery(string $query, string $base_dn = null, array $options = [])
    {
        // TODO Do not bind in executeQuery (must be done only once before executing query)
        $this->ldap->bind($this->config['search_dn'], $this->config['search_password']);

        if (empty($base_dn)) {
            $base_dn = $this->config['base_dn'];
        }

        return $this->ldap->query($base_dn, $query, $options)->execute();
    }

    /**
     * @return bool
     *
     * @throws LdapException When the query given was not right
     */
    public function create(string $fullDn, array $attributes): bool
    {
        // TODO Do not bind inside search (must be done before)
        $entryManager = $this->ldap->getEntryManager();
        $this->ldap->bind($this->config['search_dn'], $this->config['search_password']);

        $entry = new Entry($fullDn, $attributes);

        // XXX Check if its possible to return the saved LDAP entry.
        return !empty($entryManager->add($entry));
    }

    /**
     * @return bool
     *
     * @throws LdapException
     */
    public function update(string $query, array $attributes) : bool
    {
        $entryManager = $this->ldap->getEntryManager();

        // TODO Replace query by fullDn.
        // Finding and updating an existing entry
        $result = $this->executeQuery($query);

        // FIXME Check result before doing anything on it
        $entry = $result[0];

        if (empty($entry)) {
            return false;
        }

        foreach ($attributes as $key => $value) {
            $entry->setAttribute($key, $value);
        }
        // XXX Check if its possible to return the saved LDAP entry.
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
        // TODO Do not bind here (must be done only once before executing query)
        $this->ldap->bind($this->config['search_dn'], $this->config['search_password']);

        // Removing an existing entry
        $entryManager = $this->ldap->getEntryManager();
        $entryManager->remove(new Entry($fullDn));

        return true;
    }

    /**
     * Search LDAP tree.
     *
     * @return Entry[]|\Symfony\Component\Ldap\Adapter\CollectionInterface
     *
     * @throws LdapException
     *
     * @psalm-return \Symfony\Component\Ldap\Adapter\CollectionInterface|array<array-key, Entry>
     */
    public function search(string $query, string $base_dn = null, array $options = ['scope' => QueryInterface::SCOPE_SUB])
    {
        $search_options = array_merge(['scope' => QueryInterface::SCOPE_SUB], $options);
        return $this->executeQuery($query, $base_dn, $search_options);
    }

    /**
     * Single-level search.
     *
     * @return Entry[]|\Symfony\Component\Ldap\Adapter\CollectionInterface
     *
     * @throws LdapException
     *
     * @psalm-return \Symfony\Component\Ldap\Adapter\CollectionInterface|array<array-key, Entry>
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
        return $this->executeQuery($query, $base_dn, $get_options);
    }
}
