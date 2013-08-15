<?php

// Define path to application directory
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment, (I am only running this in development)
defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
            realpath(APPLICATION_PATH . '/../library'),
            get_include_path(),
        )));

/** Zend_Application */
require_once 'Zend/Application.php';


$application = new Zend_Application(
                APPLICATION_ENV,
                APPLICATION_PATH . '/configs/application.ini'
);

$cli = new \Symfony\Component\Console\Application(
                'AMC Command Line Interface for Maintenance',
                \Doctrine\Common\Version::VERSION
);

$bootstrap = $application->bootstrap()->getBootstrap();
$bootstrap->bootstrap('Doctrine');

// Retrieve Doctrine Container resource
//$container = $application->getBootstrap()->getResource('doctrine');

/*
  try {
  // Bootstrapping Console HelperSet
  $helperSet = array();

  if (($dbal = $container->getConnection(getenv('CONN') ? : $container->defaultConnection)) !== null) {
  $helperSet['db'] = new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($dbal);
  }

  if (($em = $container->getEntityManager(getenv('EM') ? : $container->defaultEntityManager)) !== null) {
  $helperSet['em'] = new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em);
  }
  } catch (\Exception $e) {
  $cli->renderException($e, new \Symfony\Component\Console\Output\ConsoleOutput());
  }
 */

use \Symfony\Component\Console as Console;
use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

class AMC_Console extends Command {

    protected $em;

    protected function configure() {
        $this
                ->setName('Migrate:task')
                ->setDescription('Migration Tasks')
                ->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to greet?')
                ->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output) {
        
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $name = $input->getArgument('name');
//        if ($name) {
//            $text = 'Hello ' . $name;
//        } else {
//            $text = 'Hello';
//        }
//
//        if ($input->getOption('yell')) {
//            $text = strtoupper($text);
//        }
//
//        $output->writeln($text);
        switch (strtolower($name)) {
            case "invoice-lineitems":
                $output->writeln("Invoice Line Items");
                break;
            default:
                break;
        }
    }

}

$cli->add(new AMC_Console('Migrate:task'));

$cli->run();