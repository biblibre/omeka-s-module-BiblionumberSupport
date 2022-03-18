<?php

namespace BiblionumberSupport;

return [
    'view_manager' => [
        'template_path_stack' => [
            dirname(__DIR__) . '/view',
        ],
    ],
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
                            'may_terminate' => true,
                        ],
                    ],
                ],
            ],
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => dirname(__DIR__) . '/language',
                'pattern' => '%s.mo',
                'text_domain' => null,
            ],
        ],
    ],
];
