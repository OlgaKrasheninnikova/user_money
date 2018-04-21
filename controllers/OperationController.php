<?php

namespace app\controllers;

use app\models\OperationManager;
use app\models\UserManager;
use app\providers\OperationIndexProvider;
use Yii;
use app\models\Operation;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OperationController
 */
class OperationController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create'],
                        'roles' => ['@'],
                    ]
                ],
            ]
        ];
    }


    /**
     * Lists all Operation models.
     * @return string
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;

        $dataProvider = new OperationIndexProvider([
            'query' => Operation::find()->where(['from_user_id' => $userId])->orWhere(['to_user_id' => $userId]),
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'userName' => Yii::$app->user->identity->username,
        ]);
    }

    /**
     * Creates a new Operation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string
     */
    public function actionCreate()
    {
        $model = new Operation();
        $model->scenario = Operation::SCENARIO_USER_PAY;
        if ($model->load(Yii::$app->request->post())) {
            $operationManager = new OperationManager();
            try {
                $um = new UserManager();
                $userTo = $um->getUserObjectByName($model->to_user_name);
                $operationManager->execute(Yii::$app->user->identity, $userTo, $model->getAttribute('amount'));
            } catch (\Exception $e) {
                return $this->render('create', [
                    'model' => $model,
                    'error' => $e->getMessage()
                ]);
            }
            return $this->redirect(['/operation', 'id' => Yii::$app->user->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Operation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Operation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Operation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
