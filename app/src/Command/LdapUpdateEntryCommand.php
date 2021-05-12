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

class LdapUpdateEntryCommand extends Command
{
    protected static $defaultName = 'app:ldap:update-entry';

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
            ->setDescription('Update a LDAP Entry')
            ->setHelp('Update an existing LDAP entry using a DN and attributes.')
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
            ->addArgument(
                'query',
                InputArgument::OPTIONAL,
                'LDAP Update query. Must be a valid LDAP search query. Example: (description=Human)',
                '(objectClass=*)'
            );
        $this->configureLdapOptions($this);
    }

    /**
     * @return int|null
     *
     * @psalm-return 0|1|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fullDn = $input->getArgument('dn');
        $attributes = $input->getArgument('attr');
        $query = $input->getArgument('query');
        $symfonyStyle = new SymfonyStyle($input, $output);

        $symfonyStyle->comment("update entry :");

        $jsonDecodeAttributes = json_decode($attributes, true);

        if (empty($jsonDecodeAttributes)) {
            $symfonyStyle->error('The attribute Option is not a valid JSON');
            return 1;
        }

        $config = $this->returnConfig($input);

        $ldapClient = new Client($this->ldap, $config);
        $ldapClient->bind();

        if ($ldapClient->update($fullDn, $query, $jsonDecodeAttributes)) {
            $symfonyStyle->success("Following LDAP entry was successfully updated: $fullDn");
            return 0;
        }
        $symfonyStyle->error("An error occurred during update of LDAP entry");
        return 1;
    }
}
