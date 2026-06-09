<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

/** @var yii\web\View $this */
/** @var app\Infrastructure\Persistence\ProjectRecord $model */

$this->title = 'Проект: ' . $model->name;
?>
<div class="project-view">
    <div class="card">
        <div class="card-header">
            <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Добавить группу', ['group/create', 'project_id' => $model->id], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Добавить слово', ['keyword/create', 'project_id' => $model->id], ['class' => 'btn btn-info']) ?>
        </div>
        <div class="card-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    [
                        'label' => 'Владелец',
                        'value' => $model->user ? ($model->user->username ?: $model->user->telegram_id) : '—'
                    ],
                    'name',
                    'digest_interval',
                    'is_active:boolean',
                    'last_digest_sent_at:datetime',
                    'created_at:datetime',
                ],
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h3>Группы</h3>
            <div class="card">
                <div class="card-body">
                    <?= GridView::widget([
                        'dataProvider' => new ActiveDataProvider(['query' => $model->getMonitoredGroups()]),
                        'summary' => false,
                        'columns' => [
                            'name',
                            'telegram_username',
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'controller' => 'group',
                                'template' => '{view} {delete}'
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <h3>Ключевые слова</h3>
            <div class="card">
                <div class="card-body">
                    <?= GridView::widget([
                        'dataProvider' => new ActiveDataProvider(['query' => $model->getKeywords()]),
                        'summary' => false,
                        'columns' => [
                            'word',
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'controller' => 'keyword',
                                'template' => '{view} {delete}'
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
