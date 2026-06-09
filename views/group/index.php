<?php

use app\Infrastructure\Persistence\MonitoredGroupRecord;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Группы для мониторинга';
?>
<div class="group-index card">
    <div class="card-header">
        <?= Html::a('Добавить группу', ['create'], ['class' => 'btn btn-success']) ?>
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
                'name',
                'telegram_username',
                'telegram_id',
                'last_message_id',
                'created_at:datetime',
                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
</div>
