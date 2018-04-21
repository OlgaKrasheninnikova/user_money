<?php
namespace app\providers;

use app\models\UserManager;
use yii\data\ActiveDataProvider;

/**
 * Class OperationIndexProvider. Prerare additional data to show operations list.
 * @package app\providers
 */
class OperationIndexProvider extends ActiveDataProvider {

    public $userNamesHash;

    public function init(){
        parent::init();

        $userIdsList = [];
        foreach ($this->getModels() as $model) {
            /** Operation $model */
            $userIdsList[$model->from_user_id] = true;
            $userIdsList[$model->to_user_id] = true;
        }
        $um = new UserManager();
        $this->userNamesHash = $um->getNamesByIds(array_keys($userIdsList));

    }
}
