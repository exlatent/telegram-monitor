<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\Infrastructure\Persistence\ProjectRecord $model */
/** @var array $users */

$this->title = 'Редактировать проект: ' . $model->name;
?>
<div class="project-update">
    <?= $this->render('_form', [
        'model' => $model,
        'users' => $users,
    ]) ?>
</div>
