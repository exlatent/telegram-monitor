<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\Infrastructure\Persistence\MonitoredGroupRecord $model */
/** @var array $projects */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="group-form card">
    <div class="card-body">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'project_id')->dropDownList($projects, ['prompt' => 'Выберите проект']) ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'telegram_username')->textInput(['maxlength' => true])->hint('Без символа @') ?>

        <?= $form->field($model, 'telegram_id')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
