<?php

namespace App\Tests\Service\Ldap;

use Symfony\Component\Ldap\Adapter\CollectionInterface;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Exception\LdapException;

/**
 * This will suppress all the PMD warnings in
 * this class.
 *
 * @SuppressWarnings(PHPMD)
 */
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
        $count = 1;
        return $count;
    }

    /**
     * @return \Traversable
     */
    public function getIterator()
    {
        $searches = $this->search->getResources();

        foreach ($searches as $search) {
            $current = $search;

            if (false === $current) {
                throw new LdapException('Could not rewind entries array: ');
            }
            //TODO verify that con is not useful for the test
            //yield $this->getSingleEntry($con,$current);
            yield $this->getSingleEntry($current);
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

    private function getSingleEntry($current): Entry
    {
        //Check the depth of the array
        switch ($current) {
            case isset($current[0]):
                $attributes = $current[0]['attributes'];
                break;
            case isset($current['attributes']):
                $attributes = $current['attributes'];
                break;
            default:
                throw new LdapException('Could not fetch attributes: ');
        }

        //Check the depth of the array
        switch ($current) {
            case isset($current[0]):
                $distinguishedNames = $current[0]['dn'];
                break;
            case isset($current['dn']):
                $distinguishedNames = $current['dn'];
                break;
            default:
                throw new LdapException('Could not fetch DN: ');
        }
        
        return new Entry($distinguishedNames, $attributes);
    }
}
