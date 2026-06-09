<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

/** @var yii\web\View $this */
/** @var app\Infrastructure\Persistence\UserRecord $model */

$this->title = 'Пользователь: ' . ($model->username ?: $model->telegram_id);
?>
<div class="user-view">
    <div class="card">
        <div class="card-header">
            <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены, что хотите удалить этого пользователя?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
        <div class="card-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'telegram_id',
                    'username',
                    'first_name',
                    'last_name',
                    'status',
                    'created_at:datetime',
                    'updated_at:datetime',
                ],
            ]) ?>
        </div>
    </div>

    <h3>Проекты пользователя</h3>
    <div class="card">
        <div class="card-body">
            <?= GridView::widget([
                'dataProvider' => new ActiveDataProvider([
                    'query' => $model->getProjects(),
                ]),
                'columns' => [
                    'id',
                    'name',
                    'is_active:boolean',
                    'created_at:datetime',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'controller' => 'project',
                        'template' => '{view}'
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>
