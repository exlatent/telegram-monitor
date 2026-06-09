<?php

use app\Infrastructure\Persistence\UserRecord;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Пользователи';
?>
<div class="user-index card">
    <div class="card-header">
        <?= Html::a('Создать пользователя', ['create'], ['class' => 'btn btn-success']) ?>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'id',
                'telegram_id',
                'username',
                [
                    'label' => 'Имя',
                    'value' => function (UserRecord $model) {
                        return trim($model->first_name . ' ' . $model->last_name);
                    }
                ],
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'value' => function (UserRecord $model) {
                        $class = $model->status === 'active' ? 'badge-success' : 'badge-danger';
                        return Html::tag('span', Html::encode($model->status), ['class' => "badge $class"]);
                    }
                ],
                'created_at:datetime',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update} {delete} {toggle}',
                    'buttons' => [
                        'toggle' => function ($url, $model) {
                            $icon = $model->status === 'active' ? 'fa-ban' : 'fa-check';
                            $title = $model->status === 'active' ? 'Заблокировать' : 'Разблокировать';
                            return Html::a('<i class="fas ' . $icon . '"></i>', ['toggle-status', 'id' => $model->id], [
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
