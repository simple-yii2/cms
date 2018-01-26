<?php

namespace cms;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

use cms\components\BackendModule;

/**
 * Simple CMS main module
 */
class Module extends BackendModule
{

	/**
	 * @inheritdoc
	 */
	public $layout = 'main';

	/**
	 * @var array Config that appling to backend modules
	 */
	public $modulesConfig = [];

	/**
	 * @var array custom cms (backend) modules config.
	 */
	public $customModules = [];

	/**
	 * @inheritdoc
	 */
	public static function moduleName()
	{
		return 'cms';
	}

	/**
	 * @inheritdoc
	 */
	protected static function getDirname()
	{
		return __DIR__;
	}

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();

		$this->setApplicationSettings();
		$this->checkModules();
		$this->checkPasswordChange();

		$this->makeMenu();
	}

	/**
	 * Change application settings
	 * @return void
	 */
	private function setApplicationSettings()
	{
		$app = Yii::$app;

		//application name
		$app->name = 'simple-yii2/cms';

		//application home url
		$app->homeUrl = ['/' . $this->id . '/default/index'];

		//original bootstrap theme
		$app->assetManager->bundles['yii\bootstrap\BootstrapAsset']['sourcePath'] = '@bower/bootstrap/dist';

		//error page
		$app->errorHandler->errorAction = '/' . $this->id . '/default/error';

		//login and password change
		$user = Yii::$app->getUser();
		$user->loginUrl = ['/' . $this->id . '/user/login/index'];
		if ($user->hasProperty('passwordChangeUrl'))
			$user->passwordChangeUrl = ['' . $this->id . '/user/password/index'];
	}

	/**
	 * Check modules, that may be used in CMS
	 * @return void
	 */
	protected function checkModules()
	{
		//add exists modules
		$modules = [];
		foreach (require(__DIR__ . '/config/modules.php') as $name => $module) {
			if (class_exists($module))
				$modules[$name] = array_merge(['class' => $module], ArrayHelper::getValue($this->modulesConfig, $name, []));
		}

		//add custom modules
		$modules = array_merge($modules, $this->customModules);

		//apply
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
	 * Checking if user needs to change password
	 * @return void
	 */
	private function checkPasswordChange()
	{
		$user = Yii::$app->getUser();
		if (!$user->getIsGuest() && $user->getIdentity()->passwordChange && $user->passwordChangeRequired())
			Yii::$app->end();
	}

	/**
	 * Building main menu
	 * @return void
	 */
	protected function makeMenu()
	{
		//base path for routes
		$base = '/' . $this->id;

		$catalogItems = [];
		$paymentItems = [];
		$securityItems = [];

		//modules items
		$items = [];
		foreach ($this->modules as $module) {
			if ($module instanceof \cms\catalog\backend\Module) {
				$catalogItems = $module->cmsMenu($base);
			} elseif ($module instanceof \cms\payment\backend\Module) {
				$paymentItems = $module->cmsMenu($base);
			} elseif ($module instanceof \cms\user\backend\Module) {
				$securityItems = $module->cmsMenu($base);
			} elseif ($module instanceof \cms\components\BackendModule) {
				$items = array_merge($items, $module->cmsMenu($base));
			}
		}

		//removes duplicate separators
		//removes separator from the beginning and end of the list
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

		//modules menu
		$modulesMenu = [];
		if (!empty($items)) {
			$modulesMenu = [
				[
					'label' => Yii::t('cms', 'Modules'),
					'items' => $items,
				],
			];
		}

		Yii::$app->params['menu'] = array_merge($modulesMenu, $catalogItems, $paymentItems, $securityItems);
		Yii::$app->params['menu-user'] = $this->getModule('user')->cmsUserMenu($base);
	}

}
