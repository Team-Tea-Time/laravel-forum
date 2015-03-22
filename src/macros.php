<?php

Form::macro('inline', function($route, $form = array(), $link = array()){
    $form += array(
        'method'      => 'POST',
        'class'       => 'inline',
        'url'         => $route
    );

    $link += array(
        'class'       => '',
        'attributes'  => '',
        'label'       => 'Submit'
    );

    $output = Form::open($form)
        . "<a href=\"#\" class=\"{$link['class']}\" data-submit {$link['attributes']}>{$link['label']}</a>"
        . Form::close();

    return $output;
});
