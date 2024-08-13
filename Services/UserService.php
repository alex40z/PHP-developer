<?php

namespace App\Services;

use App\Core\App;
use App\Core\DB;
use App\Core\Exceptions\WrongRequestException;

class UserService extends BaseService
{
    public function showUser(array $params): string
    {
        if (isset($params['user_id'])) {
            $result = DB::getInstance()->selectRowBy('users', ['user_id', 'nickname', 'phone'], $params);
            if (!$result) {
                throw new WrongRequestException('Пользователь с указанным ID не найден');
            }
            return json_encode($result);
        } else {
            return json_encode(DB::getInstance()->selectAllRows('users', ['user_id', 'nickname', 'phone'], (int)$params['page_num']));
        }
    }

    public function editUser(array $params): void
    {
        DB::getInstance()->updateRows('users', $params, ['user_id' => App::$userID]);
    }
}
