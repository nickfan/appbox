<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2015-01-21 16:28
 *
 */


require_once __DIR__ . '/../../bootstrap/initenv.php';

use Nickfan\AppBox\Support\Facades\AppBox;

$myexampleDict = array('abc','def','hig');
AppBox::debug($myexampleDict);
$confInst = AppBox::make('boxconf');
var_dump($confInst->get('app.timezone'));

BoxDict::set('abc.bbc',array('test'=>'valtest','test2'=>23));
var_dump(BoxDict::get('abc.bbc'));
BoxDict::cleanup();
var_dump(BoxDict::get('abc.bbc'));
