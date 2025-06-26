<?php
declare(strict_types=1);

use App\Auth\Auth;

Auth::logout();
header('Location: ' . BASE_URL . '/login/index');
exit;

