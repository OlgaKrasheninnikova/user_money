<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Operation */

$this->title = 'Make Operation';
$this->params['breadcrumbs'][] = ['label' => 'Operations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="operation-create">

    <?php if (isset($error)) { ?>
        <div class="alert alert-danger"><?=$error?></div>
    <?php } ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
