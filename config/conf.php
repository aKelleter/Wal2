<?php
declare(strict_types=1);

const APP_NAME = "WaL-2";
const APP_VER = "1.0.1-25062025-1715";
const BASE_URL = '/prj/Wal2';
const DEFAULT_MODULE = 'default';

const APP_MULTI_LANG = true;
const APP_DEFAULT_LANG = 'fr_BE';

define('ENV', 'dev'); // ou 'prod' (production) ou 'acc' (acceptance)
define('IS_PROD', ENV === 'prod');
define('IS_DEV', ENV === 'dev');
define('IS_ACC', ENV === 'acc');

define('DEBUG', IS_DEV || IS_ACC);

define('LOG_PATHFILE', ROOT_PATH . '/logs/' . ENV . '-' . date('Y-m-d') . '.log');
define('CACHE_PATH', ROOT_PATH . '/cache/' . ENV);
