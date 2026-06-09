<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\Infrastructure\Persistence\MonitoredGroupRecord $model */
/** @var array $projects */

$this->title = 'Добавить группу';
?>
<div class="group-create">
    <?= $this->render('_form', [
        'model' => $model,
        'projects' => $projects,
    ]) ?>
</div>
