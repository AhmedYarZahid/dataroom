<?php

/* @var $data array*/
/* @var $breadcrumbs string*/

if ($title) {
    $this->title = $title;
}

if ($breadcrumbs) {
    $this->params['breadcrumbs'][] = $breadcrumbs;
}

?>


<?php if (!empty($data)): ?>
    <div class="panel-group" id="accordion">
        <?php foreach ($data as $value) :?>
            <!-- Panel -->
            <div class="panel panel-default">
                <!-- Header -->
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse-faq-category-<?= $value['category']->id ?>"><?= $value['category']->title ?></a>
                    </h4>
                </div>
                <div id="<?= 'collapse-faq-category-' . $value['category']->id ?>" class="panel-collapse collapse">
                    <!-- Content -->
                    <div class="panel-body">
                        <?php foreach ($value['items'] as $item): ?>
                            <div class="panel panel-default">
                                <!-- Header -->
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="<?= 'collapse-faq-category-' . $value['category']->id ?>" href="#collapse-faq-item-<?= $item->id ?>"><?= $item->question ?></a>
                                    </h4>
                                </div>
                                <div id="<?= 'collapse-faq-item-' . $item->id ?>" class="panel-collapse collapse">
                                    <!-- Content -->
                                    <div class="panel-body">
                                        <?= $item->answer ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach;?>
    </div>
<?php else:?>
    <h4>NO DATA</h4>
<?php endif; ?>

