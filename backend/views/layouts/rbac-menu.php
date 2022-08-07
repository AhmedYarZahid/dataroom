<?php

use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

$controller = $this->context;
$menus = \Yii::$app->getModule('rbac')->menus;
$route = $controller->route;
foreach ($menus as $i => $menu) {
    $menus[$i]['active'] = strpos($route, trim($menu['url'][0], '/')) === 0;
}
$this->params['nav-items'] = $menus;
?>
<?php $this->beginContent('@app/views/layouts/main.php') ?>
<div class="row">
    <div class="col-lg-3">
        <div id="manager-menu" class="list-group">
            <?php
            foreach ($menus as $id => $menu) {
                if ($id != 'menu') {
                    $label = Html::tag('i', '', ['class' => 'glyphicon glyphicon-chevron-right pull-right']) .
                        Html::tag('span', Html::encode($menu['label']), []);
                    $active = !empty($menu['active']) ? ' active' : '';
                    echo Html::a($label, $menu['url'], [
                        'class' => 'list-group-item' . $active,
                    ]);
                }
            }
            ?>
        </div>
    </div>
    <div class="col-lg-9">
        <?= $content ?>
    </div>
</div>
<?php $this->endContent(); ?>
