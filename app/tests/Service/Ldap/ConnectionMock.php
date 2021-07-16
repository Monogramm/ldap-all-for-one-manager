<?php

namespace App\Tests\Service\Ldap;

use Symfony\Component\Ldap\Adapter\AbstractConnection;
use Symfony\Component\Ldap\Exception\LdapException;

/**
 * This will suppress all the PMD warnings in
 * this class.
 *
 * @SuppressWarnings(PHPMD)
 */
class ConnectionMock extends AbstractConnection
{
    private const LDAP_INVALID_CREDENTIALS = 0x31;
    private const LDAP_TIMEOUT = 0x55;
    private const LDAP_ALREADY_EXISTS = 0x44;

    /** @var bool */
    private $bound = false;

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
        $this->disconnect();
    }

    /**
     * {@inheritdoc}
     */
    public function isBound()
    {
        return $this->bound;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $password WARNING: When the LDAP server allows unauthenticated binds,
     * a blank $password will always be valid
     */
    public function bind($distinguishedNames = null, $password = null)
    {
        // TODO Define expected responses for tests
        $this->bound = true;
    }

    public function getResource()
    {
        return null;
    }

    public function setOption($name, $value)
    {
        // TODO Define expected responses for tests
        switch ($name) {
            case 'exception':
                throw new LdapException(sprintf('Could not set value "%s" for option "%s".', $value, $name));
                break;

            default:
                # code...
                break;
        }
    }

    public function getOption($name)
    {
        $ret = null;

        // TODO Define expected responses for tests
        switch ($name) {
            case 'exception':
                throw new LdapException(sprintf('Could not retrieve value for option "%s".', $name));
                break;

            default:
                $ret = null;
                break;
        }

        return $ret;
    }

    private function disconnect()
    {
        $this->bound = false;
    }
}
