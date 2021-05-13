<?php

namespace App\Command;

use App\Service\Ldap\Client;
use App\Command\BuildLdapConfig;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LdapCreateEntryCommand extends Command
{
    protected static $defaultName = 'app:ldap:create-entry';

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
            ->setDescription('Creates a LDAP Entry')
            ->setHelp('Create a new LDAP entry using a DN and attributes.')
            ->addArgument(
                'dn',
                InputArgument::REQUIRED,
                'LDAP entry Distinguished Name'
            )
            ->addArgument(
                'attr',
                InputArgument::REQUIRED,
                'LDAP entry attributes. Must be provided as a valid JSON string: {"uid":"john.doe","cn":"John DOE"}'
            )
            ->addOption(
                'jsonfile',
                null,
                InputOption::VALUE_OPTIONAL,
                'WIP Path to a JSON file containing the LDAP entry attributes. Must be a path to a valid JSON file'
            );
        $this->configureLdapOptions($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $distingName = $input->getArgument('dn');
        $attributes = $input->getArgument('attr');

        $symfonyStyle = new SymfonyStyle($input, $output);

        $jsonDecodeAttributes = json_decode($attributes, true);

        if (empty($jsonDecodeAttributes)) {
            $symfonyStyle->error('The Attribute argument is not a valid JSON.');
            return 1;
        }

        $config = $this->returnConfig($input);

        $ldapClient = new Client($this->ldap, $config);
        $ldapClient->bind();

        if ($ldapClient->create($distingName, $jsonDecodeAttributes)) {
            $symfonyStyle->success("Following LDAP entry was successfuly create: $distingName");
            return 0;
        }

        $symfonyStyle->error('An error occurred during creation of LDAP entry');
        return 1;
    }
}
