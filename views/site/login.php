<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model app\models\forms\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', 'Login');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php ActiveForm::begin(['options' => ['class' => 'm-t'], 'action' => ['/site/login']]) ?>
<div class="form-group">
    <input type="email" class="form-control" placeholder="E-mail" required="" name="LoginForm[username]">
</div>
<div class="form-group">
    <input type="password" class="form-control" placeholder="Пароль" required="" name="LoginForm[password]">
</div>
<button type="submit" class="btn btn-primary block full-width m-b">
    <?= Yii::t('app', 'Login') ?>
</button>

<a href="/site/forget">
    <small><?= Yii::t('app', 'Forget password?') ?></small>
</a>

<p class="text-muted text-center">
    <small><?= Yii::t('app', 'Do not have an account?') ?></small>
</p>
<a class="btn btn-sm btn-white btn-block" href="/site/register">
    <?= Yii::t('app', 'Create an account') ?>
</a>
<?php ActiveForm::end() ?>
