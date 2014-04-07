<?php

namespace ZFStarterMail\Model\Gateway;

use DomainModel\Feature\FilterColumnsFeature;
use DomainModel\Gateway\DomainModelTableGatewayAbstractFactory;
use DomainModel\Gateway\DomainModelTableGatewayAbstractFactory\Options;

/**
 * Class GatewayAbstractFactory
 * @package ZFStarterMail\Model\Gateway
 */
class GatewayAbstractFactory extends DomainModelTableGatewayAbstractFactory
{
    public function __construct()
    {
        $this->provides = array(
            'MailTemplatesGateway' => array(
                Options::OPTION_TABLE_NAME              => 'mail_templates',
                Options::OPTION_TABLE_FEATURES          => array(new FilterColumnsFeature()),
                Options::OPTION_DOMAIN_OBJECT_PROTOTYPE => 'ZFStarterMail\Model\MailTemplate',
            )
        );
    }
}
