<?php

declare(strict_types=1);

namespace App\Models;

class Roles
{
    public const CUSTOMER        = 'Customer';
    public const ENGINEER        = 'Engineer';
    public const PROJECT_MANAGER = 'Project Manager';
    public const ADMIN           = 'Admin';
    public const ADMIN_READ_ONLY = 'Admin Read Only';
    public const SUPER_ADMIN     = 'Super Admin';

    public const LIST            = [
        self::CUSTOMER,
        self::ENGINEER,
        self::PROJECT_MANAGER,
        self::ADMIN,
        self::ADMIN_READ_ONLY,
        self::SUPER_ADMIN
    ];

    public const CUSTOMER_PERMISSIONS = [
        Permissions::VIEW_PROJECT,
        Permissions::VIEW_WORK_ITEM,
        Permissions::VIEW_TIME_ENTRY,
        Permissions::RUN_REPORTS
    ];

    public const ENGINEER_PERMISSIONS = [
        ...self::CUSTOMER_PERMISSIONS,
        Permissions::EDIT_TIME_ENTRY
    ];

    public const PROJECT_MANAGER_PERMISSIONS = [
        ...self::ENGINEER_PERMISSIONS,
        Permissions::EDIT_PROJECT,
        Permissions::EDIT_WORK_ITEM,
        Permissions::VEW_ORDERS
    ];

    public const ADMIN_PERMISSION = [
        ...self::PROJECT_MANAGER_PERMISSIONS,
        Permissions::VIEW_ALL_PROJECTS,
        Permissions::EDIT_ORDERS
    ];

    public const ADMIN_READ_ONLY_PERMISSIONS = [
        Permissions::VIEW_PROJECT,
        Permissions::VIEW_ALL_PROJECTS,
        Permissions::VIEW_WORK_ITEM,
        Permissions::VIEW_TIME_ENTRY,
        Permissions::RUN_REPORTS
    ];

    public const SUPER_ADMIN_PERMISSIONS = [
        ...self::ADMIN_PERMISSION,
        Permissions::MANAGE_SYSTEM
    ];

    public const PERMISSIONS = [
        self::CUSTOMER        => self::CUSTOMER_PERMISSIONS,
        self::ENGINEER        => self::ENGINEER_PERMISSIONS,
        self::PROJECT_MANAGER => self::PROJECT_MANAGER_PERMISSIONS,
        self::ADMIN           => self::ADMIN_PERMISSION,
        self::ADMIN_READ_ONLY => self::ADMIN_READ_ONLY_PERMISSIONS,
        self::SUPER_ADMIN     => self::SUPER_ADMIN_PERMISSIONS
    ];
}
