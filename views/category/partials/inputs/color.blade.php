<div class="form-group">
    <label for="color">{{ trans('forum::general.color') }}</label>
    <div class="pickr"></div>
    <input type="hidden" value="{{ isset($category->color) ? $category->color : (old('color') ?? config('forum.frontend.default_category_color')) }}" name="color">
</div>