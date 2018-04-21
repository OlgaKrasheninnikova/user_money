<?php

namespace app\models;

use yii\base\Exception;


/**
 * Class OperationManager
 *
 * @package app\models
 */
class OperationManager{


    /**
     * Executes money transfer
     * @param User $fromUser
     * @param User $toUser
     * @param float $amount
     * @return bool
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public function execute(User $fromUser, User $toUser, float $amount) : bool {
        if ($fromUser->id == $toUser->id) {
            throw new Exception('User can\'t move money to his own bill');
        }

        if (!$fromUser->decreaseBalance($amount) || !$toUser->increaseBalance($amount)) {
            throw new Exception('Can\'t change user balance');
        }

        $transaction = \Yii::$app->db->beginTransaction();

        if ($fromUser->save() && $toUser->save() && $this->createOperation($fromUser, $toUser, $amount)) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            throw new Exception('Can\'t save changes');
        }

    }


    /**
     * @param User $fromUser
     * @param User $toUser
     * @param float $amount
     * @return bool
     */
    public function createOperation(User $fromUser, User $toUser, float $amount) : bool {
        if (!$amount || !$fromUser->id || !$toUser->id) {
            return false;
        }
        if ($fromUser->id == $toUser->id) {
            return false;
        }

        $operation = new Operation();
        $operation->scenario = Operation::SCENARIO_USER_PAY;
        $operation->setAttribute('from_user_id', $fromUser->id);
        $operation->setAttribute('to_user_id', $toUser->id);
        $operation->setAttribute('amount', $amount);
        $operation->setAttribute('executed_at', date('U'));
        return $operation->save(false);
    }


}