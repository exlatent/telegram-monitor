<?php

namespace app\Presentation\Controllers;

use app\Infrastructure\Persistence\DigestLogRecord;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class DigestLogController extends AdminBaseController
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => DigestLogRecord::find()->with(['project', 'user']),
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    protected function findModel($id)
    {
        if (($model = DigestLogRecord::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Log entry not found.');
    }
}
