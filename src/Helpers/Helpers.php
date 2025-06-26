<?php
declare(strict_types=1);

namespace App\Helpers;

final class Helpers
{
   public static function registerGettext(): void
   {
       spl_autoload_register(function ($class) {
           $prefix = 'Gettext\\';
           $base_dir = ROOT_PATH . '/vendor/gettext/';
           $len = strlen($prefix);
           if (strncmp($class, $prefix, $len) !== 0) {
               return;
           }
           $relative_class = substr($class, $len);
           $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
           if (file_exists($file)) {
               require $file;
           }
       });
   }
}
