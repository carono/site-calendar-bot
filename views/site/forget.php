<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model app\models\forms\ForgetForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', 'Forget');
$this->params['breadcrumbs'][] = $this->title;
?>
<h2 class="font-bold"><?= Yii::t('app', 'Forgot password') ?></h2>

<p>
    <?= Yii::t('app', 'Enter your email address, you will be sent a link to reset your password') ?>
</p>

<div class="row">
    <div class="col-lg-12">
        <?php $form = ActiveForm::begin(['id' => 'login-form', 'class' => 'm-t']); ?>
        <div class="form-group">
            <input type="email" class="form-control" placeholder="Email" required="" name="ForgetForm[username]">
        </div>

        <button type="submit" class="btn btn-primary block full-width m-b">
            <?= Yii::t('app', 'Send Reset Token') ?>
        </button>

        <?php ActiveForm::end(); ?>
    </div>
</div>
