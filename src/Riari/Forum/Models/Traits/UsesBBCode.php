<?php namespace Riari\Forum\Models\Traits;

use Decoda\Filter\BlockFilter;
use Decoda\Filter\CodeFilter;
use Decoda\Filter\EmailFilter;
use Decoda\Filter\ImageFilter;
use Decoda\Filter\ListFilter;
use Decoda\Filter\QuoteFilter;
use Decoda\Filter\TableFilter;
use Decoda\Filter\TextFilter;
use Decoda\Filter\UrlFilter;
use Decoda\Filter\VideoFilter;
use Decoda\Hook\CensorHook;
use Decoda\Hook\ClickableHook;
use Decoda\Hook\EmoticonHook;
use Riari\Forum\Libraries\DecodaBladeEngine;
use Decoda\Decoda;
use Decoda\Filter\DefaultFilter;
use Riari\Forum\Libraries\DecodaLaravelConfigLoader;

trait UsesBBCode {


    /**
     * Gets the post-content and runs it through Decoda
     * to decode BBcode, parse emoticons and links, or censor words.
     *
     * @return string
     */
    public function getDecodedContentAttribute()
    {
        // First make sure, the content is HTML entities encoded.
        $content = e($this->content);

        // Instantiate the Decoda-object.
        // We use null as the configPath to keep Decoda from using
        // it's built in (emoticon- and censorship-configs),
        // since we load these from our own laravel-config.
        $code = new Decoda($content, ['configPath' => null]);
        $code->setEngine(new DecodaBladeEngine());
        $code->setStrict(false);

        // Add filter for default tags, if at least one is enabled.
        if (
            config('forum.preferences.bbcode.tags.b') ||
            config('forum.preferences.bbcode.tags.i') ||
            config('forum.preferences.bbcode.tags.u') ||
            config('forum.preferences.bbcode.tags.s') ||
            config('forum.preferences.bbcode.tags.sup') ||
            config('forum.preferences.bbcode.tags.sub')
        ) {
            $code->addFilter(new DefaultFilter());
        }

        // Add filter for block-quotes, if enabled.
        if (config('forum.preferences.bbcode.tags.quote')) {
            $code->addFilter(new QuoteFilter());
        }

        // Add filter for image-tags, if enabled.
        if (config('forum.preferences.bbcode.tags.img') || config('forum.preferences.emoticons.enabled')) {
            $code->addFilter(new ImageFilter());
        }

        // Add filter for video-tags, if enabled.
        if (config('forum.preferences.bbcode.tags.video')) {
            $code->addFilter(new VideoFilter());
        }

        // Add filter for url-tags, if enabled.
        if (config('forum.preferences.bbcode.tags.url') || config('forum.preferences.auto_links')) {
            $code->addFilter(new UrlFilter());
        }

        // Add filter for list tags, if enabled.
        if (
            config('forum.preferences.bbcode.tags.list') ||
            config('forum.preferences.bbcode.tags.olist')
        ) {
            $code->addFilter(new ListFilter());
        }

        // Add filter for font tags, if enabled.
        if (
            config('forum.preferences.bbcode.tags.font') ||
            config('forum.preferences.bbcode.tags.size') ||
            config('forum.preferences.bbcode.tags.color')
        ) {
            $code->addFilter(new TextFilter());
        }

        // Add filter for text-alignment.
        if (
            config('forum.preferences.bbcode.tags.left') ||
            config('forum.preferences.bbcode.tags.center') ||
            config('forum.preferences.bbcode.tags.right')
        ) {
            $code->addFilter(new BlockFilter());
        }

        // Add filter for block-quotes, if enabled.
        if (config('forum.preferences.bbcode.tags.code')) {
            $code->addFilter(new CodeFilter());
        }

        // Add filter for block-quotes, if enabled.
        if (config('forum.preferences.bbcode.tags.table')) {
            $code->addFilter(new TableFilter());
        }

        // Add hook for emoticons, if enabled.
        if (config('forum.preferences.emoticons.enabled')) {
            $emoticonHook = new EmoticonHook(array('path' => config('forum.preferences.emoticons.path')));
            $emoticonHook->addLoader(new DecodaLaravelConfigLoader('forum.preferences.emoticons.list'));
            $code->addHook($emoticonHook);
        }

        // Add hook for censorship, if enabled.
        if (config('forum.preferences.censorship.enabled')) {
            $censorshipHook = new CensorHook();
            $censorshipHook->addLoader(new DecodaLaravelConfigLoader('forum.preferences.censorship.list'));
            $code->addHook($censorshipHook);
        }

        // Add hook to automatically convert URLs and emails (not wrapped in tags) into clickable links.
        if (config('forum.preferences.auto_links')) {
            $code->addFilter(new EmailFilter());
            $code->addHook(new ClickableHook());
        }

        $content = $code->parse();

        // Finally convert nl2br.
        $content = nl2br($content);

        return $content;
    }

    /**
     * Returns a full BBcode quote for this post.
     *
     * @return string
     */
    public function getQuoteAttribute()
    {
        return '[quote='.$this->authorName.']'.$this->content.'[/quote]';
    }

    /**
     * Returns the route to a reply to the current post (for usage with "Quote Reply").
     *
     * @return mixed
     */
    public function getReplyRouteAttribute()
    {
        return $this->getRoute('forum.get.reply.thread');
    }

}
