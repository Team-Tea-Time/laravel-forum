<?php namespace Atrakeur\Forum\Models;

abstract class AbstractForumBaseModel extends \Eloquent {

	protected function rememberAttribute($item, $function) {
		$cacheItem = get_class($this).$this->id.$item;

		$value = \Cache::rememberForever($cacheItem, $function);

		return $value;
	}

	protected function clearAttributeCache() {
		foreach ($this->appends as $attribute) {
			$cacheItem = get_class($this).$this->id.$attribute;
			\Cache::forget($cacheItem);
		}
	}

}