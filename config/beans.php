<?php

use Modifyljf\Admin\Contracts\BaseService;
use Modifyljf\Admin\Services\BaseServiceImpl;

return [
    BaseService::class => [
        'class' => BaseServiceImpl::class,
        'shared' => false,
        'singleton' => true,
    ],
];
