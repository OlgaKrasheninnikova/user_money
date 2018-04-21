<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Operations of user "' . $userName . '"';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="operation-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Operation', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute' => 'from_user_id',
                'label' => 'Plaintiff',
                'value' => function ($model) use ($dataProvider) {
                    return $dataProvider->userNamesHash[$model->from_user_id] ?? '';
                }
            ],
            [
               'attribute' => 'to_user_id',
               'label' => 'Recipient',
               'value' => function ($model) use ($dataProvider)  {
                   return $dataProvider->userNamesHash[$model->to_user_id] ?? '';
               }
            ],
            ['attribute' => 'amount', 'format' => ['currency', 'EUR']],
            'executed_at:datetime',
        ],
    ]); ?>
</div>
