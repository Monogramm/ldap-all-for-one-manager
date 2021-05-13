<?php

namespace App\Tests\Command;

use App\Command\LdapGetEntryCommand;
use App\Tests\Command\AbstractUnitTestLdap;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\Exception\LdapException;
use Symfony\Component\Console\Exception\RuntimeException;

class LdapGetEntryCommandUnitTest extends AbstractUnitTestLdap
{

    public $baseDn = 'ou=people,dc=planetexpress,dc=com';
    public $query = '(objectClass=inetOrgPerson)';
    public $attributes = 'uid,mail,sn,description';

    public $userId = ['professor'];
    public $commonName = 'cn=Hubert J. Farnsworth';
    public $surname = ['Farnsworth'];
    public $email = ["professor@planetexpress.com", "hubert@planetexpress.com"];
    public $description = ['Human'];

    public function testExecute()
    {
        $this->buildLdapMock();

        $this->ldapConnectionMock->expects($this->exactly(0))
            ->method('isBound')
            ->willReturn(true);

        $this->ldapQueryMock->expects($this->any())
            ->method('execute')
            ->willReturn([
                new Entry(
                    "dn=$this->commonName,$this->baseDn",
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

        $ldap = new Ldap($this->ldapAdapterMock);

        $cmd = new LdapGetEntryCommand(
            $ldap
        );

        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application();
        $application->add($cmd);

        $command = $application->find('app:ldap:get-entry');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'entry' => "$this->commonName,$this->baseDn",
            'query' => $this->query,
            '--attr' => $this->attributes
        ]);

        // the output of the command in the console
        $code = $commandTester->getStatusCode();

        $this->assertEquals(0, $code);
    }

    public function testExecuteLdif()
    {
        $this->buildLdapMock();

        $this->ldapConnectionMock->expects($this->exactly(0))
            ->method('isBound')
            ->willReturn(true);

        $this->ldapQueryMock->expects($this->any())
            ->method('execute')
            ->willReturn([
                new Entry(
                    "dn=$this->commonName,$this->baseDn",
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

        $ldap = new Ldap($this->ldapAdapterMock);

        $cmd = new LdapGetEntryCommand(
            $ldap
        );

        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application();
        $application->add($cmd);

        $command = $application->find('app:ldap:get-entry');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'entry' => "$this->commonName,$this->baseDn",
            'query' => $this->query,
            '--attr' => $this->attributes,
            '--format' => 'ldif'
        ]);

        // the output of the command in the console
        $code = $commandTester->getStatusCode();

        $this->assertEquals(0, $code);
    }

    public function testExecuteWithBadEntry()
    {
        $this->buildLdapMock();

        $this->ldapConnectionMock->expects($this->exactly(0))
            ->method('isBound')
            ->willReturn(true);

        $this->ldapQueryMock->expects($this->any())
            ->method('execute')
            ->will($this->throwException(new LdapException));

        $this->ldapConnectionMock->expects($this->once())
            ->method('bind');

        $this->ldapAdapterMock->expects($this->once())
            ->method('getConnection')
            ->willReturn($this->ldapConnectionMock);

        $this->ldapAdapterMock->expects($this->once())
            ->method('createQuery')
            ->willReturn($this->ldapQueryMock);

        $ldap = new Ldap($this->ldapAdapterMock);

        $cmd = new LdapGetEntryCommand(
            $ldap
        );

        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application();
        $application->add($cmd);

        $this->expectException(LdapException::class);

        $command = $application->find('app:ldap:get-entry');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'entry' => "wrong=test,$this->baseDn",
            '--attr' => $this->attributes
        ]);
    }
}
