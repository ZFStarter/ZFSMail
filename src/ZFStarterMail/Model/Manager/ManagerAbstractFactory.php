<?php
namespace ZFStarterMail\Model\Manager;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ManagerAbstractFactory
 * @package ZFStarterMail\Model\Manager
 */
class ManagerAbstractFactory implements AbstractFactoryInterface
{
    protected $provides = array(
        'MailTemplatesManager' => 'ZFStarterMail\Model\Manager\MailTemplatesManager'
    );

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return array_key_exists($requestedName, $this->provides);
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return new $this->provides[$requestedName]();
    }
}
