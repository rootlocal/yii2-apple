<?php

namespace common\models;

use Throwable;
use Yii;
use yii\base\Exception;
use yii\base\UserException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%apple}}".
 *
 * @property int $id
 * @property integer $color
 * @property float $size
 * @property int $state
 * @property int $created_at
 * @property int $updated_at
 * @property int $date_of_fall
 *
 *
 * @property-read array $stateItems {@see self::getStateItems()}
 * @property-read array $colorsItems {@see self::getColorsItems()}
 */
class Apple extends ActiveRecord
{
    /** @var int На дереве */
    public const APPLE_STATE_ON_TREE = 1;
    /** @var int На земле */
    public const APPLE_STATE_ON_GROUND = 2;
    /** @var int Время лежания яблока на земле пока не протухнет */
    public const APPLE_LIFE_TIME_SECONDS = 5 * 3600;

    public const APPLE_COLOR_RED = 1;
    public const APPLE_COLOR_GREEN = 2;
    public const APPLE_COLOR_BLUE = 3;
    public const APPLE_COLOR_BLACK = 4;
    public const APPLE_COLOR_CYAN = 5;
    public const APPLE_COLOR_MAGENTA = 6;
    public const APPLE_COLOR_YELLOW = 7;
    public const APPLE_COLOR_WHITE = 8;
    public const APPLE_COLOR_NAVY = 9;
    public const APPLE_COLOR_PURPLE = 10;
    public const APPLE_COLOR_ORANGE = 11;


    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%apple}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['state'], 'default', 'value' => 1],
            [['color'], 'required'],
            [['size'], 'number'],
            [['color'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'color' => 'Цвет',
            'size' => 'Размер',
            'state' => 'Статус',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлен',
            'date_of_fall' => 'Дата падения',
        ];
    }

    /** {@inheritdoc} */
    public function behaviors(): array
    {
        return [

            'timestampBehavior' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => null,
            ],
        ];
    }

    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if (empty($this->created_at)) {
            $this->created_at = time();
        }

        return true;
    }

    /**
     * Откусить яблоко
     * @throws Exception
     */
    public function eat(int $percent): bool
    {
        if ($this->state === static::APPLE_STATE_ON_TREE) {
            throw new UserException('Съесть нельзя, яблоко на дереве');
        } elseif ($this->hasBad()) {
            throw new UserException('Яблоко испортилось');
        } elseif (round($this->size * 100) - $percent < 0) {
            throw new UserException(sprintf('Нельзя откусить больше чем есть. Осталось от яблока: "%1.2f" Кусаем: %d%%', $this->size, $percent));
        }

        $transaction = $this::getDb()->beginTransaction();

        try {
            $this->size = round($this->size - ($percent / 100), 2);
            if ($this->save(false)) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
            }
        } catch (Throwable $e) {
            Yii::error($e->getMessage(), __METHOD__);
            $transaction->rollBack();
        }

        return false;
    }

    /**
     * Можем ли откусить яблоко
     * @return bool
     */
    public function hasEat(): bool
    {
        return $this->state === static::APPLE_STATE_ON_GROUND && !$this->hasBad();
    }

    /**
     * Испорчено ли яблоко
     * @return bool
     */
    public function hasBad(): bool
    {
        if ($this->state === static::APPLE_STATE_ON_GROUND) { // проверяем только если яблоко на земле
            return (time() - $this->date_of_fall) > static::APPLE_LIFE_TIME_SECONDS;
        } else {
            return true;
        }
    }

    /**
     * Уронить на землю
     * @throws \yii\db\Exception
     * @throws UserException
     */
    public function fallToGround(): bool
    {
        if (!$this->hasEat()) { // Если яблоко не на земле - значит определенно весит на дереве
            $transaction = $this::getDb()->beginTransaction();

            try {
                $this->date_of_fall = time();
                $this->state = static::APPLE_STATE_ON_GROUND;

                if ($this->save(false)) {
                    $transaction->commit();
                    return true;
                } else {
                    $transaction->rollBack();
                    $message = 'Ошибка при падении яблока. Не удалось сохранить';
                    Yii::error($message, __METHOD__);
                }

            } catch (\yii\db\Exception $e) {
                Yii::error($e->getMessage(), __METHOD__);
                $transaction->rollBack();
                throw $e;
            }

        } else {
            throw new UserException(sprintf('Яблоко id="%s" уже на земле', $this->id));
        }


        return false;
    }

    public function getStateItems(): array
    {
        return [
            static::APPLE_STATE_ON_TREE => 'На дереве',
            static::APPLE_STATE_ON_GROUND => 'На Земле',
        ];
    }

    public function getStateItem($item): string
    {
        $items = $this->getStateItems();
        return array_key_exists($item, $items) ? $items[$item] : 'Не поддерживаемое значение';
    }

    public function getColorsItems(): array
    {
        return [
            static::APPLE_COLOR_RED => 'Красный',
            static::APPLE_COLOR_GREEN => 'Зеленый',
            static::APPLE_COLOR_BLUE => 'Голубой',
            static::APPLE_COLOR_BLACK => 'Черный',
            static::APPLE_COLOR_CYAN => 'Сине-зелёный',
            static::APPLE_COLOR_MAGENTA => 'Пурпурный',
            static::APPLE_COLOR_YELLOW => 'Желтый',
            static::APPLE_COLOR_WHITE => 'Белый',
            static::APPLE_COLOR_NAVY => 'формы морских офицеров',
            static::APPLE_COLOR_PURPLE => 'Пурпурный',
            static::APPLE_COLOR_ORANGE => 'Оранжеывй',
        ];
    }

    public function getColorsItem($item): string
    {
        $items = $this->getColorsItems();
        return array_key_exists($item, $items) ? $items[$item] : 'Некорректное значение';
    }

}
