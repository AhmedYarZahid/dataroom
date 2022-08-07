<?php

function d($var,$caller=null)
{
    if(!isset($caller)){
        $backtrace = debug_backtrace(1);
        $caller = array_shift($backtrace);
    }
    echo '<code>File: '.$caller['file'].' / Line: '.$caller['line'].'</code>';
    echo '<pre>';
    yii\helpers\VarDumper::dump($var, 10, true);
    echo '</pre>';
}

function dd($var)
{
    $backtrace = debug_backtrace(1);
    $caller = array_shift($backtrace);
    d($var,$caller);
    die();
}