<?php

namespace ZFStarterMail\Model\Gateway;

use ZFS\DomainModel\Feature\FilterColumnsFeature;
use ZFS\DomainModel\Gateway\AbstractFactory;
use ZFS\DomainModel\Service\Options;

/**
 * Class GatewayAbstractFactory
 * @package ZFStarterMail\Model\Gateway
 */
class GatewayAbstractFactory extends AbstractFactory
{
    public function __construct()
    {
        $this->provides = array(
            'MailTemplatesGateway' => array(
                Options::OPTION_TABLE_NAME              => 'mail_templates',
                Options::OPTION_TABLE_FEATURES          => array(new FilterColumnsFeature()),
                Options::OPTION_OBJECT_PROTOTYPE        => 'ZFStarterMail\Model\MailTemplate',
            )
        );
    }
}
