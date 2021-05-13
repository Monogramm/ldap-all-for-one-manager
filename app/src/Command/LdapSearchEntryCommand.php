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

class LdapSearchEntryCommand extends Command
{
    protected static $defaultName = 'app:ldap:search-entries';

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
                'base',
                InputArgument::OPTIONAL,
                'LDAP Base Search DN. Must be a valid LDAP DN. Example: ou=people,ou=example,ou=com'
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
                'Attributes to retrieve. DN will always be displayed. Example: uid,sn,cn'
            )
            ->addOption(
                'labels',
                null,
                InputOption::VALUE_OPTIONAL,
                'Labels to show. Example: Unique ID,Last Name,Full Name'
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
        $symfonyStyle->comment("List of entries:");

        $baseDn = $input->getArgument('base');
        $query = $input->getArgument('query');

        // Attributes to retrieve from LDAP
        $attributes = explode(',', trim($input->getOption('attr')));
        if (1 === count($attributes) && empty($attributes[0])) {
            $attributes = [];
        }
        $attributes = array_unique($attributes);

        // Attributes' labels
        $labels =  explode(',', $input->getOption('labels'));
        if (count($labels) !== count($attributes) || empty($labels[0])) {
            $labels = $attributes;
        }
        array_unshift($labels, 'dn');
        $labels = array_unique($labels);

        // Retrieve LDAP config from input or env var
        $config = $this->returnConfig($input);

        $ldapClient = new Client($this->ldap, $config);
        $ldapClient->bind();

        // Search LDAP entries (with forced filtering of attributes)
        $options = [];
        $options['filter'] = $attributes;
        array_unshift($options['filter'], 'dn');
        $ldapEntries = $ldapClient->search($query, $baseDn, $options);

        foreach ($ldapEntries as $key => $entry) {
            $entries[$key]['dn'] = $entry->getDn();

            foreach ($attributes as $attr) {
                $entries[$key][$attr] = ($entry->hasAttribute($attr) && !empty($entry->getAttribute($attr))) ?
                    json_encode($entry->getAttribute($attr)) : null;
            }
        }

        if (isset($entries)) {
            (new SymfonyStyle($input, $output))
                ->table($labels, $entries);

            return 0;
        }

        $symfonyStyle->error('No matching LDAP entry was found.');
        return 1;
    }
}
