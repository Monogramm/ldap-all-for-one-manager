<?php

namespace App\Command;

use App\Service\Ldap\Client;
use Psr\Log\LoggerInterface;
use App\Command\BuildLdapConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Ldap\Ldap;

class LdapLoginCommand extends Command
{
    protected static $defaultName = 'app:ldap:login';

    use BuildLdapConfig;

    /**
     * @var Ldap
     */
    private $ldap;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Ldap $ldap,
        LoggerInterface $logger
    ) {
        $this->ldap = $ldap;
        $this->logger = $logger;
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
            ->setDescription('Test login against current LDAP server')
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'LDAP User ID'
            )
            ->addArgument(
                'password',
                InputArgument::REQUIRED,
                'LDAP User Password'
            );
        $this->configureLdapOptions($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->comment("LDAP login:");

        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        // Checking input format
        $invalid = false;
        if (empty($username)) {
            $io->error('Username cannot be empty');
            $invalid = true;
        }
        if (empty($password)) {
            $io->error('Password cannot be empty');
            $invalid = true;
        }

        if ($invalid) {
            return 2;
        }

        $config = $this->returnConfig($input);

        $ldapClient = new Client($this->ldap, $config);

        // LDAP login
        try {
            $entry = $ldapClient->check($username, $password);
        } catch (\Throwable $e) {
            $this->logger->error($e);
            $io->error('Failed to check against LDAP. Error message: '.$e->getMessage());
            return 1;
        }

        if (!isset(
            $entry->getAttribute($config['mail_key'])[0],
            $entry->getAttribute($config['uid_key'])[0]
        )) {
            $io->error('Failed to authenticate user against LDAP.');
            return 1;
        }
        $io->success('LDAP login successful!');

        return 0;
    }
}
