<?php

declare(strict_types=1);

namespace App\Models;

class Permissions
{
    public const VIEW_PROJECT      = 'View Project';
    public const VIEW_ALL_PROJECTS = 'View All Projects';
    public const EDIT_PROJECT      = 'Edit Project';
    public const VIEW_WORK_ITEM    = 'View Work Item';
    public const EDIT_WORK_ITEM    = 'Edit Work Item';
    public const VIEW_TIME_ENTRY   = 'View Time Entry';
    public const EDIT_TIME_ENTRY   = 'Edit Time Entry';
    public const MANAGE_SYSTEM     = 'Manage System';
    public const RUN_REPORTS       = 'Run Reports';
    public const VEW_ORDERS        = 'View Orders';
    public const EDIT_ORDERS       = 'Edit Orders';

    public const LIST              = [
        self::VIEW_PROJECT,
        self::VIEW_ALL_PROJECTS,
        self::EDIT_PROJECT,
        self::VIEW_WORK_ITEM,
        self::EDIT_WORK_ITEM,
        self::VIEW_TIME_ENTRY,
        self::EDIT_TIME_ENTRY,
        self::MANAGE_SYSTEM,
        self::RUN_REPORTS,
        self::VEW_ORDERS,
        self::EDIT_ORDERS
    ];
}
