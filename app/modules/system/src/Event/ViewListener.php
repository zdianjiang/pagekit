<?php

namespace Pagekit\System\Event;

use Pagekit\Application as App;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ViewListener implements EventSubscriberInterface
{
    /**
     * Registers view styles/scripts.
     */
    public function onKernelRequest($event)
    {
        $app   = App::getInstance();
        $debug = $app['module']['application']->config('debug');

        $app['styles']->register('codemirror', 'vendor/assets/codemirror/codemirror.css');
        $app['scripts']->register('codemirror', 'vendor/assets/codemirror/codemirror.js');
        $app['scripts']->register('jquery', 'vendor/assets/jquery/dist/jquery.min.js');
        $app['scripts']->register('lodash', 'vendor/assets/lodash/lodash.min.js');
        $app['scripts']->register('marked', 'vendor/assets/marked/marked.js');
        $app['scripts']->register('uikit', 'vendor/assets/uikit/js/uikit.min.js', 'jquery');
        $app['scripts']->register('uikit-autocomplete', 'vendor/assets/uikit/js/components/autocomplete.min.js', 'uikit');
        $app['scripts']->register('uikit-form-password', 'vendor/assets/uikit/js/components/form-password.min.js', 'uikit');
        $app['scripts']->register('uikit-htmleditor', 'vendor/assets/uikit/js/components/htmleditor.min.js', ['uikit', 'marked', 'codemirror']);
        $app['scripts']->register('uikit-pagination', 'vendor/assets/uikit/js/components/pagination.min.js', 'uikit');
        $app['scripts']->register('uikit-nestable', 'vendor/assets/uikit/js/components/nestable.min.js', 'uikit');
        $app['scripts']->register('uikit-notify', 'vendor/assets/uikit/js/components/notify.min.js', 'uikit');
        $app['scripts']->register('uikit-sortable', 'vendor/assets/uikit/js/components/sortable.min.js', 'uikit');
        $app['scripts']->register('uikit-sticky', 'vendor/assets/uikit/js/components/sticky.min.js', 'uikit');
        $app['scripts']->register('uikit-upload', 'vendor/assets/uikit/js/components/upload.min.js', 'uikit');
        $app['scripts']->register('uikit-datepicker', 'vendor/assets/uikit/js/components/datepicker.min.js', 'uikit');
        $app['scripts']->register('uikit-timepicker', 'vendor/assets/uikit/js/components/timepicker.js', 'uikit-autocomplete');
        $app['scripts']->register('gravatar', 'vendor/assets/gravatarjs/gravatar.js');
        $app['scripts']->register('system', 'app/modules/system/app/system.js', ['jquery', 'tmpl', 'locale']);
        $app['scripts']->register('vue', 'vendor/assets/vue/dist/'.($debug ? 'vue.js' : 'vue.min.js'));
        $app['scripts']->register('vue-system', 'app/modules/system/app/vue-system.js', ['vue-resource', 'lodash', 'locale', 'uikit-pagination']);
        $app['scripts']->register('vue-resource', 'app/modules/system/app/vue-resource.js', ['vue']);
        $app['scripts']->register('vue-validator', 'app/modules/system/app/vue-validator.js', ['vue']);

        $app['view']->data('$pagekit', ['version' => $app['version'], 'url' => $app['router']->getContext()->getBaseUrl(), 'csrf' => $app['csrf']->generate()]);

        $app['view']->section()->set('messages', function() use ($app) {
            return $app['view']->render('system:views/messages/messages.php');
        });

        $app['view']->section()->prepend('head', function () use ($app) {
            if ($templates = $app['view']->tmpl()->queued()) {
                $app['view']->script('tmpl', $app['url']->get('@system/system/tmpls', ['templates' => implode(',', $templates)]));
            }
        });

        foreach ($app['module'] as $module) {
            if (isset($module->templates)) {
                foreach ($module->templates as $name => $tmpl) {
                    $app['view']->tmpl()->register($name, $tmpl);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest'
        ];
    }
}
