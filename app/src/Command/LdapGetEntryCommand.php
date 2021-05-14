<?php

namespace App\Command;

use App\Service\Ldap\Client;
use App\Command\BuildLdapConfig;
use RuntimeException;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\DTO\LdapEntryDTO;

class LdapGetEntryCommand extends Command
{
    protected static $defaultName = 'app:ldap:get-entry';

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
            ->setDescription('Search LDAP Entries')
            ->setHelp('Search LDAP entries using a query.')
            ->addArgument(
                'entry',
                InputArgument::REQUIRED,
                'LDAP Entry DN. Must be a valid LDAP DN. Example: uid=john.doe,ou=people,ou=example,ou=com'
            )
            ->addArgument(
                'query',
                InputArgument::OPTIONAL,
                'LDAP Search query. Must be a valid LDAP search query. Example: (description=Human)',
                '(objectClass=*)'
            )
            ->addOption(
                'attr',
                null,
                InputOption::VALUE_REQUIRED,
                'Attributes to retrieve. Will retrieve all attributes if empty. Example: uid,sn,cn'
            )
            ->addOption(
                'format',
                null,
                InputOption::VALUE_REQUIRED,
                'Output format. Valid formats are: json,ldif',
                'json'
            );
        $this->configureLdapOptions($this);
    }

    /**
     * @return int
     *
     * @psalm-return 0|1
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);

        $entry = $input->getArgument('entry');
        $query = $input->getArgument('query');

        // Attributes to retrieve from LDAP
        $attributes = explode(',', trim($input->getOption('attr')));
        if (1 === count($attributes) && empty($attributes[0])) {
            $attributes = [];
        }
        $attributes = array_unique($attributes);

        $format = $input->getOption('format');

        // Retrieve LDAP config from input or env var
        $config = $this->returnConfig($input);

        $ldapClient = new Client($this->ldap, $config);
        $ldapClient->bind();

        // Search LDAP entries (with forced filtering of attributes)
        $options = [];
        $options['filter'] = $attributes;
        //array_unshift($options['filter'], 'dn');
        $ldapEntry = $ldapClient->get($query, $entry, $options);

        if (! empty($ldapEntry)) {
            // TODO Create a LdapEntry DTO for serialization from/to Entry (in particular jpegPhoto)
            $ldapEntry = LdapEntryDTO::serializeJpegPhoto($ldapEntry);

            // Manage output formats.
            $outputEntry = LdapEntryDTO::serializeEntry($ldapEntry, $format);
            $symfonyStyle->text($outputEntry);

            return 0;
        }

        $symfonyStyle->error('No matching LDAP entry was found.');
        return 1;
    }
}
