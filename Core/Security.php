<?php

namespace App\Core;

use App\Core\Exceptions\ForbiddenException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\WrongRequestException;
use App\Services\DirectoryService;

class Security {

    private string $role;

    public function checkAuthToken(): bool
    {
        if (!isset($_COOKIE['AuthToken'])) {
            return false;
        }
        $result = DB::getInstance()->selectRowBy('users', ['user_id', 'role'], ['auth_token' => $_COOKIE['AuthToken']]);
        if (!$result) {
            throw new ForbiddenException('Авторизационный токен не найден');
        }
        App::$userID = $result['user_id'];
        $this->role = $result['role'];
        return true;
    }

    public function checkAccessRights(string $actionName): void
    {
        if (!in_array($actionName, ['Migration::startMigration', 'Access::loginUser',
            'Access::resetPassword', 'Access::updatePassword']) && !$this->checkAuthToken())
        {
            throw new UnauthorizedException();
        }
        if (explode('::', $actionName)[0] == 'Admin' && $this->role != 'admin') {
            throw new ForbiddenException('Для доступа к данной функции необходимо иметь роль администратора');
        }
    }

    public function checkFileOwner(int $fileID): string
    {
        $result = DB::getInstance()->selectRowBy('files_list', ['owner_id', 'is_dir', 'file_name'], ['file_id' => $fileID]);
        if (!$result || $result['is_dir'] == 1) {
            throw new WrongRequestException('Неверно указан ID файла');
        }
        if ($result['owner_id'] != App::$userID) {
            throw new ForbiddenException('Нет прав доступа к указанному файлу');
        }
        return $result['file_name'];
    }

    public function checkDirectoryOwner(int $directoryID): void
    {
        if ($directoryID != DirectoryService::GLOBAL_ROOT_DIR) {
            $result = DB::getInstance()->selectRowBy('files_list', ['owner_id', 'is_dir'], ['file_id' => $directoryID]);
            if (!$result || $result['is_dir'] != 1) {
                throw new WrongRequestException('Неверно указан ID папки');
            }
            if ($result['owner_id'] != App::$userID) {
                throw new ForbiddenException('Нет прав доступа к указанной папке');
            }
        }
    }

    public function checkShareOwner(int $shareID): void
    {
        $result = DB::getInstance()->selectRowBy('files_list f join shares_list s on f.file_id = s.file_id',
            ['owner_id'], ['share_id' => $shareID]);
        if (!$result) {
            throw new WrongRequestException('Неверно указан ID совместного доступа');
        }
        if ($result['owner_id'] != App::$userID) {
            throw new ForbiddenException('Нет прав доступа');
        }
    }
}
