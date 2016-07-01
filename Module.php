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

		Yii::$app->name = 'simple-yii/cms';
		Yii::$app->homeUrl = ['/' . $this->id . '/default/index'];

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
		$menu = [];

		$base = '/' . $this->id;

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
				$menu = array_merge($menu, $class::getMenu($base));
		}

		if (!Yii::$app->user->isGuest) {
			$menu[] = [
				'label' => Yii::t('user', 'Logout') . '(' . Yii::$app->user->identity->username . ')',
				'url' => ["$base/user/logout/index"],
			];
		}

		Yii::$app->params['menu'] = $menu;
	}

}
