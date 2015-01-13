<?php

namespace Mopa\Bundle\BootstrapBundle\Tests\Functional;

use Symfony\Bundle\AsseticBundle\Command\DumpCommand;

class AsseticCommandTest extends CommandTestCase
{
    public function testAsseticCommand()
    {
        $output = $this->executeConsole(
            new DumpCommand()
        );

        var_dump($output);
    }
}