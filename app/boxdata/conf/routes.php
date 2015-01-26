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

Route::get('/', function() {
    echo 'Hello world!';
});
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
