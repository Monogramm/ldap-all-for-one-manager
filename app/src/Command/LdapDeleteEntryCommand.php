<?php

namespace App\Command;

use App\Service\Ldap\Client;
use App\Command\BuildLdapConfig;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LdapDeleteEntryCommand extends Command
{
    protected static $defaultName = 'app:ldap:delete-entry';

    use BuildLdapConfig;

    /**
     * @var Ldap
     */
    private $ldap;

    public function __construct(
        Ldap $ldap
    ) {
        $this->ldap = $ldap;
        parent::__construct(self::$defaultName);
    }

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setDescription('Delete a LDAP Entry')
            ->setHelp('Delete an existing LDAP entry identified by DN.')
            ->addArgument(
                'dn',
                InputArgument::REQUIRED,
                'LDAP entry Distinguished Name'
            );
        $this->configureLdapOptions($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fullDn = $input->getArgument('dn');
        $symfonyStyle = new SymfonyStyle($input, $output);

        $config = $this->returnConfig($input);

        $ldapClient = new Client($this->ldap, $config);
        $ldapClient->bind();

        if ($ldapClient->delete($fullDn)) {
            $symfonyStyle->success("Following LDAP entry was successfully deleted: $fullDn");
            return 0;
        }

        $symfonyStyle->error('An error occurred during deletion of LDAP entry');
        return 1;
    }
}
