<?php

namespace App\Tests\Service\Ldap;

use Symfony\Component\Ldap\Adapter\CollectionInterface;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Exception\LdapException;

class CollectionMock implements CollectionInterface
{
    private $connection;
    private $search;
    private $entries;

    public function __construct(ConnectionMock $connection, QueryMock $search)
    {
        $this->connection = $connection;
        $this->search = $search;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        if (null === $this->entries) {
            $this->entries = iterator_to_array($this->getIterator(), false);
        }

        return $this->entries;
    }

    /**
     * @return int
     */
    public function count()
    {
        $con = $this->connection->getResource();
        $searches = $this->search->getResources();
        $count = 0;
        foreach ($searches as $search) {
            // TODO Define expected responses for tests
            $searchCount = 0;
            if (false === $searchCount) {
                throw new LdapException('Error while retrieving entry count: ');
            }
            $count += $searchCount;
        }

        return $count;
    }

    /**
     * @return \Traversable
     */
    public function getIterator()
    {
        if (0 === $this->count()) {
            return;
        }

        $con = $this->connection->getResource();
        $searches = $this->search->getResources();
        foreach ($searches as $search) {
            // TODO Define expected responses for tests
            $current = ldap_first_entry($con, $search);

            if (false === $current) {
                throw new LdapException('Could not rewind entries array: ');
            }

            yield $this->getSingleEntry($con, $current);

            // TODO Define expected responses for tests
            while (false !== $current = ldap_next_entry($con, $current)) {
                yield $this->getSingleEntry($con, $current);
            }
        }
    }

    /**
     * @return bool
     */
    public function offsetExists($offset)
    {
        $this->toArray();

        return isset($this->entries[$offset]);
    }

    public function offsetGet($offset)
    {
        $this->toArray();

        return $this->entries[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        $this->toArray();

        $this->entries[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        $this->toArray();

        unset($this->entries[$offset]);
    }

    private function getSingleEntry($con, $current): Entry
    {
        // TODO Define expected responses for tests
        $attributes = ldap_get_attributes($con, $current);

        if (false === $attributes) {
            throw new LdapException('Could not fetch attributes: ');
        }

        $attributes = $this->cleanupAttributes($attributes);

        // TODO Define expected responses for tests
        $dn = ldap_get_dn($con, $current);

        if (false === $dn) {
            throw new LdapException('Could not fetch DN: ');
        }

        return new Entry($dn, $attributes);
    }

    private function cleanupAttributes(array $entry): array
    {
        $attributes = array_diff_key($entry, array_flip(range(0, $entry['count'] - 1)) + [
                'count' => null,
                'dn' => null,
            ]);
        array_walk($attributes, function (&$value) {
            unset($value['count']);
        });

        return $attributes;
    }
}
