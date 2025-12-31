<?php

namespace common\models;

use yii\base\Exception;
use yii\base\Model;

/**
 * Class AppleEatForm
 *
 * @property-read Apple $model
 */
class AppleEatForm extends Model
{
    /** @var int процент сколько откусить */
    public $percent;

    private Apple $_model;

    /**
     * @param Apple $model
     * @param array $config
     */
    public function __construct(Apple $model, array $config = [])
    {
        parent::__construct($config);
        $this->_model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['percent'], 'required'],
            [['percent'], 'integer', 'min' => 1],

        ];
    }

    /**
     * @throws Exception
     */
    public function eat(bool $isBeforeValidate = true): bool
    {
        if ($isBeforeValidate) {
            if (!$this->validate()) {
                return false;
            }
        }

        return $this->model->eat($this->percent);
    }

    /**
     * @return Apple
     */
    public function getModel(): Apple
    {
        return $this->_model;
    }


}