<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\Infrastructure\Persistence\UserRecord $model */

$this->title = 'Редактировать пользователя: ' . ($model->username ?: $model->telegram_id);
?>
<div class="user-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
