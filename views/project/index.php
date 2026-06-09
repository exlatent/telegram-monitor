<?php

use app\Infrastructure\Persistence\ProjectRecord;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Проекты';
?>
<div class="project-index card">
    <div class="card-header">
        <?= Html::a('Создать проект', ['create'], ['class' => 'btn btn-success']) ?>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'id',
                [
                    'attribute' => 'user_id',
                    'label' => 'Пользователь',
                    'value' => function (ProjectRecord $model) {
                        return $model->user ? ($model->user->username ?: "ID: " . $model->user->telegram_id) : '—';
                    }
                ],
                'name',
                'digest_interval',
                [
                    'attribute' => 'is_active',
                    'format' => 'raw',
                    'value' => function (ProjectRecord $model) {
                        $class = $model->is_active ? 'badge-success' : 'badge-secondary';
                        $text = $model->is_active ? 'Активен' : 'Пауза';
                        return Html::tag('span', $text, ['class' => "badge $class"]);
                    }
                ],
                'created_at:datetime',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update} {delete} {toggle}',
                    'buttons' => [
                        'toggle' => function ($url, $model) {
                            $icon = $model->is_active ? 'fa-pause' : 'fa-play';
                            $title = $model->is_active ? 'Приостановить' : 'Активировать';
                            return Html::a('<i class="fas ' . $icon . '"></i>', ['toggle-active', 'id' => $model->id], [
                                'title' => $title,
                                'data-method' => 'post',
                                'class' => 'btn btn-sm btn-default'
                            ]);
                        }
                    ]
                ],
            ],
        ]); ?>
    </div>
</div>
