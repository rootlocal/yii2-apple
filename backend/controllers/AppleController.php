<?php

namespace backend\controllers;

use common\models\Apple;
use common\models\AppleEatForm;
use common\models\AppleSearch;
use Throwable;
use Yii;
use yii\base\Exception;
use yii\base\UserException;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * AppleController implements the CRUD actions for Apple model.
 */
class AppleController extends BackendController
{
    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'generate' => ['GET'],
                        'eat' => ['GET', 'POST'],
                        'fall-to-ground' => ['GET'],
                    ],
                ],
            ],
        );
    }

    /**
     * Lists all Apple models.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new AppleSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGenerate(): Response
    {
        $generatedCounter = 0;
        $errors = [];
        for ($i = 0; $i < rand(5, 10); $i++) {
            $model = new Apple();
            $transaction = $model::getDb()->beginTransaction();

            try {
                $colors = array_keys($model->getColorsItems());
                $minColor = min($colors);
                $maxColor = max($colors);
                $model->size = 1;
                $model->created_at = rand(0, time());
                $model->color = rand($minColor, $maxColor);
                if ($model->save(false)) {
                    $transaction->commit();
                    $generatedCounter++;
                } else {
                    $transaction->rollBack();
                    $message = sprintf('Ошибка при генерации яблок, ошибка сохранения яблока #%d', ($i + 1));
                    Yii::error($message, __METHOD__);
                    $errors[] = $message;
                }
            } catch (\Throwable $e) {
                $transaction->rollBack();
                $errors[] = sprintf('Ошибка генерации яблока #%d', ($i + 1));
                Yii::error($e->getMessage(), __METHOD__);
            }

        }

        $message = sprintf('Сгенерировано яблок: %d', $generatedCounter);

        if (!empty($errors)) {
            Yii::$app->session->setFlash('error', sprintf('%s<br>Есть ошибки при генерации яблок: %s', $message, implode('<br>', $errors)));
        } else {
            Yii::$app->session->setFlash('success', $message);
        }

        return $this->redirect(['index']);
    }

    /**
     * Уронить яблоко на землю
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionFallToGround(int $id): Response
    {
        $model = $this->findModel($id);

        try {
            if ($model->fallToGround()) {
                $message = sprintf('Успешно. Яблоко "%s" на земле', $model->id);
                Yii::$app->session->setFlash('success', $message);
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка. Яблоко не упало');
            }
        } catch (UserException $e) {
            Yii::$app->session->setFlash('error', sprintf('Ошибка: "%s"', $e->getMessage()));
        } catch (Throwable $e) {
            Yii::$app->session->setFlash('error', 'Произошла непреденная ошибка падения яблока на землю');
            Yii::error($e->getMessage(), __METHOD__);
        }

        return $this->redirect(['index']);
    }

    /**
     * @bref Откусить яблоко
     * @param int $id id яблока
     * @return Response|string
     * @throws NotFoundHttpException|Exception
     */
    public function actionEat(int $id)
    {
        $model = new AppleEatForm($this->findModel($id));

        if ($this->request->isPost && $model->load($this->request->post()) && $model->validate()) {
            try {
                if ($model->eat()) {
                    Yii::$app->session->setFlash('success', sprintf('Яблоко успешно откушено остаток от яблока: "%1.2f"', $model->model->size));
                } else {
                    Yii::$app->session->setFlash('error', 'Ошибка. Яблоко не удалось откусить');
                }
            } catch (UserException $e) {
                Yii::$app->session->setFlash('error', sprintf('Ошибка: "%s"', $e->getMessage()));
            } catch (Throwable $e) {
                Yii::$app->session->setFlash('error', 'Произошла ошибка. яблоко не удалось откусить');
                Yii::error($e->getMessage(), __METHOD__);
            }

            return $this->redirect(['eat', 'id' => $id]);
        }

        return $this->render('eat', ['model' => $model]);
    }

    /**
     * Finds the Apple model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Apple the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Apple
    {
        if (($model = Apple::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
