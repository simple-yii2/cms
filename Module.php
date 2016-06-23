<?php

namespace cms;

use Yii;

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

		$this->checkModules();

		//user
		$user = Yii::$app->getUser();
		$user->loginUrl = ['/' . $this->id . '/user/login/index'];

		//error
		Yii::$app->errorHandler->errorAction = '/' . $this->id . '/default/error';
	}

	/**
	 * Check modules, that may be used in CMS and building main menu.
	 * @return void
	 */
	protected function checkModules()
	{
		$modules = [];
		$menu = [];

		$base = '/' . $this->id;

		foreach (require(__DIR__ . '/config/modules.php') as $name => $module) {
			if (class_exists($module)) {
				$modules[$name] = $module;
				if (method_exists($module, 'getMenu'))
					$menu = array_merge($menu, $module::getMenu($base));
			}
		}


		if (!Yii::$app->user->isGuest) {
			$menu[] = [
				'label' => Yii::t('user', 'Logout') . '(' . Yii::$app->user->identity->username . ')',
				'url' => ["$base/user/logout/index"],
			];
		}

		$this->modules = $modules;
		Yii::$app->params['menu'] = $menu;
	}

}
