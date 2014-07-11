<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-27 16:48
 *
 */


namespace Nickfan\BoxApp\Controller;

use Nickfan\BoxApp\Dispatcher\DispatcherInterface;

abstract class AbstractController {
    protected $dispatcher;

    public function __construct(DispatcherInterface $dispatcherInstance) {
        $this->dispatcher = $dispatcherInstance;
    }

    public function Index() {
    }
}