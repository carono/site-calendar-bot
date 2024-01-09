<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model app\models\forms\ResetForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', 'Registration');
$this->params['breadcrumbs'][] = $this->title;
?>
<h2 class="font-bold"><?= Yii::t('app', 'Reset Password') ?></h2>

<p>
    <?= Yii::t('app', 'Enter your new password') ?>
</p>

<div class="row">
    <div class="col-lg-12">
        <?php $form = ActiveForm::begin(['id' => 'login-form', 'class' => 'm-t']); ?>
        <div class="form-group">
            <input type="password" class="form-control" placeholder="новый пароль" required="" name="ResetForm[password]">
        </div>

        <button type="submit" class="btn btn-primary block full-width m-b">
            <?= Yii::t('app', 'Change Password') ?>
        </button>

        <?php ActiveForm::end(); ?>
    </div>
</div>