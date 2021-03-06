<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-27 16:31
 *
 */

namespace App\Boxcontrollers\Localhost\Favicon;

use Nickfan\BoxApp\BoxController\BoxAbstractController;
use Nickfan\AppBox\Common\BoxConstants;

class Ico extends BoxAbstractController {

    public function Index(){
        $content = base64_decode(BoxConstants::TRANSGIFDATA);
        $contentType = 'image/gif';
        header('Content-Type: '.$contentType);
        header('Content-Length: '.strlen($content));
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        echo $content;
        exit;
    }
}
