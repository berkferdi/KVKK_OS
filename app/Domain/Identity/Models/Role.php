<?php

namespace App\Domain\Identity\Models;

use Spatie\Permission\Models\Role as SpatieRole;

/**
 * @property int|null $tenant_id
 */
class Role extends SpatieRole {}
