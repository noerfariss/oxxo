<?php

namespace App\Enums;

enum RolesEnum: string
{
        // roles
    case ADMIN = 'admin';
    case OWNER = 'owner';
    case SUPERADMIN = 'superadmin';

        // status
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case ACCEPTED = 'accepted';
}
