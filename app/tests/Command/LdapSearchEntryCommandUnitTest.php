<?php

namespace App\Tests\Command;

use App\Command\LdapSearchEntryCommand;
use App\Tests\Command\AbstractUnitTestLdap;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\Exception\LdapException;
use Symfony\Component\Console\Exception\RuntimeException;

class LdapSearchEntryCommandUnitTest extends AbstractUnitTestLdap
{

    public $baseDn = 'ou=people,dc=planetexpress,dc=com';
    public $query = '(cn=Hubert J. Farnsworth)';
    public $attributes = 'uid,mail,sn,description';
    public $labels = 'Id,Email,Surname,Type';

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

        $cmd = new LdapSearchEntryCommand(
            $ldap
        );

        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application();
        $application->add($cmd);

        $command = $application->find('app:ldap:search-entries');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'base' => "dn=$this->commonName,$this->baseDn",
            'query' => $this->query,
            '--attr' => $this->attributes,
            '--labels' => $this->labels
        ]);

        // the output of the command in the console
        $code = $commandTester->getStatusCode();

        $this->assertEquals(0, $code);
    }

    public function testExecuteWithEmptyQuery()
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

        $cmd = new LdapSearchEntryCommand(
            $ldap
        );

        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application();
        $application->add($cmd);

        $this->expectException(LdapException::class);

        $command = $application->find('app:ldap:search-entries');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'base' => "dn=$this->commonName,$this->baseDn",
            'query' => '',
            '--attr' => $this->attributes,
            '--labels' => $this->labels
        ]);
    }

    public function testExecuteWithoutBaseDn()
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
                ),
                new Entry(
                    "dn=cn=Amy Wong+sn=Kroker,ou=people,dc=planetexpress,dc=com ",
                    [
                            'uid' => ['amy'],
                            'mail' =>['amy@planetexpress.com'],
                            'sn' => ['Kroker'],
                            'description' => ['Human']
                        ]
                ),
                new Entry(
                    "dn=cn=Bender Bending Rodríguez,ou=people,dc=planetexpress,dc=com",
                    [
                        'uid' => ['bender'],
                        'mail' => ['bender@planetexpress.com'],
                        'sn' => ['Rodríguez'],
                        'description' => ['Robot']
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

        $cmd = new LdapSearchEntryCommand(
            $ldap
        );

        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application();
        $application->add($cmd);

        $command = $application->find('app:ldap:search-entries');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'query' => $this->query,
            '--attr' => $this->attributes,
            '--labels' => $this->labels
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

        $this->ldapQueryMock->expects($this->any())
            ->method('execute')
            ->willReturn([
                new Entry(
                    "dn=$this->commonName,$this->baseDn",
                    [
                        'cn' => $this->commonName
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

        $cmd = new LdapSearchEntryCommand(
            $ldap
        );

        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application();
        $application->add($cmd);

        $command = $application->find('app:ldap:search-entries');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'base' => "dn=$this->commonName,$this->baseDn",
            'query' => $this->query,
            '--labels' => $this->labels
        ]);

        // the output of the command in the console
        $code = $commandTester->getStatusCode();
        $this->assertEquals(0, $code);
    }

    public function testExecuteWithoutLabels()
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

        $cmd = new LdapSearchEntryCommand(
            $ldap
        );

        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application();
        $application->add($cmd);

        $command = $application->find('app:ldap:search-entries');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'base' => "dn=$this->commonName,$this->baseDn",
            'query' => $this->query,
            '--attr' => $this->attributes
        ]);

        // the output of the command in the console
        $code = $commandTester->getStatusCode();
        $this->assertEquals(0, $code);
    }

    public function testExecuteWithoutAttributeAndLabel()
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
                    []
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

        $cmd = new LdapSearchEntryCommand(
            $ldap
        );

        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application();
        $application->add($cmd);

        $command = $application->find('app:ldap:search-entries');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'base' => "dn=$this->commonName,$this->baseDn",
            'query' => $this->query
        ]);

        // the output of the command in the console
        $code = $commandTester->getStatusCode();
        $this->assertEquals(0, $code);
    }

    public function testExecuteWithoutQuery()
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

        $cmd = new LdapSearchEntryCommand(
            $ldap
        );

        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application();
        $application->add($cmd);

        $command = $application->find('app:ldap:search-entries');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'base' => "dn=$this->commonName,$this->baseDn",
            '--attr' => $this->attributes,
            '--labels' => $this->labels
        ]);

        // the output of the command in the console
        $code = $commandTester->getStatusCode();
        $this->assertEquals(0, $code);
    }

    public function testExecuteNoLdapEntryFound()
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

        $cmd = new LdapSearchEntryCommand(
            $ldap
        );

        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application();
        $application->add($cmd);

        $this->expectException(LdapException::class);

        $command = $application->find('app:ldap:search-entries');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'base' => "dn=$this->commonName,$this->baseDn",
            'query' => "(cn=Test Test)",
            '--attr' => $this->attributes,
            '--labels' => $this->labels
        ]);
    }
}
