<?php

namespace Singular\Tests\Unit;

use Singular\Application;
use Singular\ConfigLoader;

class ConfigLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Singular\Exception
     */
    public function testConfigDirNotFound()
    {
        $app = new Application(array('base_dir' => __DIR__."/../"));

        $app['base_dir'] = __DIR__;

        $config = new ConfigLoader($app);
        $config->loadConfigs();
    }
} 