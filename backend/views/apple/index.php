<?php

use common\components\grid\columns\DatePickerColumn;
use common\models\Apple;
use yii\bootstrap5\Html;
use yii\bootstrap5\LinkPager;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var common\models\AppleSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Apples';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apple-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Сгенерировать', ['generate'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => LinkPager::class],
        'columns' => [
            [
                'class' => SerialColumn::class,
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => [
                    'class' => 'text-center',
                    'style' => ['width' => '20px'],
                ],
            ],

            [
                'attribute' => 'id',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => [
                    'class' => 'text-center',
                    'style' => ['width' => '20px'],
                ],
            ],

            [
                'attribute' => 'color',
                'filter' => $searchModel->getColorsItems(),
                'value' => function (Apple $model) {
                    return $model->getColorsItem($model->color);
                },
            ],
            'size',
            [
                'attribute' => 'state',
                'filter' => $searchModel->stateItems,
                'value' => function (Apple $model) {
                    return $model->getStateItem($model->state);
                },
            ],

            [
                'class' => DatePickerColumn::class,
                'attribute' => 'created_at',
                'format' => 'datetime',
                'headerOptions' => [
                    'style' => ['width' => '200px'],
                    'class' => 'text-center',
                ],
            ],
            [
                'class' => DatePickerColumn::class,
                'attribute' => 'updated_at',
                'format' => 'datetime',
                'headerOptions' => [
                    'style' => ['width' => '200px'],
                    'class' => 'text-center',
                ],
            ],
            [
                'class' => DatePickerColumn::class,
                'attribute' => 'date_of_fall',
                'format' => 'datetime',
                'headerOptions' => [
                    'style' => ['width' => '200px'],
                    'class' => 'text-center',
                ],
            ],

            [
                'class' => ActionColumn::class,
                'template' => '{eat} {fall-to-ground}',
                'urlCreator' => function ($action, Apple $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'buttons' => [
                    'fall-to-ground' => function ($url, $model, $key) {
                        return Html::a('Уронить', $url, [
                            'class' => 'btn btn-sm btn-success',
                            'title' => 'Уронить яблоко',
                            'data-pjax' => '0',
                        ]);
                    },
                    'eat' => function ($url, $model, $key) {
                        return Html::a('съесть', $url, [
                            'class' => 'btn btn-sm btn-success',
                            'title' => 'съесть процент',
                            'data-pjax' => '0',
                        ]);
                    },
                ],
                'visibleButtons' => [
                    'eat' => function (Apple $model, $key, $index) {
                        return $model->hasEat();
                    },
                    'fall-to-ground' => function (Apple $model, $key, $index) {
                        return $model->state == $model::APPLE_STATE_ON_TREE;
                    },
                ],
            ],


        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
