<?php

namespace ZFStarterMail\Model\Gateway;

use DomainObject\Feature\FilterColumnsFeature;
use DomainObject\Gateway\DomainObjectTableGatewayAbstractFactory;
use DomainObject\Gateway\DomainObjectTableGatewayAbstractFactory\Options;

/**
 * Class GatewayAbstractFactory
 * @package ZFStarterMail\Model\Gateway
 */
class GatewayAbstractFactory extends DomainObjectTableGatewayAbstractFactory
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
