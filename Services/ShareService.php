<?php

namespace App\Services;

use App\Core\App;
use App\Core\DB;
use App\Core\Exceptions\ForbiddenException;
use App\Core\Exceptions\WrongRequestException;

class ShareService extends BaseService
{
    public function showShare(array $params): string
    {
        $fileID = $params['file_id'];
        $pageNum = $params['page_num'];
        App::$security->checkFileOwner((int)$fileID);
        $usersList = DB::getInstance()->selectRowsBy('shares_list join users on member_id = user_id',
            ['user_id', 'login', 'nickname'], ['file_id' => $fileID], $pageNum);
        return json_encode($usersList);
    }

    public function addShare(array $params): void
    {
        $fileID = $params['file_id'];
        $memberID = $params['member_id'];
        App::$security->checkFileOwner((int)$fileID);
        if ($memberID == App::$userID) {
            throw new ForbiddenException('Невозможно предоставить совместный доступ самому себе');
        }
        try {
            DB::getInstance()->insertRow('shares_list', $params);
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) {
                if (str_contains($e->getMessage(), 'check_file_member')) {
                    throw new WrongRequestException('Совместный доступ уже предоставлен');
                } else {
                    throw $e;
                }
            } else {
                throw $e;
            }
        }
    }

    public function deleteShare(array $params): void
    {
        $shareID = $params['share_id'];
        App::$security->checkShareOwner((int)$shareID);
        DB::getInstance()->deleteRows('shares_list', ['share_id' => $shareID]);
    }
}