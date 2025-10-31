<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->set404Override('App\Controllers\ErrorsController::show404');

$directory = APPPATH . 'Modules';

$dirIterator = new DirectoryIterator($directory);
foreach ($dirIterator as $fileInfo) {
    if ($fileInfo->isDir() && !$fileInfo->isDot()) {
        require $fileInfo->getPathname() . DIRECTORY_SEPARATOR . 'Routes.php';
    }
}