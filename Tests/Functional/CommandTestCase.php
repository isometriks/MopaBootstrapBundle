<?php

namespace Mopa\Bundle\BootstrapBundle\Tests\Functional;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

abstract class CommandTestCase extends WebTestCase
{
    protected function executeConsole(Command $command, array $arguments = array(), array $options = array())
    {
        $command->setApplication(new Application($this->createClient()->getKernel()));

        if ($command instanceof \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand) {
            $command->setContainer($this->createClient()->getContainer());
        }

        $arguments = array_replace(array('command' => $command->getName()), $arguments);
        $options = array_replace(array('--env' => 'test'), $options);

        $commandTester = new CommandTester($command);
        $commandTester->execute($arguments, $options);

        return $commandTester->getDisplay();
    }
}