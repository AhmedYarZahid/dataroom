<?php
Yii::setAlias('common', dirname(__DIR__));
Yii::setAlias('frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('console', dirname(dirname(__DIR__)) . '/console');

Yii::setAlias('uploads-webroot', '@backend/web');
Yii::setAlias('uploads', '@backend/web/uploads');
Yii::setAlias('uploads/images-rel', '/uploads/images');
Yii::setAlias('uploads/editor-rel', '/uploads/editor');
Yii::setAlias('uploads/documents-rel', '/uploads/documents');
Yii::setAlias('uploads/news-rel', '/uploads/news');
Yii::setAlias('uploads/office-members-rel', '/uploads/office-members');

Yii::$container->set('kartik\grid\GridView', [
    'hover' => true,
    'condensed' => false,
    'striped' => true,
    'bordered' => true,
    'resizableColumns' => false,
    'pjax' => true,
]);

// You CAN'T extend new class from old one, because it's totally replace of old class by new one
//Yii::$classMap['yii\helpers\FileHelper'] = '@app/helpers/FileHelper.php';

// Seems you CAN extend new class from old one
/*Yii::$container->set(
    'yii\validators\NumberValidator',
    ['class' => 'common\validators\NumberValidator']
);*/