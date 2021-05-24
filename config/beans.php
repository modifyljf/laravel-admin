<?php

use Guesl\Admin\Contracts\BaseService;
use Guesl\Admin\Services\BaseServiceImpl;

return [
    BaseService::class => [
        'class' => BaseServiceImpl::class,
        'shared' => false,
        'singleton' => true,
    ],
];
