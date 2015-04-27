<?php

namespace Laracasts\Utilities\JavaScript;

use Illuminate\Events\Dispatcher;

class LaravelViewBinder implements ViewBinder
{

    /**
     * The event dispatcher implementation.
     *
     * @var Dispatcher
     */
    private $event;

    /**
     * The name of the view to bind any
     * generated JS variables to.
     *
     * @var string
     */
    private $views;

    /**
     * The attributes of script tag
     */
    private $js_attributes;

    /**
     * Create a new Laravel view binder instance.
     *
     * @param Dispatcher   $event
     * @param string|array $views
     * @param array        $js_attributes
     */
    function __construct(Dispatcher $event, $views, $js_attributes)
    {
        $this->event = $event;
        $this->views = str_replace('/', '.', (array) $views);
        $this->js_attributes = $js_attributes;
    }

    /**
     * Bind the given JavaScript to the view.
     *
     * @param string $js
     */
    public function bind($js)
    {
        $e = function($s) {
          return htmlentities($s, ENT_QUOTES, mb_internal_encoding(), true);
        };

        foreach ($this->views as $view) {
            $this->event->listen("composing: {$view}", function () use ($js, $e) {

                $html_attrs = '';
                foreach($this->js_attributes as $name => $value) {
                  $html_attrs .= sprintf(' %s="%s"', $e($name), $e($value));
                }

                echo "<script{$html_attrs}>{$js}</script>";
            });
        }
    }

}
