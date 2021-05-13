<?php

namespace App\Tests\Command;

use App\Command\LdapCreateEntryCommand;
use App\Tests\Command\AbstractUnitTestLdap;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Ldap\Exception\LdapException;

class LdapCreateEntryCommandUnitTest extends AbstractUnitTestLdap
{

    public $fullDn = 'cn=Cubert Farnsworth,ou=people,dc=planetexpress,dc=com';
    public $attribute = '{"sn":["Farnsworth"],"objectClass":["inetOrgPerson"]}';

    public $dnWrong = 'cn=Cubert Farnsworthou=people,dc=planetexpress,dc=com';
    public $attributeWrong = '{sn":["Farnsworth"],"objectClass":["inetOrgPerson"]}';

    public function testExecute()
    {
        $this->buildLdapMock();

        $this->ldapConnectionMock->expects($this->exactly(0))
            ->method('isBound')
            ->willReturn(true);

        $this->ldapConnectionMock->expects($this->once())
            ->method('bind');

        $this->ldapAdapterMock->expects($this->once())
            ->method('getConnection')
            ->willReturn($this->ldapConnectionMock);

        $this->ldapAdapterMock->expects($this->once())
            ->method('getEntryManager')
            ->willReturn($this->ldapEntryManagerMock);

        $this->ldapEntryManagerMock->expects($this->any())
            ->method('add')
            ->willReturn(true);

        $ldap = new Ldap($this->ldapAdapterMock);

        $cmd = new LdapCreateEntryCommand(
            $ldap
        );

        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application();
        $application->add($cmd);

        $command = $application->find('app:ldap:create-entry');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'dn' => $this->fullDn,
            'attr' => $this->attribute
        ]);

        // the output of the command in the console
        $code = $commandTester->getStatusCode();
        $this->assertEquals(0, $code);
    }

    public function testExecuteWithoutAttribute()
    {
        $this->buildLdapMock();

        $this->ldapConnectionMock->expects($this->exactly(0))
            ->method('isBound')
            ->willReturn(true);

        $this->ldapConnectionMock->expects($this->never())
            ->method('bind');

        $this->ldapAdapterMock->expects($this->never())
            ->method('getConnection')
            ->willReturn($this->ldapConnectionMock);

        $this->ldapAdapterMock->expects($this->never())
            ->method('getEntryManager')
            ->willReturn($this->ldapEntryManagerMock);

        $ldap = new Ldap($this->ldapAdapterMock);

        $cmd = new LdapCreateEntryCommand(
            $ldap
        );

        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application();
        $application->add($cmd);

        $this->expectException(RuntimeException::class);

        $command = $application->find('app:ldap:create-entry');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'dn' => $this->fullDn
        ]);
    }

    public function testExecuteWithoutDn()
    {
        $this->buildLdapMock();

        $this->ldapConnectionMock->expects($this->exactly(0))
            ->method('isBound')
            ->willReturn(true);

        $this->ldapConnectionMock->expects($this->never())
            ->method('bind');

        $this->ldapAdapterMock->expects($this->never())
            ->method('getConnection')
            ->willReturn($this->ldapConnectionMock);

        $this->ldapAdapterMock->expects($this->never())
            ->method('getEntryManager')
            ->willReturn($this->ldapEntryManagerMock);

        $ldap = new Ldap($this->ldapAdapterMock);

        $cmd = new LdapCreateEntryCommand(
            $ldap
        );

        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application();
        $application->add($cmd);

        $this->expectException(RuntimeException::class);

        $command = $application->find('app:ldap:create-entry');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'attr' => $this->attribute
        ]);
    }

    public function testExecuteBadAttributeFormat()
    {
        $this->buildLdapMock();

        $this->ldapConnectionMock->expects($this->exactly(0))
            ->method('isBound')
            ->willReturn(true);

        $this->ldapConnectionMock->expects($this->never())
            ->method('bind');

        $this->ldapAdapterMock->expects($this->never())
            ->method('getConnection')
            ->willReturn($this->ldapConnectionMock);

        $this->ldapAdapterMock->expects($this->never())
            ->method('getEntryManager')
            ->willReturn($this->ldapEntryManagerMock);

        $ldap = new Ldap($this->ldapAdapterMock);

        $cmd = new LdapCreateEntryCommand(
            $ldap
        );

        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application();
        $application->add($cmd);

        $command = $application->find('app:ldap:create-entry');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'dn' => $this->fullDn,
            'attr' => $this->attributeWrong
        ]);

        // the output of the command in the console
        $code = $commandTester->getStatusCode();
        $this->assertEquals(1, $code);
    }

    public function testExecuteBadDnFormat()
    {
        $this->buildLdapMock();

        $this->ldapConnectionMock->expects($this->exactly(0))
            ->method('isBound')
            ->willReturn(true);

        $this->ldapConnectionMock->expects($this->once())
            ->method('bind');

        $this->ldapAdapterMock->expects($this->once())
            ->method('getConnection')
            ->willReturn($this->ldapConnectionMock);

        $this->ldapAdapterMock->expects($this->once())
            ->method('getEntryManager')
            ->willReturn($this->ldapEntryManagerMock);

        $this->ldapEntryManagerMock->expects($this->any())
            ->method('add')
            ->will($this->throwException(new LdapException));

        $ldap = new Ldap($this->ldapAdapterMock);

        $cmd = new LdapCreateEntryCommand(
            $ldap
        );

        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application();
        $application->add($cmd);

        $this->expectException(LdapException::class);

        $command = $application->find('app:ldap:create-entry');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'dn' => $this->dnWrong,
            'attr' => $this->attribute
        ]);
    }

    public function testExecuteBadDnAndAttributeFormat()
    {
        $this->buildLdapMock();

        $this->ldapConnectionMock->expects($this->exactly(0))
            ->method('isBound')
            ->willReturn(true);

        $this->ldapConnectionMock->expects($this->never())
            ->method('bind');

        $this->ldapAdapterMock->expects($this->never())
            ->method('getConnection')
            ->willReturn($this->ldapConnectionMock);

        $this->ldapAdapterMock->expects($this->never())
            ->method('getEntryManager')
            ->willReturn($this->ldapEntryManagerMock);

        $ldap = new Ldap($this->ldapAdapterMock);

        $cmd = new LdapCreateEntryCommand(
            $ldap
        );

        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application();
        $application->add($cmd);

        $command = $application->find('app:ldap:create-entry');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'dn' => $this->dnWrong,
            'attr' => $this->attributeWrong
        ]);

        // the output of the command in the console
        $code = $commandTester->getStatusCode();
        $this->assertEquals(1, $code);
    }
}
