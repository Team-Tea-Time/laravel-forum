@extends ('forum::master', ['category' => null, 'thread' => null])

@section ('content')
    <ul class="list-group sortable">
        @foreach ($categories as $category)
            @include ('forum::category.partials.sortable-list-item')
        @endforeach
    </ul>

    <hr>

    <div id="alert-changes-applied" class="alert alert-success" style="display: none;" role="alert">
        {{ trans('forum::general.changes_applied') }}
    </div>

    <button type="button" class="btn btn-primary" id="save" disabled>
        {{ trans('forum::general.save') }}
    </button>

    <script>
    var categories = [];
    var sortables = $('.sortable');
    var alertChangesApplied = $('#alert-changes-applied');
    var buttonSave = $('#save');

    function serialize(sortable) {
        var data = [];
        var children = [].slice.call(sortable.children);
        for (var i in children) {
            var nested = children[i].querySelector('ul');
            data.push({
                id: children[i].dataset['id'],
                children: nested ? serialize(nested) : []
            });
        }
        return data
    }

    sortables.each(function (i) {
        new Sortable($(this)[0], {
            group: 'categories',
            animation: 150,
    	    easing: 'cubic-bezier(1, 0, 0, 1)',
            fallbackOnBody: true,
            swapThreshold: 0.65,
            onStart: function (evt) {
                alertChangesApplied.hide();
            },
            onEnd: function (evt) {
                categories = serialize(sortables.first()[0]);
                buttonSave.attr('disabled', false);
            }
        });
    });

    buttonSave.click(function(e) {
        buttonSave.attr('disabled', true);

        $.ajax('{{ url(config('forum.frontend.router.prefix')) }}/api/bulk/category/position', {
            type: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify({ categories: categories }),
            beforeSend: function (xhr) {
                xhr.setRequestHeader ('Authorization', 'Bearer {{ auth()->user()->api_token }}');
            },
            success: function () {
                alertChangesApplied.fadeIn();
            }
        });
    });
    </script>

    <style>
    ul.sortable ul.sortable {
        margin-top: 5px;
        padding: 0;
        min-height: 20px;
    }
    </style>
@stop