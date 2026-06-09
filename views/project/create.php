<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\Infrastructure\Persistence\ProjectRecord $model */
/** @var array $users */

$this->title = 'Создать проект';
?>
<div class="project-create">
    <?= $this->render('_form', [
        'model' => $model,
        'users' => $users,
    ]) ?>
</div>
