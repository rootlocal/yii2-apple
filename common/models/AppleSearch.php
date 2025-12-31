<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;

/**
 * AppleSearch represents the model behind the search form of `common\models\Apple`.
 */
class AppleSearch extends Apple
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'state'], 'integer'],
            [['color'], 'integer'],
            [['size'], 'number'],
            [['created_at', 'updated_at', 'date_of_fall'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return DataProviderInterface
     */
    public function search(array $params = [], ?string $formName = null): DataProviderInterface
    {
        $query = Apple::find()->alias('a')
            ->andWhere(['>', 'a.size', 0])
            ->andWhere(['or',
                ['a.state' => static::APPLE_STATE_ON_TREE],
                ['and',
                    ['a.state' => static::APPLE_STATE_ON_GROUND],
                    [' >=', 'a.date_of_fall', time() - static::APPLE_LIFE_TIME_SECONDS],
                ],
            ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            $query->where('0=1'); // Будем показывать ничего, при невалидных фильтров поиска
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'size' => $this->size,
            'color' => $this->color,
            'state' => $this->state,
            'created_at' => $this->created_at,
            'date_of_fall' => $this->date_of_fall,
        ]);

        if (!empty($this->updated_at)) {
            $start = Yii::$app->formatter->asTimestamp($this->updated_at . ' 00:00:01');
            $stop = Yii::$app->formatter->asTimestamp($this->updated_at . ' 23:59:59');

            if ($start !== false) {
                $query->andWhere(['>=', 'a.updated_at', $start]);
            }

            if ($stop !== false) {
                $query->andWhere(['<=', 'a.updated_at', $stop]);
            }
        }

        if (!empty($this->created_at)) {
            $start = Yii::$app->formatter->asTimestamp($this->created_at . ' 00:00:01');
            $stop = Yii::$app->formatter->asTimestamp($this->created_at . ' 23:59:59');

            if ($start !== false) {
                $query->andWhere(['>=', 'a.created_at', $start]);
            }

            if ($stop !== false) {
                $query->andWhere(['<=', 'a.created_at', $stop]);
            }
        }

        if (!empty($this->date_of_fall)) {
            $start = Yii::$app->formatter->asTimestamp($this->date_of_fall . ' 00:00:01');
            $stop = Yii::$app->formatter->asTimestamp($this->date_of_fall . ' 23:59:59');

            if ($start !== false) {
                $query->andWhere(['>=', 'a.date_of_fall', $start]);
            }

            if ($stop !== false) {
                $query->andWhere(['<=', 'a.date_of_fall', $stop]);
            }
        }

        return $dataProvider;
    }
}
