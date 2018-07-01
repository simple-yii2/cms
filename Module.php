<?php

namespace cms;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\rbac\BaseManager;
use cms\components\BackendModule;
use cms\user\common\components\User;

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

        $this->checkConfig();
        $this->checkModules();
        $this->setApplicationSettings();
        $this->checkPasswordChange();

        $this->makeMenu();
    }

    /**
     * Check application configuration
     * @return type
     */
    private function checkConfig()
    {
        //auth manager
        $auth = Yii::$app->getAuthManager();
        if (!($auth instanceof BaseManager))
            throw new InvalidConfigException('You should to configure "authManager" application component inherited from yii\rbac\BaseManager class.');

        //user application component
        $user = Yii::$app->getUser();
        if (!($user instanceof User))
            throw new InvalidConfigException('You should to set "user" application component inherited from cms\user\common\components\User class.');
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
        $modules = [];

        //add custom modules
        $modules = array_merge($modules, $this->customModules);

        //add exists modules
        foreach (require(__DIR__ . '/config/modules.php') as $name => $module) {
            if (class_exists($module))
                $modules[$name] = array_merge(['class' => $module], ArrayHelper::getValue($this->modulesConfig, $name, []));
        }

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
     * Building menus
     * @return void
     */
    protected function makeMenu()
    {
        //base path for routes
        $base = '/' . $this->id;

        //modules menu
        $items = [];
        foreach ($this->modules as $module) {
            if ($module instanceof BackendModule) {
                $item = $module->cmsMenu();
                if (!empty($item)) {
                    $items[] = $item;
                }
            }
        }

        //cms menus
        Yii::$app->params['menu-modules'] = $this->normalizeItems($items);
        Yii::$app->params['menu-user'] = $this->normalizeItems($this->getModule('user')->cmsUserMenu());
    }

    /**
     * Normalize menu items (url route)
     * @param array $items 
     * @return array
     */
    protected function normalizeItems($items)
    {
        //base route
        $base = '/' . $this->id;

        //process items
        foreach ($items as $key => $item) {
            //url
            $route = ArrayHelper::getValue($item, ['url', 0]);
            if ($route !== null) {
                if ($route[0] != '/') {
                    $route = '/' . $route;
                }
                $item['url'][0] = $base . $route;
            }
            //normolize children items
            if (isset($item['items'])) {
                $item['items'] = $this->normalizeItems($item['items']);
            }
            //set normalized item
            $items[$key] = $item;
        }
        return $items;
    }

}
