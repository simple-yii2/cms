<?php

namespace cms\components;

use Yii;

/**
 * Backend CMS module
 * 
 * Every backend CMS modules supports:
 * - checking database with [[cmsDatabase]] function
 * - making menu for CMS backend with [[cmsMenu]] function
 * - check roles and permissions with [[cmsSecurity]] function
 */
class BackendModule extends BaseModule
{

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();

		static::cmsDatabase();
		static::cmsSecurity();
	}

	/**
	 * Checking database
	 * 
	 * The database schema is placed in [[/moduleName/schema]] directory
	 * Filename of the schema is complies to [[yii\db\Connection::driverName]] with `.sql` extension
	 * 
	 * @return void
	 */
	protected static function cmsDatabase()
	{
		$db = Yii::$app->db;

		$filename = static::getDirname() . '/schema/' . $db->driverName . '.sql';
		$content = @file_get_contents($filename);
		if ($content === false)
			return;

		foreach (explode(';', $content) as $s) {
			if (trim($s) !== '')
				$db->createCommand($s)->execute();
		}
	}

	protected static function cmsSecurity()
	{

	}

	/**
	 * Making module menu for CMS
	 * 
	 * @param string $base base path for making url routes
	 * @return array
	 */
	protected static function cmsMenu($base)
	{
		return [];
	}

}
