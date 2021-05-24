<?php

namespace Es\CoreBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Es\CoreBundle\Mailer\MailerUtils;

/**
 * Commande servant à cloturer les objets trouvés au bout d'un an.
 * @author hroux
 * @since 09/09/2019
 */
class SendMailWarningPasswordExpiredCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'escore:security:send_mail_warning_password_expired';
    
    public function __construct(MailerUtils $mailerUtils)
    {
        parent::__construct();
        $this->mailerUtils = $mailerUtils;
    }

    protected function configure()
    {
        $this->setDescription('Closure OT event.');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Ok');
        $this->mailerUtils->sendMailWarningPasswordExpired();
        
        return Command::SUCCESS;
    }
}
