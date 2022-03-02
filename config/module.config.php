<?php

namespace BiblionumberSupport;

return [
    'controllers' => [
        'factories' => [
            'BiblionumberSupport\Controller\Index' => Service\Controller\IndexControllerFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'site' => [
                'child_routes' => [
                    'get-biblio' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/get-biblio/:biblionumber',
                            'defaults' => [
                                '__NAMESPACE__' => 'BiblionumberSupport\Controller',
                                'controller' => 'Index',
                                'action' => 'redirect',
                            ],
                            // 'constraints' => [
                            //     'biblionumber' => '\d+',
                            // ],
                            'may_terminate' => true,
                        ],
                    ],
                ],
            ],
        ],
    ]
];
