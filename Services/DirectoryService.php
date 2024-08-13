<?php

namespace App\Services;

use App\Core\App;
use App\Core\DB;
use App\Core\Exceptions\WrongRequestException;
use PDOException;

class DirectoryService extends BaseService
{
    const GLOBAL_ROOT_DIR = 1;
    const SHARED_TO_ME = 'shared_to_me';
    const SHARED_FROM_ME = 'shared_from_me';

    private function checkDirectoryName(string $directoryName): void
    {
        if (mb_strlen($directoryName) > 255 || preg_match('/[\/:*?"<>|\\\\]/', $directoryName)) {
            throw new WrongRequestException('Недопустимое имя папки');
        }
    }

    public function showDir(array $params): string
    {
        $directoryID = $params['directory_id'];
        $pageNum = (int)$params['page_num'];
        if ($directoryID == self::SHARED_TO_ME) {
            return json_encode(DB::getInstance()->selectRowsBy('files_list f join shares_list s on f.file_id = s.file_id',
                ['f.file_id', 'is_dir', 'original_file_name', 'loading_time'],
                ['member_id' => App::$userID],
                $pageNum));
        } else if ($directoryID == self::SHARED_FROM_ME) {
            return json_encode(DB::getInstance()->selectRowsBy('files_list f join shares_list s on f.file_id = s.file_id',
                ['f.file_id', 'original_file_name', 'loading_time'],
                ['owner_id' => App::$userID],
                $pageNum));
        } else {
            App::$security->checkDirectoryOwner((int)$directoryID);
            $filesList = DB::getInstance()->selectRowsBy('files_list',
                ['file_id', 'is_dir', 'original_file_name', 'loading_time'],
                ['parent_id' => $directoryID, 'owner_id' => App::$userID],
                $pageNum);
            if ($directoryID != self::GLOBAL_ROOT_DIR) {
                $result = DB::getInstance()->selectRowBy('files_list', ['parent_id'], ['file_id' => $directoryID]);
                $filesList[] = [
                    'file_id' => $result['parent_id'],
                    'is_dir' => 1,
                    'original_file_name' => '..',
                    'loading_time' => null
                ];
            }
            return json_encode($filesList);
        }
    }

    public function addDir(array $params): void
    {
        $parentID = $params['parent_id'];
        $directoryName = $params['directory_name'];
        App::$security->checkDirectoryOwner((int)$parentID);
        $this->checkDirectoryName($directoryName);
        try {
            DB::getInstance()->insertRow('files_list', ['parent_id' => $parentID,
                'is_dir' => 1,
                'owner_id' => App::$userID,
                'original_file_name' => $directoryName]);
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) {
                if (str_contains($e->getMessage(), 'check_file_duplicate')) {
                    throw new WrongRequestException('Папка уже существует');
                } else {
                    throw $e;
                }
            } else {
                throw $e;
            }
        }
    }

    public function editDir(array $params): void
    {
        $directoryID = $params['directory_id'];
        $directoryName = $params['directory_name'];
        if ($directoryID == self::GLOBAL_ROOT_DIR) {
            throw new WrongRequestException('Невозможно изменить корневую папку');
        }
        App::$security->checkDirectoryOwner((int)$directoryID);
        $this->checkDirectoryName($directoryName);
        try {
            DB::getInstance()->updateRows('files_list', ['original_file_name' => $directoryName], ['file_id' => $directoryID]);
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) {
                if (str_contains($e->getMessage(), 'check_file_duplicate')) {
                    throw new WrongRequestException('Папка уже существует');
                } else {
                    throw $e;
                }
            } else {
                throw $e;
            }
        }
    }

    public function deleteDir(array $params): void
    {
        $directoryID = $params['directory_id'];
        if ($directoryID == self::GLOBAL_ROOT_DIR) {
            throw new WrongRequestException('Невозможно удалить корневую папку');
        }
        App::$security->checkDirectoryOwner((int)$directoryID);
        try {
            DB::getInstance()->deleteRows('files_list', ['file_id' => $directoryID]);
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) {
                if (str_contains($e->getMessage(), 'check_parent')) {
                    throw new WrongRequestException('Папка должна быть пустой');
                } else {
                    throw $e;
                }
            } else {
                throw $e;
            }
        }
    }
}
