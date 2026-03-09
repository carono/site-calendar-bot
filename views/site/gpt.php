<?php

use app\models\gpt\GptForm;
use kartik\file\FileInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var GptForm $model
 */
$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);

echo "<div class='row'>";
echo "<div class='col-lg-6'>";
echo $form->field($model, 'system')->textarea(['rows' => 5]);
echo "</div>";

echo "<div class='col-lg-6'>";
echo $form->field($model, 'prompt')->textarea(['rows' => 5]);
echo "</div>";
echo "</div>";

echo FileInput::widget([
    'model' => $model,
    'attribute' => 'image[]',
    'options' => ['multiple' => true]
]);

ActiveForm::end();

if ($model->answer) {
    echo '<br>';
    echo Html::tag('div', nl2br($model->answer), ['class' => 'alert alert-info']);
}