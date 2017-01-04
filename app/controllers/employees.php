<?php

namespace App\Controllers;

use App\Library\AController;
use App\Models\Employee;
use App\Models\Salary;

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

    private function getEmployee($employeeId, $cache = true)
    {
        $redisKey = "Employee:" . $employeeId;
        if ($cache === false) {
            echo "Cache is never accessed";
            $employee = Employee::find($employeeId);
            $salaries = $employee->salaries;
            $departments = $employee->departments;
            $titles = $employee->titles;
        } elseif ($this->cache->exists($redisKey)) {
            $employee = json_decode($this->cache->get($redisKey));
        } else {
            $employee = Employee::find($employeeId);
            $salaries = $employee->salaries;
            $departments = $employee->departments;
            $titles = $employee->titles;
            $this->cache->set($redisKey, $employee->toJson());
        }
        return $employee;
    }

    public function getSalary($req, $res, $args)
    {
        $employeeId = rand(10001, 11000);
        $employee = $this->getEmployee($employeeId);
        return $res->withJson($employee);
    }

    public function showProfile($req, $res, $args)
    {
        $employee = $this->getEmployee($args['employeeId']);
        $title = reset($employee->titles);

        return $this->ci->view->render($res, 'profile.phtml', [
            "employee" => $employee,
            "salaries" => $employee->salaries,
            "title" => $title->title,
            "departments" => $employee->departments
        ]);
    }

    public function showDetails($req, $res, $args)
    {
        $employee = $this->getEmployee(++$args['employeeId']);
        $title = reset($employee->titles);

        return $this->ci->view->render($res, 'info.phtml', [
            "employee" => $employee,
            "salaries" => $employee->salaries,
            "title" => $title->title,
            "departments" => $employee->departments
        ]);
    }

    public function showNoRedisProfile($req, $res, $args)
    {
        $employee = $this->getEmployee($args['employeeId'], false);
        $title = reset($employee->titles);

        return $this->ci->view->render($res, 'profile.phtml', [
            "employee" => $employee,
            "salaries" => $employee->salaries,
            "title" => $title->title,
            "departments" => $employee->departments
        ]);
    }

    public function showNoRedisDetails($req, $res, $args)
    {
        $employee = $this->getEmployee(++$args['employeeId'], false);
        $title = reset($employee->titles);

        return $this->ci->view->render($res, 'info.phtml', [
            "employee" => $employee,
            "salaries" => $employee->salaries,
            "title" => $title->title,
            "departments" => $employee->departments
        ]);
    }

    public function deleteSalary($req, $res, $args)
    {
        $employeeId = $args['employeeId'];
        $salary = $args['salaryId'];
        $dRes = Salary::where('emp_no', $employeeId)->where('salary', $salary)->first()->delete();
        $redisKey = "Employee:" . $employeeId;
        $cRes = $this->cache->del($redisKey);
        echo "Success";
    }

    public function showUncachedProfile($req, $res, $args)
    {
        $employee = $this->getEmployee($args['employeeId'], false);
        $title = reset($employee->titles);

        return $this->ci->view->render($res, 'uprofile.phtml', [
            "employee" => $employee,
            "salaries" => $employee->salaries,
            "title" => $title->title,
            "departments" => $employee->departments
        ]);
    }

    public function showRedisProfile($req, $res, $args)
    {
        $employee = $this->getEmployee($args['employeeId']);
        $title = reset($employee->titles);

        return $this->ci->view->render($res, 'uprofile.phtml', [
            "employee" => $employee,
            "salaries" => $employee->salaries,
            "title" => $title->title,
            "departments" => $employee->departments
        ]);
    }

    public function showVarnishProfile($req, $res, $args)
    {
        $employee = $this->getEmployee($args['employeeId']);
        $title = reset($employee->titles);

        return $this->ci->view->render($res, 'uprofile.phtml', [
            "employee" => $employee,
            "salaries" => $employee->salaries,
            "title" => $title->title,
            "departments" => $employee->departments
        ]);
    }
}