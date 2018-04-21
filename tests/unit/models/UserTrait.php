<?php

trait UserTrait {

    public function createUser($name = null, $balance = null) {
        if (is_null($name)) {
            $name = 'user1';
        }
        if (is_null($balance)) {
            $balance = \app\models\UserManager::BALANCE_DEFAULT;
        }
        $id = $this->tester->haveRecord('app\models\User', ['username' => $name, 'balance' => $balance, 'created_at' => date('U')]);
        return \app\models\User::findIdentity($id);
    }
}