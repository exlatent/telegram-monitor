<?php

namespace app\Presentation\Controllers;

use app\Infrastructure\Persistence\ProjectRecord;
use app\Infrastructure\Persistence\UserRecord;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use Yii;

class ProjectController extends AdminBaseController
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ProjectRecord::find()->with('user'),
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC]
            ]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = ProjectRecord::find()
            ->where(['id' => $id])
            ->with(['user', 'monitoredGroups', 'keywords'])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('Project not found');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        $model = new ProjectRecord();
        $model->is_active = true;
        $model->digest_interval = 60;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $users = UserRecord::find()
            ->select(["CONCAT(username, ' (', telegram_id, ')') as name", 'id'])
            ->indexBy('id')
            ->column();

        return $this->render('create', [
            'model' => $model,
            'users' => $users,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $users = UserRecord::find()
            ->select(["CONCAT(username, ' (', telegram_id, ')') as name", 'id'])
            ->indexBy('id')
            ->column();

        return $this->render('update', [
            'model' => $model,
            'users' => $users,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    public function actionToggleActive($id)
    {
        $model = $this->findModel($id);
        $model->is_active = !$model->is_active;
        $model->save(false);
        
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = ProjectRecord::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
