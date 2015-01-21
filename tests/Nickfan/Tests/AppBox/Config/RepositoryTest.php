<?php
namespace Nickfan\Tests\AppBox\Config;

use Nickfan\AppBox\Config\BoxRepository;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-06-27 at 10:18:52.
 */
class RepositoryTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Repository
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
        $this->object = new BoxRepository(null, $confPath);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
    }

    /**
     * @covers Nickfan\AppBox\Config\Repository::getVersion
     * @todo   Implement testGetVersion().
     */
    public function testGetVersion() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\Repository::getIncludePath
     * @todo   Implement testGetIncludePath().
     */
    public function testGetIncludePath() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\Repository::setIncludePath
     * @todo   Implement testSetIncludePath().
     */
    public function testSetIncludePath() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\Repository::setUserCacheObject
     * @todo   Implement testSetUserCacheObject().
     */
    public function testSetUserCacheObject() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\Repository::cacheLoad
     * @todo   Implement testCacheLoad().
     */
    public function testCacheLoad() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\Repository::cacheDel
     * @todo   Implement testCacheDel().
     */
    public function testCacheDel() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\Repository::cacheSave
     * @todo   Implement testCacheSave().
     */
    public function testCacheSave() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\Repository::cacheFlush
     * @todo   Implement testCacheFlush().
     */
    public function testCacheFlush() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\Repository::get
     * @todo   Implement testGet().
     */
    public function testGet() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\Repository::set
     * @todo   Implement testSet().
     */
    public function testSet() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\Repository::has
     * @todo   Implement testHas().
     */
    public function testHas() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\Repository::parseKey
     * @todo   Implement testParseKey().
     */
    public function testParseKey() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\Repository::fileLoad
     * @todo   Implement testFileLoad().
     */
    public function testFileLoad() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\Repository::fileGetRequire
     * @todo   Implement testFileGetRequire().
     */
    public function testFileGetRequire() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\Repository::fileGetContent
     * @todo   Implement testFileGetContent().
     */
    public function testFileGetContent() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\Repository::getItems
     * @todo   Implement testGetItems().
     */
    public function testGetItems() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\Repository::offsetExists
     * @todo   Implement testOffsetExists().
     */
    public function testOffsetExists() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\Repository::offsetGet
     * @todo   Implement testOffsetGet().
     */
    public function testOffsetGet() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\Repository::offsetSet
     * @todo   Implement testOffsetSet().
     */
    public function testOffsetSet() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\Repository::offsetUnset
     * @todo   Implement testOffsetUnset().
     */
    public function testOffsetUnset() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
