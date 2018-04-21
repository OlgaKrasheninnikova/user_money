<?php

namespace app\models;


use \app\models\User;
use yii\base\Exception;

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
     */
    public function login(string $userName) : bool {
        $user = UserManager::findByUsername($userName);
        if (!$user) $user = $this->createUser($userName);
        return \Yii::$app->user->login($user);
    }


    /**
     * @param string $userName
     * @return \app\models\User
     * @throws Exception
     */
    public function createUser(string $userName) : User {
        if (!$userName) {
            throw new Exception('No name for new user');
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
    public function getUserObjectByName(string $userName) : User {
        $user = UserManager::findByUsername($userName);
        if (!$user) {
            $user = $this->createUser($userName);
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
    public static function findIdentity($id)
    {
        return User::findOne(['id' => $id]);
    }

    /**
     * @param $username
     * @return null|User
     */
    public static function findByUsername($username)
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
        $usersList = User::find()->where(['id' => $idList])->all();
        $res = [];
        foreach ($usersList as $user) {
            /* User $user */
            $res[$user->id] = $user->username;
        }
        return $res;
    }

}