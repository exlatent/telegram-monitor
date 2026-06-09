<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\Infrastructure\Persistence\DigestLogRecord $model */

$this->title = 'Лог дайджеста #' . $model->id;
?>
<div class="digest-log-view card">
    <div class="card-body">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                [
                    'label' => 'Проект',
                    'value' => $model->project ? $model->project->name : '—',
                ],
                [
                    'label' => 'Пользователь',
                    'value' => $model->user ? ($model->user->username ?: $model->user->telegram_id) : '—',
                ],
                'matches_count',
                'created_at:datetime',
            ],
        ]) ?>

        <div class="mt-4">
            <h5>Текст сообщения:</h5>
            <div class="p-3 bg-light border rounded">
                <?= nl2br(Html::encode($model->message_text)) ?>
            </div>
        </div>
    </div>
</div>
