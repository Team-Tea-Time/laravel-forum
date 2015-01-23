<?php namespace Riari\Forum\Libraries;

class Utils {

  public static function toggleProperty($item, $property)
  {
    $item->$property = !$item->$property;
    $item->save();
  }

}
