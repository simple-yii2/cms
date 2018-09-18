<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$title = Yii::t('user', 'Login');

$this->title = Yii::$app->name;

?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'options' => ['class' => 'login-form panel panel-default'],
]); ?>

    <div class="panel-heading"><h4><?= Html::encode($title) ?></h4></div>

    <div class="panel-body">
        <div class="row">
            <div class="col-xs-12 login-email"><?= Html::activeTextInput($model, 'email', ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('email')]) ?></div>
            <div class="col-xs-12 login-password"><?= Html::activePasswordInput($model, 'password', ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('password')]) ?></div>
            <div class="col-xs-12 login-remember-me"><?= Html::activeCheckbox($model, 'rememberMe') ?></div>
            <div class="col-xs-12 login-button"><?= Html::submitButton(Yii::t('user', 'Login'), ['class' => 'btn btn-primary']) ?></div>
        </div>
    </div>

<?php ActiveForm::end() ?>
