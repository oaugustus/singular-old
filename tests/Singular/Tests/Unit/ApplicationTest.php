<?php

namespace Singular\Tests\Unit;

use Singular\Application;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $app = new Application(array('base_dir' => __DIR__."/../"));

        $this->assertInstanceOf('Singular\Application', $app);
    }

    /**
     * @expectedException \Singular\Exception
     */
    public function testInvalidBaseDir()
    {
        $app = new Application(array('base_dir' => __DIR__."abc"));
    }

    /**
     * @expectedException \Singular\Exception
     */
    public function testAppDirNotFound()
    {
        $app = new Application(array('base_dir' => __DIR__));
    }
} 