<?php
/**
 * User: naxel
 * Date: 02.04.14 19:08
 */

namespace ZFStarterMail\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Redactor extends AbstractHelper
{
    protected $options = array(
        'lang'         => 'en',
        'path'         => '/assets/js/redactor/', // w/o lang prefix
        'imageUpload'  => '/pages/images/upload', // url or false
        'imageGetJson' => '/pages/images/list',
        'fileUpload'   => '/admin/files/upload/',
        'fileDownload' => '/admin/files/download/?file=',
        'fileDelete'   => '/admin/files/delete/?file=',
    );

    /**
     * @param string $id
     * @param array  $options
     */
    public function redactor($id, $options = array())
    {
        $this->options = array_merge($this->options, $options);

        /** @var $view \Zend\View\Renderer\PhpRenderer */
        $view = $this->getView();
        $view->headLink()->prependStylesheet('/assets/js/redactor/redactor.css', 'screen', null, null);
        $view->headScript()->appendFile('/assets/js/redactor/redactor.js')
            ->appendScript(
                '(function($){$(function(){$("#' . $id . '").redactor(' . json_encode($options) . ');});})(jQuery)',
                'text/javascript',
                array('noescape' => true)
            );
    }
}
