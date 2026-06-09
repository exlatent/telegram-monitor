<?php

use app\Infrastructure\Persistence\DigestLogRecord;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Логи дайджестов';
?>
<div class="digest-log-index card">
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
                [
                    'attribute' => 'user_id',
                    'value' => function (DigestLogRecord $model) {
                        return $model->user ? ($model->user->username ?: $model->user->telegram_id) : '—';
                    },
                    'label' => 'Пользователь'
                ],
                'matches_count',
                'created_at:datetime',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                ],
            ],
        ]); ?>
    </div>
</div>
