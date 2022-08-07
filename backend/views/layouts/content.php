<?php
use yii\widgets\Breadcrumbs;
use dmstr\widgets\Alert;
use yii\helpers\Html;

?>
<aside class="right-side">
    <section class="content-header">
        <h1>
            <!--<?/*= \yii\helpers\Inflector::camel2words(\yii\helpers\Inflector::id2camel($this->context->module->id)) */?>-->
            <!--<small><?/*= ($this->context->module->id !== \Yii::$app->id) ? 'Module' : '' */?></small>-->

            <?php if (isset($this->context->title)): ?>
                <?= Yii::t('admin', $this->context->title) ?>
            <?php endif ?>

            <?php if (isset($this->context->titleSmall)): ?>
                <small><?= Yii::t('admin', $this->context->titleSmall) ?></small>
            <?php endif ?>

        </h1>
        <?=
        Breadcrumbs::widget(
            [
                'encodeLabels' => false,
                'homeLink' => [
                    'label' => '<i class="fa fa-dashboard"></i>' . Yii::t('admin', 'Home'),
                    'url' => Yii::$app->homeUrl,
                ],
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]
        ) ?>
    </section>

    <section class="content">
        <?= Alert::widget() ?>
        <?= $content ?>
    </section>

    <!--<footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; My Company <?/*= date('Y') */?></p>

            <p class="pull-right"><?/*= Yii::powered() */?></p>
        </div>
    </footer>-->

</aside>