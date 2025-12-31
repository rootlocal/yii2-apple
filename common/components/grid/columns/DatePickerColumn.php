<?php

namespace common\components\grid\columns;

use kartik\date\DatePicker;
use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;

class DatePickerColumn extends DataColumn
{
    public $format = 'date';
    public string $placeholder = 'Выберете...';
    public array $pluginOptions = [];


    protected function renderDatePickerWidget(): ?string
    {
        if (empty($this->grid->filterModel)) {
            return null;
        }

        $pluginOptions = ArrayHelper::merge([
            'calendarWeeks' => false,
            'autoclose' => true,
            'todayBtn' => false,
            'format' => 'dd.mm.yyyy',
            'todayHighlight' => false,
        ], $this->pluginOptions);

        return DatePicker::widget([
            'model' => $this->grid->filterModel,
            'attribute' => $this->filterAttribute,
            'options' => [
                'class' => 'form-control',
                'placeholder' => $this->placeholder,
                'autocomplete' => 'off',
            ],
            'language' => 'ru',
            'type' => DatePicker::TYPE_COMPONENT_APPEND,
            'pluginOptions' => $pluginOptions,
        ]);
    }

    public function renderFilterCellContent()
    {
        $this->filter = $this->renderDatePickerWidget();
        return parent::renderFilterCellContent();
    }

}
