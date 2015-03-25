<?php

namespace Mopa\Bundle\BootstrapBundle\Tests\Functional;

use Symfony\Bundle\AsseticBundle\Command\DumpCommand;
use Symfony\Component\Filesystem\Filesystem;

class AsseticCommandTest extends CommandTestCase
{
    protected $compileDir;
    protected $filesystem;

    public function setUp()
    {
        parent::setUp();

        $this->compileDir = sys_get_temp_dir() . '/mopa_bootstrap';
        $this->filesystem = new Filesystem();
    }

    public function testAsseticCommand()
    {
        $output = $this->executeConsole(
            new DumpCommand(),
            array('write_to' => $this->compileDir)
        );

        echo $output;
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->filesystem->remove($this->compileDir);
    }
}