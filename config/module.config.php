<?php

namespace BiblionumberSupport;

return [
    'controllers' => [
        'invokables' => [
            'BiblionumberSupport\Controller\Index' => Controller\IndexController::class,
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
