<?php

namespace App\ORM;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Settings\Settings;
use App\ORM\BaseDataService;

class DataService extends BaseDataService {

    private $view;
    private $logger;

    public function __construct(Twig $view, LoggerInterface $logger) {
        parent::__construct($view, $logger);
        $this->view = $view;
        $this->logger = $logger;
    }

    public function getUsersData($id = 0) {
        return $this->__getData(Settings::USERS, $id);
    }

    public function getUserDataBySession($session = 0) {
        if (!$session) {
            return array();
        }
        $sdata = $this->__getData(Settings::USERSESSIONS, $session);
        if ($sdata && count($sdata) > 0) {
            return $this->getUsersData($sdata["user_id"]);
        }
        return array();
    }

    public function saveUsersData($data) {
        return $this->__saveDatafile(Settings::USERS, $data);
    }

    public function updateUserData($userdata) {
        if (!($userdata && $userdata["id"])) {
            return false;
        }
        $users = $this->getUsersData();
        $users[$userdata["id"]] = $userdata;
        $this->saveUsersData($users);

        return true;
    }

    public function getUsersSessions($id = 0) {
        return $this->__getData(Settings::USERSESSIONS, $id);
    }

    public function saveUsersSessions($data) {
        return $this->__saveDatafile(Settings::USERSESSIONS, $data);
    }

    public function getImgsData($id = 0) {
        return $this->__getData(Settings::IMGSLIST, $id);
    }

    public function saveImgsData($data) {
        return $this->__saveDatafile(Settings::IMGSLIST, $data);
    }

}
