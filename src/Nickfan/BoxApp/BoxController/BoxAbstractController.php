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


namespace Nickfan\BoxApp\BoxController;

use Nickfan\BoxApp\BoxDispatcher\BoxDispatcherInterface;

abstract class BoxAbstractController {
    protected $dispatcher;

    public function __construct(BoxDispatcherInterface $dispatcherInstance) {
        $this->dispatcher = $dispatcherInstance;
    }

    public function Index() {
    }
}