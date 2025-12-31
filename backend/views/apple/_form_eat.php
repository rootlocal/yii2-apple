<?php

use common\models\AppleEatForm;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\web\View;

/**
 * @var View $this
 * @var AppleEatForm $model
 * @var ActiveForm $form
 */
?>

<div class="apple-eat-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'percent')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
