<?php

namespace common\widgets\imperavi;

use yii\web\AssetBundle;

/**
 * Widget asset bundle
 */
class Asset extends AssetBundle
{
	/**
	 * @inheritdoc
	 */
	public $sourcePath = '@common/widgets/imperavi/assets';

	/**
	 * @var string Redactor language
	 */
	public $language;

	/**
	 * @var array Redactor plugins array
	 */
	public $plugins = [];

	/**
	 * @inheritdoc
	 */
	public $css = [
		'redactor.css'
	];

	/**
	 * @inheritdoc
	 */
	public $js = [
	    'redactor.min.js'
	];

	/**
	 * @inheritdoc
	 */
	public $depends = [
		'yii\web\JqueryAsset'
	];

	/**
	 * Register asset bundle language files and plugins.
	 */
	public function registerAssetFiles($view)
	{
		if ($this->language !== null) {
			$this->js[] = 'lang/' . $this->language . '.js';
		}
		if (!empty($this->plugins)) {
			foreach ($this->plugins as $plugin) {
				if (in_array($plugin, ['alignment', 'clips'])) {
					$this->css[] = 'plugins/' . $plugin . '/' . $plugin . '.css';
				}
				$this->js[] = 'plugins/' . $plugin . '/' . $plugin .'.js';
			}
		}
		parent::registerAssetFiles($view);
	}
}
