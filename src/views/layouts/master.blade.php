<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <script>window.jQuery || document.write('<script src="//code.jquery.com/jquery-1.11.2.min.js">\x3C/script>')</script>

    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

    @if(config('forum.preferences.bbcode.enabled'))
        <link rel="stylesheet" href="{{asset('vendor/riari/laravel-forum/js/wysibb/theme/default/wbbtheme.css')}}" />
    @endif

    <title>{!! trans('forum::base.home_title') !!}</title>
</head>
<body>
    <div class="container">
        @include('forum::partials.alerts')

        @yield('content')
    </div>

    <script>
        $('a[data-confirm]').click(function(event) {
            if (!confirm('{!! trans('forum::base.generic_confirm') !!}')) {
                event.stopImmediatePropagation();
                event.preventDefault();
            }
        });
        $('[data-method]:not(.disabled)').click(function(event) {
            $('<form action="' + $(this).attr('href') + '" method="POST">' +
            '<input type="hidden" name="_method" value="' + $(this).data('method') + '">' +
            '<input type="hidden" name="_token" value="{!! Session::getToken() !!}"' +
            '</form>').submit();

            event.preventDefault();
        });
    </script>

    @if(config('forum.preferences.bbcode.enabled'))
        <script src="{{asset('vendor/riari/laravel-forum/js/wysibb/jquery.wysibb.min.js')}}"></script>
        <script>
            $(document).ready(function() {
                var wbbOpt = {
                    buttons: "{{
                        (config('forum.preferences.bbcode.tags.b')?'bold,':'').
                        (config('forum.preferences.bbcode.tags.i')?'italic,':'').
                        (config('forum.preferences.bbcode.tags.u')?'underline,':'').
                        (config('forum.preferences.bbcode.tags.s')?'strike,':'').
                        (config('forum.preferences.bbcode.tags.sup')?'sup,':'').
                        (config('forum.preferences.bbcode.tags.sub')?'sub,':'')
                    }},|,{{
                        (config('forum.preferences.bbcode.tags.img')?'img,':'').
                        (config('forum.preferences.bbcode.tags.video')?'video,':'').
                        (config('forum.preferences.bbcode.tags.url')?'link,':'')
                    }},|,{{
                        (config('forum.preferences.bbcode.tags.list')?'bullist,':'').
                        (config('forum.preferences.bbcode.tags.olist')?'numlist,':'')
                    }},|,{{
                        (config('forum.preferences.bbcode.tags.font')?'fontfamily,':'').
                        (config('forum.preferences.bbcode.tags.size')?'fontsize,':'').
                        (config('forum.preferences.bbcode.tags.color')?'fontcolor,':'')
                    }},|,{{
                        (config('forum.preferences.bbcode.tags.left')?'justifyleft,':'').
                        (config('forum.preferences.bbcode.tags.center')?'justifycenter,':'').
                        (config('forum.preferences.bbcode.tags.right')?'justifyright,':'')
                    }},|,{{
                        (config('forum.preferences.bbcode.tags.quote')?'quote,':'').
                        (config('forum.preferences.bbcode.tags.code')?'code,':'').
                        (config('forum.preferences.bbcode.tags.table')?'table,':'')
                    }},|,{{
                        (config('forum.preferences.emoticons.enabled')?'smilebox,':'')
                    }},|,removeFormat",
                    smileList:
                            [
                                @foreach(config('forum.preferences.emoticons.list') as $name => $smileys)
                                    {title:CURLANG.{{$name}}, img: '<img src="{{asset(config('forum.preferences.emoticons.path').$name.'.png')}}" class="sm">', bbcode:" :{{$name}}: "},
                                @endforeach
                            ],
                    allButtons: {
                        quote: {
                            transform: {

                                '{!! preg_replace( "/\r|\n/", "", view('forum::bbcode.quote')->with(['content'=>'{SELTEXT}'])->render()) !!}':'[quote]{SELTEXT}[/quote]',
                                '{!! preg_replace( "/\r|\n/", "", view('forum::bbcode.quote')->with(['content'=>'{SELTEXT}','author'=>'{AUTHOR}'])->render()) !!}':'[quote={AUTHOR}]{SELTEXT}[/quote]',
                            }
                        },
                        video: {
                            transform: {
                                '{!! preg_replace( "/\r|\n/", "", view('forum::bbcode.video')->with(['url'=>'http://www.youtube.com/embed/{SRC}','player'=>'iframe'])->render()) !!}':'[youtube]{SRC}[/youtube]'
                            }
                        },
                        numlist : {
                            transform : {
                                '<ol>{SELTEXT}</ol>':"[olist]{SELTEXT}[/olist]",
                                '<li>{SELTEXT}</li>':"[*]{SELTEXT}[/*]"
                            }
                        },

                        // Adapt size options to the ones accepted by decoda.
                        fs_verysmall: {
                            transform: {
                                '<font size="1">{SELTEXT}</font>':'[size=10]{SELTEXT}[/size]'
                            }
                        },
                        fs_small: {
                            transform: {
                                '<font size="2">{SELTEXT}</font>':'[size=12]{SELTEXT}[/size]'
                            }
                        },
                        fs_normal: {
                            transform: {
                                '<font size="3">{SELTEXT}</font>':'[size=14]{SELTEXT}[/size]'
                            }
                        },
                        fs_big: {
                            transform: {
                                '<font size="4">{SELTEXT}</font>':'[size=17]{SELTEXT}[/size]'
                            }
                        },
                        fs_verybig: {
                            transform: {
                                '<font size="6">{SELTEXT}</font>':'[size=20]{SELTEXT}[/size]'
                            }
                        },
                    }
                };
                $("[data-add-wysibb]").wysibb(wbbOpt)

                $('a[data-forumquickquote]').click(function(event) {
                    var jqQuickReplyTextarea = $('#quick-reply').find('textarea[name="content"]');
                    var postID = $(this).data('forumquickquote');
                    var postQuote = $('#forumPostQuote-'+postID).html().trim();
                    jqQuickReplyTextarea.insertAtCursor(postQuote,true)
                });
            })
        </script>
    @endif

</body>
</html>
