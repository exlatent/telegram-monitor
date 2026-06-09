<?php

namespace app\Presentation\Controllers;

use app\Infrastructure\Morphology\MorphologyAnalyzer;
use app\Infrastructure\Persistence\KeywordRecord;
use app\Infrastructure\Persistence\ProjectRecord;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use Yii;

class KeywordController extends AdminBaseController
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => KeywordRecord::find()->with('project'),
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
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

    public function actionCreate($project_id = null)
    {
        $model = new KeywordRecord();
        if ($project_id) {
            $model->project_id = $project_id;
        }

        if ($model->load(Yii::$app->request->post())) {
            $analyzer = new MorphologyAnalyzer();
            $model->setNormalizedFormsArray($analyzer->getNormalizedForms($model->word));
            
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $projects = ProjectRecord::find()->select(['name', 'id'])->indexBy('id')->column();

        return $this->render('create', [
            'model' => $model,
            'projects' => $projects,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $analyzer = new MorphologyAnalyzer();
            $model->setNormalizedFormsArray($analyzer->getNormalizedForms($model->word));

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $projects = ProjectRecord::find()->select(['name', 'id'])->indexBy('id')->column();

        return $this->render('update', [
            'model' => $model,
            'projects' => $projects,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = KeywordRecord::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
