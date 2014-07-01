<?php

namespace CSanquer\Silex\PdoServiceProvider\Tests\Config;

use CSanquer\Silex\PdoServiceProvider\Config\DBlibConfigTest;

/**
 * TestCase for DBlibConfig
 *
 * @author Cristian Pascottini <cristianp6@gmail.com>
 *
 */
class SqlSrvConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DBlibConfig
     */
    protected $pdoConfig;

    public function setUp()
    {
        $this->pdoConfig = new DBlibConfig();
    }

    /**
     * @dataProvider dataProviderPrepareParameters
     */
    public function testPrepareParameters($params, $expected)
    {
        $result = $this->pdoConfig->prepareParameters($params);
        $this->assertEquals($expected, $result);
    }
    
    public function dataProviderPrepareParameters()
    {
        return array(
            array(
                array(
                    'dbname' => 'fake-db',
                    'user' => 'fake-user',
                    'password' => 'fake-password',
                ),
                array(
                    'dsn' => 'dblib:port=1433;dbname=fake-db;server=localhost',
                    'user' => 'fake-user',
                    'password' => 'fake-password',
                    'options' => array(),
                    'attributes' => array(),
                ),
            ),
            array(
                array(
                    'host' => '127.0.0.1',
                    'port' => null,
                    'dbname' => 'fake-db',
                    'user' => 'fake-user',
                    'password' => 'fake-password',
                    'attributes' => array(),
                ),
                array(
                    'dsn' => 'dblib:dbname=fake-db;server=127.0.0.1',
                    'user' => 'fake-user',
                    'password' => 'fake-password',
                    'options' => array(),
                    'attributes' => array(),
                ),
            ),
        );
    }
}
