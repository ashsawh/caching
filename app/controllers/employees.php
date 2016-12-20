<?php

namespace App\Controllers;

use App\Library\AController;
use App\Models\Employee;

class Employees extends AController {
    protected $filterList = array(
        'first_name',
        'last_name',
        'gender',
        'hire_date',
        'birth_date',
    );

    public function get($req, $res, $args)
    {
        $redisKey = "Employee:" . (int)$args['employeeId'];
        if ($this->cache->exists($redisKey)) {
            $employee = $this->cache->hgetall($redisKey);
        } else {
            $employee = Employee::find((int)$args['employeeId']);
            $this->cache->hmset($redisKey, $employee->toArray());
        }
        return $res->withJson($employee);
    }

    public function index($req, $res, $args)
    {
        $this->parseQueryParams($req);
        $where = array_intersect_key($this->filters, array_flip($this->filterList));

        if (isset($this->filters)) {
            $newRes = $res
                ->withJson(
                    Employee::where($where)
                        ->offset($this->offSet)
                        ->limit($this->limit)
                        ->get($this->filterList)
                );
            return $this->ci->get('cache')->withEtag($newRes, crc32($req->getUri()->getQuery()));
        } else {

        }
    }

    public function getSalary($req, $res, $args)
    {
        $employeeId = rand(10001, 11000);
        $redisKey = "Employee:" . $employeeId;
        if ($this->cache->exists($redisKey)) {
            $employee = json_decode($this->cache->get($redisKey));
        } else {
            $employee = Employee::find($employeeId);
            $salaries = $employee->salaries;
            $departments = $employee->departments;
            $titles = $employee->titles;
            $this->cache->set($redisKey, $employee->toJson());
        }
        return $res->withJson($employee);
    }
}