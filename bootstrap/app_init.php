<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2015-01-26 14:13
 *
 */


require_once __DIR__ . '/initenv.php';

$whoops = new \Whoops\Run;
//$whoops->allowQuit(false);
//$whoops->writeToOutput(false);
if($boxapp->runningInConsole()==true) {
    $whoops->pushHandler(new \Whoops\Handler\PlainTextHandler);
}else{
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
}
$whoops->register();
$boxapp->instance('whoops', $whoops);

$boxapp->instance('boxdispatcher',  \Nickfan\BoxApp\BoxDispatcher\DefaultBoxDispatcher::getInstance($boxapp));
$boxapp->bindShared('boxview',function($boxapp){
        return new \Nickfan\BoxApp\BoxView\BoxView($boxapp['path'].'/boxviews');
});
