@include('forum::partials.pathdisplay')->with(compact('parentCategory', 'category'))

{{ Form::open(array('url' => $actionUrl)) }}

Titre: {{ Form::text('title') }}
Message: {{ Form::textarea('data') }}

{{ Form::submit('Send') }}
{{ Form::close() }}
