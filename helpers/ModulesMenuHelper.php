<?php

namespace cms\helpers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class ModulesMenuHelper
{

    public static function prepareListItems($items, &$active = null)
    {
        $active = false;
        $route = '/' . Yii::$app->getRequest()->resolve()[0];

        $r = [];
        foreach ($items as $item) {
            if (is_string($item)) {
                $r[] = $item;
            } else {
                $itemOptions = [];

                //label
                $label = ArrayHelper::getValue($item, 'label', '');
                if (ArrayHelper::getValue($item, 'encode', true)) {
                    $label = Html::encode($label);
                }

                //link
                $url = ArrayHelper::getValue($item, 'url', '#');
                $link = Html::a($label, $url);
                if (ArrayHelper::getValue($url, 0) == $route) {
                    Html::addCssClass($itemOptions, 'active');
                    $active = true;
                }

                //items
                $subitems = '';
                if (isset($item['items'])) {
                    $subitems = Html::tag('ul', implode('', self::prepareListItems($item['items'], $a)));
                    Html::addCssClass($itemOptions, 'subitems');
                    if ($a) {
                        Html::addCssClass($itemOptions, 'open');
                        $active = true;
                    }
                }

                $r[] = Html::tag('li', $link . $subitems, $itemOptions);
            }
        }

        return $r;
    }

}
