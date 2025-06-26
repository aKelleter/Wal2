<?php
declare(strict_types=1);

use App\Security\AccessControl;
use App\Module\Adm\AdmController;

AccessControl::requireLogin();
AdmController::doc(); 