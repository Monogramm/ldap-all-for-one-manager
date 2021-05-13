<?php

namespace App\Tests\Service\Ldap;

use Symfony\Component\Ldap\Adapter\AbstractQuery;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Exception\LdapException;
use Symfony\Component\Ldap\Exception\NotBoundException;

class QueryMock extends AbstractQuery
{
    /** @var ConnectionMock */
    protected $connection;

    /** @var array */
    private $results;

    /** @var array */
    private $serverctrls = [];

    public function __construct(ConnectionMock $connection, string $dn, string $query, array $options = [])
    {
        parent::__construct($connection, $dn, $query, $options);
    }

    public function __sleep()
    {
        throw new \BadMethodCallException('Cannot serialize '.__CLASS__);
    }

    public function __wakeup()
    {
        throw new \BadMethodCallException('Cannot unserialize '.__CLASS__);
    }

    public function __destruct()
    {
        $con = $this->connection->getResource();
        $this->connection = null;

        if (null === $this->results) {
            return;
        }

        $this->results = null;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (null === $this->results) {
            // If the connection is not bound, throw an exception. Users should use an explicit bind call first.
            if (!$this->connection->isBound()) {
                throw new NotBoundException('Query execution is not possible without binding the connection first.');
            }

            $this->results = [];
            $con = $this->connection->getResource();
            
            switch ($this->options['scope']) {
                case static::SCOPE_BASE:
                    $func = 'ldap_read';
                    break;
                case static::SCOPE_ONE:
                    $func = 'ldap_list';
                    break;
                case static::SCOPE_SUB:
                    $func = 'ldap_search';
                    break;
                default:
                    throw new LdapException(sprintf('Could not search in scope "%s".', $this->options['scope']));
            }

            $itemsLeft = $maxItems = $this->options['maxItems'];
            $pageSize = $this->options['pageSize'];
            // Deal with the logic to handle maxItems properly. If we can satisfy it in
            // one request based on pageSize, we don't need to bother sending page control
            // to the server so that it can determine what we already know.
            if (0 !== $maxItems && $pageSize > $maxItems) {
                $pageSize = 0;
            } elseif (0 !== $maxItems) {
                $pageSize = min($maxItems, $pageSize);
            }
            $pageControl = $this->options['scope'] != static::SCOPE_BASE && $pageSize > 0;
            $cookie = '';
            do {
                $sizeLimit = $itemsLeft;
                if ($pageSize > 0 && $sizeLimit >= $pageSize) {
                    $sizeLimit = 0;
                }
                // TODO Define expected responses for tests
                $search = $this->callSearchFunction($con, $func, $sizeLimit);

                if (false === $search) {
                    $ldapError = 'LDAP error';

                    throw new LdapException(sprintf('Could not complete search with dn "%s", query "%s" and filters "%s".%s.', $this->dn, $this->query, implode(',', $this->options['filter']), $ldapError));
                }

                $this->results[] = $search;
                $itemsLeft -= min($itemsLeft, $pageSize);

                if (0 !== $maxItems && 0 === $itemsLeft) {
                    break;
                }
            } while (null !== $cookie && '' !== $cookie);
        }

        return new CollectionMock($this->connection, $this);
    }

    /**
     * Returns a LDAP search resource. If this query resulted in multiple searches, only the first
     * page will be returned.
     *
     * @return resource
     *
     * @internal
     */
    public function getResource($idx = 0)
    {
        if (null === $this->results || $idx >= \count($this->results)) {
            return null;
        }

        return $this->results[$idx];
    }

    /**
     * Returns all LDAP search resources.
     *
     * @return array
     *
     * @internal
     */
    public function getResources(): array
    {
        return $this->results;
    }

    /**
     * Calls actual LDAP search function with the prepared options and parameters.
     *
     * @param resource $con
     *
     * @return array
     */
    private function callSearchFunction($con, string $func, int $sizeLimit)
    {
        // TODO Define expected responses for tests
        switch ($func) {
            case 'ldap_read':
                # code...
                //$ret = new Entry('uid=john.doe,ou=people,ou=example,ou=com');
                $ret = array(
                    'dn'=>'uid=john.doe,ou=people,ou=example,ou=com',
                    'attributes'=> array(
                        'uid'=>['john.doe']
                    )
                );
                break;

            case 'ldap_list':
                # code...
                $ret = array(
                    array(
                        'dn'=>'uid=john.doe,ou=people,ou=example,ou=com',
                        'attributes'=> array(
                            'uid'=>['john.doe']
                        )
                    )
                );
                break;

            case 'ldap_search':
                # code...
                $ret = array(
                    array(
                        'dn'=>'uid=john.doe,ou=people,ou=example,ou=com',
                        'attributes'=> array(
                            'uid'=>['john.doe']
                        )
                    )
                );
                break;

            default:
                $ret = null;
                break;
        }

        return $ret;
    }
}
