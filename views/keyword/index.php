<?php

use app\Infrastructure\Persistence\KeywordRecord;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Ключевые слова';
?>
<div class="keyword-index card">
    <div class="card-header">
        <?= Html::a('Добавить слово', ['create'], ['class' => 'btn btn-success']) ?>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'id',
                [
                    'attribute' => 'project_id',
                    'value' => 'project.name',
                    'label' => 'Проект'
                ],
                'word',
                [
                    'attribute' => 'normalized_forms',
                    'label' => 'Формы для поиска',
                    'value' => function (KeywordRecord $model) {
                        $forms = $model->getNormalizedFormsArray();
                        return implode(', ', $forms);
                    }
                ],
                'created_at:datetime',
                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
</div>
