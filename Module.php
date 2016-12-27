<?php

namespace cms;

use Yii;
use yii\base\InvalidConfigException;

/**
 * Simple CMS main module
 */
class Module extends \yii\base\Module
{

	/**
	 * @inheritdoc
	 */
	public $layout = 'main';

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();

		self::addTranslation();

		Yii::$app->name = 'simple-yii2/cms';
		Yii::$app->homeUrl = ['/' . $this->id . '/default/index'];

		Yii::$app->assetManager->bundles['yii\bootstrap\BootstrapAsset']['sourcePath'] = '@bower/bootstrap/dist';

		if (($view = Yii::$app->view) instanceof \cms\seo\frontend\components\View)
			$view->seoEnabled = false;

		$this->checkModules();
		$this->makeMenu();

		//user
		$user = Yii::$app->getUser();
		$user->loginUrl = ['/' . $this->id . '/user/login/index'];

		//error
		Yii::$app->errorHandler->errorAction = '/' . $this->id . '/default/error';
	}

	/**
	 * Check modules, that may be used in CMS.
	 * @return void
	 */
	protected function checkModules()
	{
		//add exists modules

		$modules = [];

		foreach (require(__DIR__ . '/config/modules.php') as $name => $module) {
			if (class_exists($module))
				$modules[$name] = $module;
		}

		$this->modules = $modules;


		//init user module

		if ($this->getModule('user') === null)
			throw new InvalidConfigException('Module `user` not found.');


		//init other modules to prepare data

		foreach (array_keys($modules) as $name) {
			$this->getModule($name);
		}
	}

	/**
	 * Building main menu.
	 * @return void
	 */
	protected function makeMenu()
	{

		$base = '/' . $this->id;

		//modules
		$items = [];
		foreach ($this->modules as $module) {
			$class = '';
			if (is_string($module)) {
				$class = $module;
			} elseif (is_array($module)) {
				$class = $module['class'];
			} elseif ($module instanceof \yii\base\Module) {
				$class = $module::className();
			}
			if (!empty($class) && method_exists($class, 'getMenu'))
				$items = array_merge($items, $class::getMenu($base));
		}

		//separators
		$i = sizeof($items) - 1;
		if (is_string($items[$i]))
			unset($items[$i]);
		if (is_string($items[0]))
			unset($items[0]);

		$modulesMenu = [];
		if (!empty($items)) {
			$modulesMenu = [
				[
					'label' => Yii::t('cms', 'Modules'),
					'items' => $items,
				],
			];
		}

		//security
		$securityMenu = $this->getModule('user')->getUserMenu($base);

		//logout
		$logoutMenu = [];
		if (!Yii::$app->user->isGuest) {
			$logoutMenu[] = [
				'label' => Yii::t('user', 'Logout') . ' (' . Yii::$app->user->identity->username . ')',
				'url' => ["$base/user/logout/index"],
			];
		}
\
		Yii::$app->params['menu'] = array_merge($modulesMenu, $securityMenu, $logoutMenu);
	}

	/**
	 * Adding translation to i18n
	 * @return void
	 */
	protected static function addTranslation()
	{
		if (!isset(Yii::$app->i18n->translations['cms'])) {
			Yii::$app->i18n->translations['cms'] = [
				'class' => 'yii\i18n\PhpMessageSource',
				'sourceLanguage' => 'en-US',
				'basePath' => __DIR__ . '/messages',
			];
		}
	}

}
