<?php

namespace App\Services;

use App\Core\App;
use App\Core\DB;
use App\Core\Exceptions\ForbiddenException;
use App\Core\Exceptions\WrongRequestException;
use App\Core\Middleware\Logger;
use App\Core\Middleware\Mailer;
use Exception;
use Ramsey\Uuid\Uuid;

class AccessService extends BaseService
{
    public function loginUser(array $params): void
    {
        $result = DB::getInstance()->selectRowBy('users', ['user_id', 'role'], $params);
        if (!$result) {
            throw new ForbiddenException('Неверно указан логин/пароль');
        }
        $token = Uuid::uuid4();
        DB::getInstance()->updateRows('users', ['auth_token' => $token], ['user_id' => $result['user_id']]);
        setcookie('AuthToken', $token);
    }

    public function logoutUser(array $params): void
    {
        setcookie('AuthToken', '', time() - 3600);
        DB::getInstance()->updateRows('users', ['auth_token' => 'null'], ['user_id' => App::$userID]);
    }

    public function resetPassword(array $params): void
    {
        $email = $params[0];
        $resetToken = hash('sha256', $email . time());
        if (DB::getInstance()->updateRows('users', ['reset_token' => $resetToken], ['login' => $email]) == 0) {
            throw new WrongRequestException("E-mail $email не найден в списке пользователей");
        }
        $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/update_password?' . $resetToken;
        $mailer = new Mailer();
        $mailer->sendMail($email, 'Reset password', "Ссылка для сброса пароля: $url");
        $logger = new Logger();
        $logger->writeInfo("На адрес $email отправлена ссылка для сброса пароля");
    }

    public function updatePassword(array $params): void
    {
        $resetToken = $params[0];
        $result = DB::getInstance()->selectRowBy('users', ['login'], ['reset_token' => $resetToken]);
        if (!$result) {
            throw new WrongRequestException('Ссылка для изменения пароля недействительна');
        }
        $newPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
        if (DB::getInstance()->updateRows('users', ['password' => $newPassword, 'reset_token' => 'null'], ['reset_token' => $resetToken]) == 0) {
            throw new Exception('Ошибка изменения пароля');
        }
        $email = $result['login'];
        $mailer = new Mailer();
        $mailer->sendMail($email, 'New password', "Новый пароль: $newPassword");
        $logger = new Logger();
        $logger->writeInfo("На адрес $email отправлен новый пароль");
    }
}
