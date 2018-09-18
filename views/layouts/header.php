<?php

use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use cms\helpers\ModulesMenuHelper;

NavBar::begin([
    'brandLabel' => false,
    'brandUrl' => Yii::$app->homeUrl,
    'options' => [
        'class' => 'navbar-inverse navbar-fixed-top',
    ],
    'renderInnerContainer' => false,
]);
echo Nav::widget([
    'options' => ['class' => 'navbar-nav'],
    'items' => [
        [
            'label' => '<span class="bars"><span></span><span></span><span></span></span><span class="title">' . Html::encode(Yii::t('cms', 'Modules')) . '</span>',
            'options' => ['class' => 'cms-modules-menu-toggle'],
            'encode' => false,
            'dropDownOptions' => ['class' => 'cms-modules-menu'],
            'items' => ModulesMenuHelper::prepareListItems(Yii::$app->params['menu-modules']),
        ],
    ],
]);
echo Nav::widget([
    'options' => ['class' => 'navbar-nav navbar-right'],
    'items' => Yii::$app->params['menu-user'],
]);
NavBar::end();
