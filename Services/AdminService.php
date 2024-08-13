<?php

namespace App\Services;

use App\Core\App;
use App\Core\DB;
use App\Core\Exceptions\WrongRequestException;
use PDOException;

class AdminService extends BaseService
{
    public function showUser(array $params): string
    {
        if (isset($params['user_id'])) {
            $result = DB::getInstance()->selectRowBy('users', ['login', 'password', 'role', 'nickname', 'phone'], $params);
            if (!$result) {
                throw new WrongRequestException('Пользователь с указанным ID не найден');
            }
            return json_encode($result);
        } else {
            return json_encode(DB::getInstance()->selectAllRows('users',
                ['user_id', 'login', 'password', 'role', 'nickname', 'phone'], (int)$params['page_num']));
        }
    }

    public function addUser(array $params): void
    {
        if (!filter_var($params['login'], FILTER_VALIDATE_EMAIL)) {
            throw new WrongRequestException('В качестве логина необходимо указать e-mail');
        }
        try {
            DB::getInstance()->insertRow('users', $params);
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new WrongRequestException('Пользователь уже существует');
            } else {
                throw $e;
            }
        }
    }

    public function editUser(array $params): void
    {
        $userID = $params['user_id'];
        unset($params['user_id']);
        if (DB::getInstance()->updateRows('users', $params, ['user_id' => $userID]) == 0) {
            throw new WrongRequestException('Пользователь с указанным ID не найден');
        };
    }

    public function deleteUser(array $params): void
    {
        $userID = $params['user_id'];
        if ($userID == App::$userID) {
            throw new WrongRequestException('Невозможно удалить свою учётную запись');
        }
        if (DB::getInstance()->deleteRows('users', ['user_id' => $userID]) == 0) {
            throw new WrongRequestException('Пользователь с указанным ID не найден');
        };
    }
}
