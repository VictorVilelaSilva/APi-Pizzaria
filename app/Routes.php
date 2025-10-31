<?php

namespace App;

use Config\Services;

$routes = Services::routes();

// Auto-discovery de rotas dos módulos
$modulesPath = APPPATH . 'Modules';
if (is_dir($modulesPath)) {
    $modules = array_diff(scandir($modulesPath), ['.', '..']);
    foreach ($modules as $module) {
        $routesFile = $modulesPath . '/' . $module . '/Routes.php';
        if (is_file($routesFile)) {
            require_once $routesFile;
        }
    }
}
