<?php

namespace cms\components;

use Yii;
use yii\base\Module;

/**
 * Base CMS module
 * 
 * Every CMS modules supports translation adding with [[cmsTranslation]] function
 */
class BaseModule extends Module
{

	/**
	 * @var string Class dirname
	 */
	private static $_dirname;

	/**
	 * Return module name that uses for translation adding
	 * @return string
	 */
	public static function moduleName()
	{
		return '';
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
		if (self::$_dirname !== null)
			return self::$_dirname;

		$class = new \ReflectionClass(self::className());
		return self::$_dirname = dirname(dirname($class->getFileName()));
	}

	/**
	 * Adding translation to i18n
	 * 
	 * Translation is placed in [[/moduleName/messages]] directory
	 * @see yii\i18n\PhpMessageSource
	 * 
	 * @return void
	 */
	protected static function cmsTranslation()
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
