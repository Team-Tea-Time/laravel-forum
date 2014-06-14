@include('forum::partials.pathdisplay')->with(compact('parentCategory', 'category', 'topic'))

<p class="lead">
	Vous postez dans @include('forum::partials.pathdisplay')->with(compact('parentCategory', 'category', 'topic'))
</p>

{{ Form::open(array('url' => $actionUrl)) }}

Titre: {{ Form::text('title') }}
Message: {{ Form::textarea('data') }}

{{ Form::submit('Send') }}
{{ Form::close() }}
