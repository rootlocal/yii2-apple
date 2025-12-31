<?php

use common\models\AppleEatForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var AppleEatForm $model
 */

$this->title = 'Съесть яблоко: ' . $model->model->id;
$this->params['breadcrumbs'][] = ['label' => 'Яблоки', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Съесть яблоко';
?>
<div class="apple-eat">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="h4">Текущий остаток от яблока: <?= $model->model->size ?></div>
    <?= $this->render('_form_eat', [
        'model' => $model,
    ]) ?>

</div>