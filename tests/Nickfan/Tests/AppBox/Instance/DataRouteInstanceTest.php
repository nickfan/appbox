<?php

namespace Nickfan\Tests\AppBox\Instance;

use Nickfan\AppBox\Config\DataRouteConf;
use Nickfan\AppBox\Instance\DataRouteInstance;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-06-27 at 10:24:00.
 */
class DataRouteInstanceTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var DataRouteInstance
     */
    protected $object;

    protected $basePath = null;
    protected $confPath = null;
    protected $etcPath = null;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->basePath = $basePath = realpath(__DIR__ . '/../../../../../');
        $appPath = $basePath . '/app';
        $this->confPath = $confPath = $appPath . '/data/conf';
        $this->etcPath = $etcPath = $appPath . '/data/etc/local';
        $this->object = DataRouteInstance::getInstance(new DataRouteConf(null, $etcPath));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
    }

    /**
     * @covers Nickfan\AppBox\Instance\DataRouteInstance::getInstance
     * @todo   Implement testGetInstance().
     */
    public function testGetInstance() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Instance\DataRouteInstance::setShutDownHandler
     * @todo   Implement testSetShutDownHandler().
     */
    public function testSetShutDownHandler() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Instance\DataRouteInstance::getRouteInstance
     * @todo   Implement testGetRouteInstance().
     */
    public function testGetRouteInstance() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Instance\DataRouteInstance::getPoolInstanceRouteIdLabels
     * @todo   Implement testGetPoolInstanceRouteIdLabels().
     */
    public function testGetPoolInstanceRouteIdLabels() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Instance\DataRouteInstance::getPoolInstanceByRouteIdSet
     * @todo   Implement testGetPoolInstanceByRouteIdSet().
     */
    public function testGetPoolInstanceByRouteIdSet() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Instance\DataRouteInstance::close
     * @todo   Implement testClose().
     */
    public function testClose() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Instance\DataRouteInstance::__destruct
     * @todo   Implement test__destruct().
     */
    public function test__destruct() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
