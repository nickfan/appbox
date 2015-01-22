<?php
/**
 * Description
 *
 * @project appbox
 * @package 
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-26 18:14
 *
 */



namespace Nickfan\AppBox\Tests;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-06-13 at 10:29:17.
 */
class MyAppTest extends \PHPUnit_Framework_TestCase {
    protected $object;

    protected $basePath = null;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->basePath = $basePath = realpath(__DIR__ . '/../');
        $this->object = new \stdClass();
        $this->object->version='1.0';
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
    }


    public function testBoxApp() {
        $this->assertEquals('1.0', $this->object->version);
    }

}
 