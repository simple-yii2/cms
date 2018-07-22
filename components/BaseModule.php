<?php

namespace cms\components;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Module;
use yii\helpers\ArrayHelper;

/**
 * Base CMS module
 * 
 * Every CMS modules supports translation adding with [[cmsTranslation]] function
 */
class BaseModule extends Module
{

    /**
     * Return module name that uses for translation adding
     * @return string
     */
    public static function moduleName()
    {
        $className = static::className();

        $name = ArrayHelper::getValue(array_reverse(explode('\\', $className)), 2);
        if ($name === null) {
            throw new InvalidConfigException('The function "moduleName" is not defined in "' . $className . '".');
        }

        return $name;
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        static::cmsTranslation();
    }

    /**
     * Current dirname getter
     * @return string
     */
    protected static function getDirname()
    {
        $class = new \ReflectionClass(static::className());
        return dirname(dirname($class->getFileName()));
    }

    /**
     * Adding translation to i18n
     * 
     * Translation is placed in [[/moduleName/messages]] directory
     * @see yii\i18n\PhpMessageSource
     * 
     * @return void
     */
    public static function cmsTranslation()
    {
        $name = static::moduleName();
        if (empty($name))
            return;

        if (isset(Yii::$app->i18n->translations[$name]))
            return;

        Yii::$app->i18n->translations[$name] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => static::getDirname() . '/messages',
        ];
    }

}
