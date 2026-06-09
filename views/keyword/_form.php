<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\Infrastructure\Persistence\KeywordRecord $model */
/** @var array $projects */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="keyword-form card">
    <div class="card-body">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'project_id')->dropDownList($projects, ['prompt' => 'Выберите проект']) ?>

        <?= $form->field($model, 'word')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
