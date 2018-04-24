<?php

namespace app\models;


use \app\models\User;
use yii\base\Exception;
use yii\base\InvalidArgumentException;

/**
 * Class UserManager
 *
 * @package app\models
 */
class UserManager{

    /**
     * @var float
     */
    const BALANCE_DEFAULT = 0;

    /**
     * @var float
     */
    const BALANCE_MIN = -1000;


    /**
     * @param string $userName
     * @return bool
     * @throws Exception
     */
    public function login(string $userName) : bool {
        $user = $this->getUserByName($userName);
        return \Yii::$app->user->login($user);
    }


    /**
     * @param string $userName
     * @return \app\models\User
     * @throws Exception
     */
    private function createUserByName(string $userName) : User {
        if (!trim($userName)) {
            throw new InvalidArgumentException('No name for new user');
        }
        $user = new User();
        $user->load(['User' => ['username' => $userName]]);
        $user->setAttribute('balance', self::BALANCE_DEFAULT);
        $user->setAttribute('created_at', date('U'));
        if (!$user->save()) {
            throw new Exception('Error. Can\'t create user.');
        }
        return $user;
    }

    /**
     * If user doesn't exist, creates him
     * @param string $userName
     * @return \app\models\User
     * @throws Exception
     */
    public function getUserByName(string $userName) : User {
        $user = $this->findByUsername($userName);
        if (!$user) {
            $user = $this->createUserByName($userName);
            if (!$user) {
                throw new Exception("User '$userName' not found");
            }
        }
        return $user;
    }

    /**
     * @param $id
     * @return null|User
     */
    public function findIdentity($id) : ?User
    {
        return User::findOne(['id' => $id]);
    }

    /**
     * @param $username
     * @return null|User
     */
    public function findByUsername($username) : ?User
    {
        return User::findOne(['username' => $username]);
    }

    /**
     * @return float
     */
    public function getMinPossibleBalance() : float {
        return self::BALANCE_MIN;
    }

    /**
     * @param array $idList
     * @return array
     */
    public function getNamesByIds(array $idList) : array {
        $res = [];
        foreach ($this->getUsersByIds($idList) as $user) {
            /* User $user */
            $res[$user->id] = $user->username;
        }
        return $res;
    }

    /**
     * @param array $idList
     * @return array
     */
    public function getUsersByIds(array $idList) : array {
        return User::find()->where(['id' => $idList])->all();
    }

}