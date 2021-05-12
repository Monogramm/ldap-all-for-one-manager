<?php

namespace App\Tests\Service\Ldap;

use Symfony\Component\Ldap\Adapter\EntryManagerInterface;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Exception\LdapException;
use Symfony\Component\Ldap\Exception\NotBoundException;
use Symfony\Component\Ldap\Exception\UpdateOperationException;

class EntryManagerMock implements EntryManagerInterface
{
    private $connection;

    public function __construct(ConnectionMock $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function add(Entry $entry)
    {
        $con = $this->getConnectionResource();

        // TODO Define expected responses for tests
        switch ($entry->getDn()) {
            case 'uid=exception':
                throw new LdapException(sprintf('Could not add entry "%s": ', $entry->getDn()));
                break;

            default:
                # code...
                break;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function update(Entry $entry)
    {
        $con = $this->getConnectionResource();

        // TODO Define expected responses for tests
        switch ($entry->getDn()) {
            case 'uid=exception':
                throw new LdapException(sprintf('Could not update entry "%s": ', $entry->getDn()));
                break;

            default:
                # code...
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Entry $entry)
    {
        $con = $this->getConnectionResource();

        // TODO Define expected responses for tests
        switch ($entry->getDn()) {
            case 'uid=exception':
                throw new LdapException(sprintf('Could not remove entry "%s": ', $entry->getDn()));
                break;

            default:
                # code...
                break;
        }
    }

    /**
     * Adds values to an entry's multi-valued attribute from the LDAP server.
     *
     * @throws NotBoundException
     * @throws LdapException
     */
    public function addAttributeValues(Entry $entry, string $attribute, array $values)
    {
        $con = $this->getConnectionResource();

        // TODO Define expected responses for tests
        switch ($entry->getDn()) {
            case 'uid=exception':
                throw new LdapException(sprintf('Could not add values to entry "%s", attribute "%s": ', $entry->getDn(), $attribute));
                break;

            default:
                # code...
                break;
        }
    }

    /**
     * Removes values from an entry's multi-valued attribute from the LDAP server.
     *
     * @throws NotBoundException
     * @throws LdapException
     */
    public function removeAttributeValues(Entry $entry, string $attribute, array $values)
    {
        $con = $this->getConnectionResource();

        // TODO Define expected responses for tests
        switch ($entry->getDn()) {
            case 'uid=exception':
                throw new LdapException(sprintf('Could not remove values from entry "%s", attribute "%s": ', $entry->getDn(), $attribute));
                break;

            default:
                # code...
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rename(Entry $entry, $newRdn, $removeOldRdn = true)
    {
        $con = $this->getConnectionResource();

        // TODO Define expected responses for tests
        switch ($entry->getDn()) {
            case 'uid=exception':
                throw new LdapException(sprintf('Could not rename entry "%s" to "%s": ', $entry->getDn(), $newRdn));
                break;

            default:
                # code...
                break;
        }
    }

    /**
     * Moves an entry on the Ldap server.
     *
     * @throws NotBoundException if the connection has not been previously bound
     * @throws LdapException     if an error is thrown during the rename operation
     */
    public function move(Entry $entry, string $newParent)
    {
        $con = $this->getConnectionResource();
        $rdn = $this->parseRdnFromEntry($entry);

        // TODO Define expected responses for tests
        switch ($entry->getDn()) {
            case 'uid=exception':
                throw new LdapException(sprintf('Could not move entry "%s" to "%s": ', $rdn, $newParent));
                break;

            default:
                # code...
                break;
        }
    }

    /**
     * Get the connection resource, but first check if the connection is bound.
     */
    private function getConnectionResource()
    {
        // If the connection is not bound, throw an exception. Users should use an explicit bind call first.
        if (!$this->connection->isBound()) {
            throw new NotBoundException('Query execution is not possible without binding the connection first.');
        }

        return $this->connection->getResource();
    }

    /**
     * @param iterable|UpdateOperation[] $operations An array or iterable of UpdateOperation instances
     *
     * @throws UpdateOperationException in case of an error
     */
    public function applyOperations(string $dn, iterable $operations): void
    {
        $operationsMapped = [];
        foreach ($operations as $modification) {
            $operationsMapped[] = $modification->toArray();
        }

        $con = $this->getConnectionResource();
        // TODO Define expected responses for tests
        switch ($dn) {
            case 'uid=exception':
                throw new UpdateOperationException(sprintf('Error executing UpdateOperation on "%s": "%s".', $dn, null));
                break;

            default:
                # code...
                break;
        }
    }

    private function parseRdnFromEntry(Entry $entry): string
    {
        if (!preg_match('/(^[^,\\\\]*(?:\\\\.[^,\\\\]*)*),/', $entry->getDn(), $matches)) {
            throw new LdapException(sprintf('Entry "%s" malformed, could not parse RDN.', $entry->getDn()));
        }

        return $matches[1];
    }
}
