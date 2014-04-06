<?php
namespace ZFStarterMail\Model\Manager;

use Common\ServiceManager\AbstractServiceAbstractFactory;

/**
 * Class ManagerAbstractFactory
 * @package ZFStarterMail\Model\Manager
 */
class ManagerAbstractFactory extends AbstractServiceAbstractFactory
{
    protected $provides = array(
        'MailTemplatesManager' => 'ZFStarterMail\Model\Manager\MailTemplatesManager'
    );
}
