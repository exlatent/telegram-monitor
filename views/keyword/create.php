<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\Infrastructure\Persistence\KeywordRecord $model */
/** @var array $projects */

$this->title = 'Добавить ключевое слово';
?>
<div class="keyword-create">
    <?= $this->render('_form', [
        'model' => $model,
        'projects' => $projects,
    ]) ?>
</div>
