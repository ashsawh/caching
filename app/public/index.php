<?php
namespace Employees;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Predis\Client;

require '../../vendor/autoload.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$container = new \Slim\Container;

$container['settings']['db'] = [
    'driver' => 'mysql',
    'host' => '10.0.15.21',
    'database' => 'employees',
    'username' => 'employees_user',
    'password' => 'emppw',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
];

$container['settings']['cdb'] = [
    'host' => '10.0.15.22'
];

$container['settings']['displayErrorDetails'] = true;
$container['settings']['addContentLengthHeader'] = false;

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function ($container) use ($capsule) {
    return $capsule;
};

$container['cdb'] = function ($container) {
    return new Client($container['settings']['cdb']);
};

$container['cache'] = function () {
    return new \Slim\HttpCache\CacheProvider();
};

$container['view'] = new \Slim\Views\PhpRenderer("../views/");
$app = new \Slim\App($container);

$app->add(new \Slim\HttpCache\Cache('public', 3600));

$app->get('/employee', 'App\Controllers\Employees:index');

$app->group('/employee/{employeeId}', function () {
    $this->get('', 'App\Controllers\Employees:get');
    $this->put('', 'App\Controllers\Employees:store');
    $this->delete('', 'App\Controllers\Employees:delete');
    $this->patch('', 'App\Controllers\Employees:delete');
});

$app->group('/employee/{employeeId}/salaries/{salaryId}', function () {
    $this->get('', 'App\Controllers\Employees:getSalary');
    $this->put('', 'App\Controllers\Employees:storeSalary');
    $this->delete('', 'App\Controllers\Employees:deleteSalary');
    $this->patch('', 'App\Controllers\Employees:updateSalary');
});

$app->run();