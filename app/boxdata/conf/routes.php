<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-16 14:41
 *
 */
use \NoahBuscher\Macaw\Macaw as Route;
use Nickfan\AppBox\Common\BoxConstants;
use Nickfan\BoxApp\Support\Facades\BoxDispatcher;
use Nickfan\BoxApp\Support\Facades\BoxView;
Route::get('/favicon.ico', function() {
    $content = base64_decode(BoxConstants::TRANSGIFDATA);
    $contentType = 'image/gif';
    header('Content-Type: '.$contentType);
    header('Content-Length: '.strlen($content));
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    echo $content;
    exit;
});

Route::get('/', function() {
    return BoxView::make('hello')->render();
});

Route::get('/hello', function() {
    echo 'Welcome to AppBox'.'<br>'.PHP_EOL;
    echo BoxDispatcher::getHost().'<br>'.PHP_EOL;
    echo BoxDispatcher::getDomain().'<br>'.PHP_EOL;
});

Route::get('/box', '\App\Boxcontrollers\Localhost\Index\Index@Index');
Route::get('/ping', '\App\Boxcontrollers\Localhost\Index\Index@Ping');
