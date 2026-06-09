<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\Infrastructure\Persistence\MonitoredGroupRecord $model */
/** @var array $projects */

$this->title = 'Редактировать группу: ' . $model->name;
?>
<div class="group-update">
    <?= $this->render('_form', [
        'model' => $model,
        'projects' => $projects,
    ]) ?>
</div>
