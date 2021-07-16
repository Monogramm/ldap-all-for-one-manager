<?php

namespace App\Tests\Command;

use App\Command\LdapLoginCommand;
use Monolog\Logger;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Ldap;

class LdapLoginCommandUnitTest extends AbstractUnitTestLdap
{
    public function testExecute()
    {
        $baseDn = 'ou=people,dc=ldap,dc=example,dc=com';
        $username = 'firstname.lastname';
        $email = 'firstname.lastname@yopmail.com';
        $password = 'S&cur3P@ssW0rd';

        $ldapEntry = new Entry(
            "uid=$username,$baseDn",
            [
                'uid' => [$username],
                'mail' => [$email],
            ]
        );

        $this->buildLdapMock();

        $this->ldapQueryMock->expects($this->any())
            ->method('execute')
            ->willReturn([
                $ldapEntry,
            ]);

        $this->ldapConnectionMock->expects($this->exactly(0))
            ->method('isBound')
            ->willReturn(true);

        $this->ldapConnectionMock->expects($this->any())
            ->method('bind');

        $this->ldapAdapterMock->expects($this->any())
            ->method('getConnection')
            ->willReturn($this->ldapConnectionMock);

        $this->ldapAdapterMock->expects($this->any())
            ->method('createQuery')
            ->willReturn($this->ldapQueryMock);

            $this->ldapAdapterMock->expects($this->any())
            ->method('escape')
            ->willReturn($username);

        $ldap = new Ldap($this->ldapAdapterMock);

        $logger = $this->createMock(Logger::class);

        $cmd = new LdapLoginCommand(
            $ldap,
            $logger
        );

        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application();
        $application->add($cmd);

        $command = $application->find('app:ldap:login');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'username' => $username,
            'password' => $password,
            '--uid-key' => 'uid',
            '--mail-key' => 'mail',
            '--base-dn' => $baseDn,
            '--query' => '(objectClass=inetOrgPerson)',
            //'--search-dn' => "cn=admin,$baseDn",
            //'--search-password' => 'password',
        ]);

        $code = $commandTester->getStatusCode();

        $this->assertEquals(0, $code);
    }
}
