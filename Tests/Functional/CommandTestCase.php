<?php

namespace Mopa\Bundle\BootstrapBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\StreamOutput;

abstract class CommandTestCase extends WebTestCase
{
    protected function executeConsole(Command $command, array $arguments = array(), array $options = array())
    {
        $command->setApplication(new Application($this->createClient()->getKernel()));

        if ($command instanceof ContainerAwareCommand) {
            $command->setContainer($this->createClient()->getContainer());
        }

        $definition = $command->getDefinition();
        $definition->addOption(new InputOption('env'));

        $input = new ArrayInput($arguments, $definition);
        $output = new StreamOutput(fopen('php://memory', 'w', false));

        $command->run($input, $output);

        rewind($output->getStream());

        $display = stream_get_contents($output->getStream());

        return $display;
    }
}