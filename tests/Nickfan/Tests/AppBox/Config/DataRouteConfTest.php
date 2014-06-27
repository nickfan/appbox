<?php
namespace Nickfan\Tests\AppBox\Config;

use Nickfan\AppBox\Config\DataRouteConf;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-06-27 at 10:11:32.
 */
class DataRouteConfTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var DataRouteConf
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
        $this->object = new DataRouteConf(null, $etcPath);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::getVersion
     * @todo   Implement testGetVersion().
     */
    public function testGetVersion() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::getRouteConfByScript
     * @todo   Implement testGetRouteConfByScript().
     */
    public function testGetRouteConfByScript() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::getRouteConfByRouteConfKeySet
     * @todo   Implement testGetRouteConfByRouteConfKeySet().
     */
    public function testGetRouteConfByRouteConfKeySet() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::getRouteConfKeySetByScript
     * @todo   Implement testGetRouteConfKeySetByScript().
     */
    public function testGetRouteConfKeySetByScript() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::getRouteScriptClosure
     * @todo   Implement testGetRouteScriptClosure().
     */
    public function testGetRouteScriptClosure() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::getRootConfTree
     * @todo   Implement testGetRootConfTree().
     */
    public function testGetRootConfTree() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::getRootInitConf
     * @todo   Implement testGetRootInitConf().
     */
    public function testGetRootInitConf() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::getRouteConfInit
     * @todo   Implement testGetRouteConfInit().
     */
    public function testGetRouteConfInit() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::getRouteConfSubset
     * @todo   Implement testGetRouteConfSubset().
     */
    public function testGetRouteConfSubset() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::getRouteConfSubtree
     * @todo   Implement testGetRouteConfSubtree().
     */
    public function testGetRouteConfSubtree() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::getIncludePath
     * @todo   Implement testGetIncludePath().
     */
    public function testGetIncludePath() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::setIncludePath
     * @todo   Implement testSetIncludePath().
     */
    public function testSetIncludePath() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::setUserCacheObject
     * @todo   Implement testSetUserCacheObject().
     */
    public function testSetUserCacheObject() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::cacheLoad
     * @todo   Implement testCacheLoad().
     */
    public function testCacheLoad() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::cacheDel
     * @todo   Implement testCacheDel().
     */
    public function testCacheDel() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::cacheSave
     * @todo   Implement testCacheSave().
     */
    public function testCacheSave() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::cacheFlush
     * @todo   Implement testCacheFlush().
     */
    public function testCacheFlush() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::getConf
     * @todo   Implement testGetConf().
     */
    public function testGetConf() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::setConf
     * @todo   Implement testSetConf().
     */
    public function testSetConf() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::hasConf
     * @todo   Implement testHasConf().
     */
    public function testHasConf() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::getScript
     * @todo   Implement testGetScript().
     */
    public function testGetScript() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::setScript
     * @todo   Implement testSetScript().
     */
    public function testSetScript() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::hasScript
     * @todo   Implement testHasScript().
     */
    public function testHasScript() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::parseConfKey
     * @todo   Implement testParseConfKey().
     */
    public function testParseConfKey() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::parseScriptKey
     * @todo   Implement testParseScriptKey().
     */
    public function testParseScriptKey() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::fileLoadConf
     * @todo   Implement testFileLoadConf().
     */
    public function testFileLoadConf() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::fileLoadScript
     * @todo   Implement testFileLoadScript().
     */
    public function testFileLoadScript() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::fileGetRequire
     * @todo   Implement testFileGetRequire().
     */
    public function testFileGetRequire() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::fileGetContent
     * @todo   Implement testFileGetContent().
     */
    public function testFileGetContent() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::getItemsConf
     * @todo   Implement testGetItemsConf().
     */
    public function testGetItemsConf() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::offsetExists
     * @todo   Implement testOffsetExists().
     */
    public function testOffsetExists() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::offsetGet
     * @todo   Implement testOffsetGet().
     */
    public function testOffsetGet() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::offsetSet
     * @todo   Implement testOffsetSet().
     */
    public function testOffsetSet() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Nickfan\AppBox\Config\DataRouteConf::offsetUnset
     * @todo   Implement testOffsetUnset().
     */
    public function testOffsetUnset() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
