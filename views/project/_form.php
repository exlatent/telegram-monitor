<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\Infrastructure\Persistence\ProjectRecord $model */
/** @var array $users */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="project-form card">
    <div class="card-body">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'user_id')->dropDownList($users, ['prompt' => 'Выберите владельца']) ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'digest_interval')->textInput(['type' => 'number'])->hint('В минутах') ?>

        <?= $form->field($model, 'is_active')->checkbox() ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
