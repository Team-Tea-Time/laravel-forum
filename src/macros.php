<?php

Form::macro('inline', function($route, $form = array(), $link = array()){
  $form += array(
    'method'      => 'POST',
    'class'       => 'inline',
    'url'         => $route
  );

  $link += array(
    'class'       => '',
    'attributes'  => 'onclick="this.form.submit();"',
    'label'       => 'Submit'
  );

  $output = Form::open($form)
          . "<a href=\"#\" class=\"{$link['class']}\" {$link['attributes']}>{$link['label']}</a>"
          . Form::close();

  return $output;
});
