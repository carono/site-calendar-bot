<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model app\models\forms\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Registration');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php ActiveForm::begin(['options' => ['class' => 'm-t'], 'action' => ['/site/register']]) ?>
<div class="form-group">
    <input type="email" class="form-control" placeholder="E-mail" required="" name="RegisterForm[username]">
</div>
<div class="form-group">
    <input type="password" class="form-control" placeholder="Пароль" required="" name="RegisterForm[password]">
</div>
<button type="submit" class="btn btn-primary block full-width m-b">
    <?= Yii::t('app', 'Register') ?>
</button>

<small>&nbsp;</small>

<p class="text-muted text-center">
    <small><?= Yii::t('app', 'Already have an account?') ?></small>
</p>
<?= Html::a(Yii::t('app', 'Login'), ['/site/login'], ['class' => 'btn btn-sm btn-white btn-block']) ?>

<?php ActiveForm::end() ?>
