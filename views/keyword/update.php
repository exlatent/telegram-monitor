<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\Infrastructure\Persistence\KeywordRecord $model */
/** @var array $projects */

$this->title = 'Редактировать слово: ' . $model->word;
?>
<div class="keyword-update">
    <?= $this->render('_form', [
        'model' => $model,
        'projects' => $projects,
    ]) ?>
</div>
