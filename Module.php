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
		$user->passwordChangeUrl = ['' . $this->id . '/user/password/index'];

		//error
		Yii::$app->errorHandler->errorAction = '/' . $this->id . '/default/error';

		//check password change
		if (!$user->getIsGuest() && $user->getIdentity()->passwordChange)
			$user->passwordChangeRequired();
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
		$items = ['<li role="separator" class="divider"></li>', '<li role="separator" class="divider"></li>', '<li role="separator" class="divider"></li>'];
		$securityItems = [];
		$catalogItems = [];
		foreach ($this->modules as $module) {
			$class = '';
			if (is_string($module)) {
				$class = $module;
			} elseif (is_array($module)) {
				$class = $module['class'];
			} elseif ($module instanceof \yii\base\Module) {
				$class = $module::className();
			}
			if (!empty($class) && method_exists($class, 'getMenu')) {
				switch ($class) {
					case 'cms\user\backend\Module':
						$securityItems = $class::getMenu($base);
						break;
					case 'cms\catalog\backend\Module':
						$catalogItems = $class::getMenu($base);
						break;
					default:
						$items = array_merge($items, $class::getMenu($base));
						break;
				}
			}
		}
		$items[] = '<li role="separator" class="divider"></li>';
		$items[] = '<li role="separator" class="divider"></li>';
		$items[] = '<li role="separator" class="divider"></li>';

		//separators
		$isPrev = true;
		foreach ($items as $key => $value) {
			if (is_string($value)) {
				if ($isPrev)
					unset($items[$key]);
				$isPrev = true;
			} else {
				$isPrev = false;
			}
		}
		if (!empty($items)) {
			$key = end((array_keys($items)));
			if (is_string($items[$key]))
				unset($items[$key]);
		}

		$modulesMenu = [];
		if (!empty($items)) {
			$modulesMenu = [
				[
					'label' => Yii::t('cms', 'Modules'),
					'items' => $items,
				],
			];
		}

		Yii::$app->params['menu-modules'] = array_merge($modulesMenu, $catalogItems, $securityItems);
		Yii::$app->params['menu-user'] = $this->getModule('user')->getUserMenu($base);
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
