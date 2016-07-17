<?php

namespace cms\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Alert that shows messages from session flash.
 */
class Alert extends Widget
{

	/**
	 * @inheritdoc
	 */
	public function run()
	{
		$type = [
			'success' => 'alert alert-success',
			'info' => 'alert alert-info',
			'warning' => 'alert alert-warning',
			'error' => 'alert alert-danger',
		];

		foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
			if (array_key_exists($key, $type))
				echo Html::tag('div', $message, ['class' => $type[$key], 'role' => 'alert']);
		}
	}

}
