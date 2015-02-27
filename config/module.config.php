<?php
return array(
    'mail' => array(
        'layout' => __DIR__ . '/../templates/zf-starter-mail/layout.phtml',
        'transport' => array(
            'host' => '127.0.0.1',
            'port' => '25'
        ),
        'defaultFrom' => array(
            'email' => 'zfstarter@nixsolutions.com',
            'name' => 'ZF Starter'
        ),
        'headers' => array(
            //'EXTERNAL' => true
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'redactor' => 'ZFStarterMail\View\Helper\Redactor'
        ),
    ),
    'rbac' => array(
        'mails_manager' => array(
            'permissions' => array(
                'mails_management.list',
                'mails_management.edit',
                'mails_management.create',
                'mails_management.remove',
            )
        ),
        //==========================================
        'admin' => array(
            'children' => array(
                'mails_manager'
            ),
        )
    ),
    'router' => array(
        'routes' => array(
            'admin_mail_templates_list_images' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/mails/images/list',
                    'defaults' => array(
                        'controller' => 'ZFStarterMail\Controller\Images',
                        'action' => 'list'
                    )
                ),
                'permissions' => 'mails_management.edit'
            ),
            'admin_mail_templates_upload_image' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/mails/images/upload',
                    'defaults' => array(
                        'controller' => 'ZFStarterMail\Controller\Images',
                        'action' => 'upload'
                    )
                ),
                'permissions' => 'mails_management.edit'
            ),
            'admin_mail_templates_list' => array(
                'layout' => 'layout/back',
                'type' => 'Segment',
                'options' => array(
                    'route' => '/admin/mails[/:page]',
                    'constraints' => array(
                        'page' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'ZFStarterMail\Controller\Admin',
                        'action' => 'index',
                        'page' => 1
                    )
                ),
                'permissions' => 'mails_management.list'
            ),
            'admin_mail_templates_create' => array(
                'layout' => 'layout/back',
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/mails/create',
                    'defaults' => array(
                        'controller' => 'ZFStarterMail\Controller\Admin',
                        'action' => 'create'
                    )
                ),
                'permissions' => 'mails_management.create'
            ),
            'admin_mail_templates_edit' => array(
                'layout' => 'layout/back',
                'type' => 'Segment',
                'options' => array(
                    'route' => '/admin/mails/edit/:id',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'ZFStarterMail\Controller\Admin',
                        'action' => 'edit'
                    )
                ),
                'permissions' => 'mails_management.edit'
            ),
            'admin_mail_templates_remove' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/admin/mails/remove/:id',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'ZFStarterMail\Controller\Admin',
                        'action' => 'remove'
                    )
                ),
                'permissions' => 'mails_management.remove'
            )
        ),
    ),
    'navigation' => array(
        'sidebar' => array(
            array(
                'name' => 'mails',
                'label' => 'Mail templates',
                'icon-class' => 'fa fa-envelope',
                'route' => 'admin_mail_templates_list',
                'pages' => array(
                    array(
                        'name' => 'mails.list',
                        'label' => 'List',
                        'icon-class' => 'fa fa-angle-double-right',
                        'route' => 'admin_mail_templates_list',
                        'permissions' => 'mails_management.list'
                    ),
                    array(
                        'name' => 'mails.create',
                        'label' => 'Create New',
                        'icon-class' => 'fa fa-angle-double-right',
                        'route' => 'admin_mail_templates_create',
                        'permissions' => 'mails_management.create'
                    )
                )
            )
        )
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            //'ZFStarterMail\Model\Manager\ManagerAbstractFactory',
            'ZFStarterMail\Model\Gateway\GatewayAbstractFactory',
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'ZFStarterMail\Controller\Admin' => 'ZFStarterMail\Controller\AdminController',
            'ZFStarterMail\Controller\Images' => 'ZFStarterMail\Controller\ImagesController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../templates',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);
