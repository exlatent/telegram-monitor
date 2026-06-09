<?php

use app\Infrastructure\Persistence\MatchRecord;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Совпадения';
?>
<div class="match-index card">
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'id',
                [
                    'attribute' => 'project_id',
                    'label' => 'Проект',
                    'value' => function (MatchRecord $model) {
                        return $model->project ? $model->project->name : '—';
                    }
                ],
                [
                    'attribute' => 'keyword_id',
                    'label' => 'Ключевое слово',
                    'value' => function (MatchRecord $model) {
                        return $model->keyword ? $model->keyword->word : '—';
                    }
                ],
                [
                    'label' => 'Текст сообщения',
                    'format' => 'raw',
                    'value' => function (MatchRecord $model) {
                        if (!$model->message) return '—';
                        $text = Html::encode(mb_substr($model->message->text, 0, 100)) . '...';
                        if ($model->message->link) {
                            return Html::a($text, $model->message->link, ['target' => '_blank']);
                        }
                        return $text;
                    }
                ],
                [
                    'attribute' => 'is_sent',
                    'format' => 'raw',
                    'value' => function (MatchRecord $model) {
                        $class = $model->is_sent ? 'badge-success' : 'badge-warning';
                        $text = $model->is_sent ? 'Отправлено' : 'В очереди';
                        return Html::tag('span', $text, ['class' => "badge $class"]);
                    }
                ],
                'created_at:datetime',
            ],
        ]); ?>
    </div>
</div>
