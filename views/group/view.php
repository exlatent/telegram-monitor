<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\Infrastructure\Persistence\MonitoredGroupRecord $model */

$this->title = 'Группа: ' . $model->name;
?>
<div class="group-view card">
    <div class="card-header">
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены?',
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
                'name',
                'telegram_username',
                'telegram_id',
                'last_message_id',
                'created_at:datetime',
                'updated_at:datetime',
            ],
        ]) ?>
    </div>
</div>
