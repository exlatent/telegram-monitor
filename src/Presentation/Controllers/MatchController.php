<?php

namespace app\Presentation\Controllers;

use app\Infrastructure\Persistence\MatchRecord;
use yii\data\ActiveDataProvider;

class MatchController extends AdminBaseController
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => MatchRecord::find()->with(['message', 'keyword', 'project']),
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC]
            ]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
}
