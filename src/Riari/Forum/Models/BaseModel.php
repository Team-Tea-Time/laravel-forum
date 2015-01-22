<?php namespace Riari\Forum\Models;

use Cache;
use Carbon\Carbon;
use Config;
use Eloquent;

abstract class BaseModel extends Eloquent {

	protected function rememberAttribute($item, $function)
	{
		$cacheItem = get_class($this).$this->id.$item;

		$value = Cache::remember($cacheItem, Config::get('forum::preferences.cache_lifetime'), $function);

		return $value;
	}

	protected static function clearAttributeCache($model)
	{
		foreach ($model->appends as $attribute) {
			$cacheItem = get_class($model).$model->id.$attribute;
			Cache::forget($cacheItem);
		}
	}

	protected function getTimeAgo($timestamp)
	{
		return Carbon::createFromTimeStamp(strtotime($timestamp))->diffForHumans();
	}

	public function getPostedAttribute()
	{
		return $this->getTimeAgo($this->created_at);
	}

	public function getUpdatedAttribute()
	{
		return $this->getTimeAgo($this->updated_at);
	}

}
