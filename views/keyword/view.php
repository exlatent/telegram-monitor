<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\Infrastructure\Persistence\KeywordRecord $model */

$this->title = 'Ключевое слово: ' . $model->word;
?>
<div class="keyword-view card">
    <div class="card-header">
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить это слово?',
                'method' => 'post',
            ],
        ]) ?>
    </div>
    <div class="card-body">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                [
                    'attribute' => 'project_id',
                    'label' => 'Проект',
                    'value' => $model->project ? $model->project->name : '—',
                ],
                'word',
                [
                    'label' => 'Нормализованные формы (JSON)',
                    'value' => $model->normalized_forms,
                ],
                [
                    'label' => 'Список форм',
                    'value' => implode(', ', $model->getNormalizedFormsArray()),
                ],
                'created_at:datetime',
                'updated_at:datetime',
            ],
        ]) ?>
    </div>
</div>
