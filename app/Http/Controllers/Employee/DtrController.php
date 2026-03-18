<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\DtrController as BaseDtrController;

/**
 * Thin wrapper so employee routes can reference this class while
 * actual logic lives in App\Http\Controllers\DtrController.
 */
class DtrController extends BaseDtrController
{
    // Inherits exportMyCsForm48() and all other methods from the base controller.
}
