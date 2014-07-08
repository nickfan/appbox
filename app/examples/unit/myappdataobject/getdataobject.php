<?php
/**
 * Description
 *
 * @project appbox
 * @package 
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-07-02 16:58
 *
 */
require_once __DIR__ . '/../../../bootstrap/initenv.php';

use Nickfan\BoxApp\DataObject\UserDataObject;
use Nickfan\BoxApp\DataObject\AccountDataObject;

//$user = new UserDataObject();
//$user->id=123;
//$user->screen_name='foo';
//$user->crts = $user->upts = time();

$defProps = array(
    'id'=>123,
    'screen_name'=>'foo',
    'upts'=>time(),
    'crts'=>time(),
);
$user = UserDataObject::factory($defProps);

$userDict = $user->toArray();

var_dump($userDict);

$screen_name = $user['screen_name'];
var_dump($screen_name);

echo PHP_EOL;

foreach($user as $prop=>$val){
    echo 'prop:'.$prop."\t".var_export($val,TRUE).PHP_EOL;
}

$serializedStr = serialize($user);
$unserializedObj = unserialize($serializedStr);
var_dump($unserializedObj);

$account = new AccountDataObject();
$account->id=123;
$account['regip']='0.0.0.0';

var_dump($account);

var_dump($account->toArray());
