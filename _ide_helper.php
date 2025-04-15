<?php
/* @noinspection ALL */
// @formatter:off
// phpcs:ignoreFile

/**
 * A helper file for Laravel, to provide autocomplete information to your IDE
 * Generated for Laravel 11.30.0.
 *
 * This file should not be included in your code, only analyzed by your IDE!
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 * @see https://github.com/barryvdh/laravel-ide-helper
 */
namespace Livewire {
    /**
     * 
     *
     * @see \Livewire\LivewireManager
     */
    class Livewire {
        /**
         * 
         *
         * @static 
         */
        public static function setProvider($provider)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->setProvider($provider);
        }

        /**
         * 
         *
         * @static 
         */
        public static function provide($callback)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->provide($callback);
        }

        /**
         * 
         *
         * @static 
         */
        public static function component($name, $class = null)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->component($name, $class);
        }

        /**
         * 
         *
         * @static 
         */
        public static function componentHook($hook)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->componentHook($hook);
        }

        /**
         * 
         *
         * @static 
         */
        public static function propertySynthesizer($synth)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->propertySynthesizer($synth);
        }

        /**
         * 
         *
         * @static 
         */
        public static function directive($name, $callback)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->directive($name, $callback);
        }

        /**
         * 
         *
         * @static 
         */
        public static function precompiler($callback)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->precompiler($callback);
        }

        /**
         * 
         *
         * @static 
         */
        public static function new($name, $id = null)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->new($name, $id);
        }

        /**
         * 
         *
         * @static 
         */
        public static function isDiscoverable($componentNameOrClass)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->isDiscoverable($componentNameOrClass);
        }

        /**
         * 
         *
         * @static 
         */
        public static function resolveMissingComponent($resolver)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->resolveMissingComponent($resolver);
        }

        /**
         * 
         *
         * @static 
         */
        public static function mount($name, $params = [], $key = null)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->mount($name, $params, $key);
        }

        /**
         * 
         *
         * @static 
         */
        public static function snapshot($component)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->snapshot($component);
        }

        /**
         * 
         *
         * @static 
         */
        public static function fromSnapshot($snapshot)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->fromSnapshot($snapshot);
        }

        /**
         * 
         *
         * @static 
         */
        public static function listen($eventName, $callback)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->listen($eventName, $callback);
        }

        /**
         * 
         *
         * @static 
         */
        public static function current()
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->current();
        }

        /**
         * 
         *
         * @static 
         */
        public static function update($snapshot, $diff, $calls)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->update($snapshot, $diff, $calls);
        }

        /**
         * 
         *
         * @static 
         */
        public static function updateProperty($component, $path, $value)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->updateProperty($component, $path, $value);
        }

        /**
         * 
         *
         * @static 
         */
        public static function isLivewireRequest()
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->isLivewireRequest();
        }

        /**
         * 
         *
         * @static 
         */
        public static function componentHasBeenRendered()
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->componentHasBeenRendered();
        }

        /**
         * 
         *
         * @static 
         */
        public static function forceAssetInjection()
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->forceAssetInjection();
        }

        /**
         * 
         *
         * @static 
         */
        public static function setUpdateRoute($callback)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->setUpdateRoute($callback);
        }

        /**
         * 
         *
         * @static 
         */
        public static function getUpdateUri()
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->getUpdateUri();
        }

        /**
         * 
         *
         * @static 
         */
        public static function setScriptRoute($callback)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->setScriptRoute($callback);
        }

        /**
         * 
         *
         * @static 
         */
        public static function useScriptTagAttributes($attributes)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->useScriptTagAttributes($attributes);
        }

        /**
         * 
         *
         * @static 
         */
        public static function withUrlParams($params)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->withUrlParams($params);
        }

        /**
         * 
         *
         * @static 
         */
        public static function withQueryParams($params)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->withQueryParams($params);
        }

        /**
         * 
         *
         * @static 
         */
        public static function withCookie($name, $value)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->withCookie($name, $value);
        }

        /**
         * 
         *
         * @static 
         */
        public static function withCookies($cookies)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->withCookies($cookies);
        }

        /**
         * 
         *
         * @static 
         */
        public static function withHeaders($headers)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->withHeaders($headers);
        }

        /**
         * 
         *
         * @static 
         */
        public static function withoutLazyLoading()
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->withoutLazyLoading();
        }

        /**
         * 
         *
         * @static 
         */
        public static function test($name, $params = [])
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->test($name, $params);
        }

        /**
         * 
         *
         * @static 
         */
        public static function visit($name)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->visit($name);
        }

        /**
         * 
         *
         * @static 
         */
        public static function actingAs($user, $driver = null)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->actingAs($user, $driver);
        }

        /**
         * 
         *
         * @static 
         */
        public static function isRunningServerless()
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->isRunningServerless();
        }

        /**
         * 
         *
         * @static 
         */
        public static function addPersistentMiddleware($middleware)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->addPersistentMiddleware($middleware);
        }

        /**
         * 
         *
         * @static 
         */
        public static function setPersistentMiddleware($middleware)
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->setPersistentMiddleware($middleware);
        }

        /**
         * 
         *
         * @static 
         */
        public static function getPersistentMiddleware()
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->getPersistentMiddleware();
        }

        /**
         * 
         *
         * @static 
         */
        public static function flushState()
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->flushState();
        }

        /**
         * 
         *
         * @static 
         */
        public static function originalUrl()
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->originalUrl();
        }

        /**
         * 
         *
         * @static 
         */
        public static function originalPath()
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->originalPath();
        }

        /**
         * 
         *
         * @static 
         */
        public static function originalMethod()
        {
            /** @var \Livewire\LivewireManager $instance */
            return $instance->originalMethod();
        }

            }
    }

namespace Srmklive\PayPal\Facades {
    /**
     * 
     *
     */
    class PayPal {
        /**
         * Get specific PayPal API provider object to use.
         *
         * @throws Exception
         * @return \Srmklive\PayPal\Services\PayPal 
         * @static 
         */
        public static function getProvider()
        {
            return \Srmklive\PayPal\PayPalFacadeAccessor::getProvider();
        }

        /**
         * Set PayPal API Client to use.
         *
         * @throws \Exception
         * @return \Srmklive\PayPal\Services\PayPal 
         * @static 
         */
        public static function setProvider()
        {
            return \Srmklive\PayPal\PayPalFacadeAccessor::setProvider();
        }

            }
    }

namespace Illuminate\Http {
    /**
     * 
     *
     */
    class Request {
        /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestValidation()
         * @param array $rules
         * @param mixed $params
         * @static 
         */
        public static function validate($rules, ...$params)
        {
            return \Illuminate\Http\Request::validate($rules, ...$params);
        }

        /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestValidation()
         * @param string $errorBag
         * @param array $rules
         * @param mixed $params
         * @static 
         */
        public static function validateWithBag($errorBag, $rules, ...$params)
        {
            return \Illuminate\Http\Request::validateWithBag($errorBag, $rules, ...$params);
        }

        /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestSignatureValidation()
         * @param mixed $absolute
         * @static 
         */
        public static function hasValidSignature($absolute = true)
        {
            return \Illuminate\Http\Request::hasValidSignature($absolute);
        }

        /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestSignatureValidation()
         * @static 
         */
        public static function hasValidRelativeSignature()
        {
            return \Illuminate\Http\Request::hasValidRelativeSignature();
        }

        /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestSignatureValidation()
         * @param mixed $ignoreQuery
         * @param mixed $absolute
         * @static 
         */
        public static function hasValidSignatureWhileIgnoring($ignoreQuery = [], $absolute = true)
        {
            return \Illuminate\Http\Request::hasValidSignatureWhileIgnoring($ignoreQuery, $absolute);
        }

        /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestSignatureValidation()
         * @param mixed $ignoreQuery
         * @static 
         */
        public static function hasValidRelativeSignatureWhileIgnoring($ignoreQuery = [])
        {
            return \Illuminate\Http\Request::hasValidRelativeSignatureWhileIgnoring($ignoreQuery);
        }

            }
    }

namespace Illuminate\Routing {
    /**
     * 
     *
     */
    class Route {
        /**
         * 
         *
         * @see \Livewire\Features\SupportLazyLoading\SupportLazyLoading::registerRouteMacro()
         * @param mixed $enabled
         * @static 
         */
        public static function lazy($enabled = true)
        {
            return \Illuminate\Routing\Route::lazy($enabled);
        }

        /**
         * 
         *
         * @see \Spatie\Permission\PermissionServiceProvider::registerMacroHelpers()
         * @param mixed $roles
         * @static 
         */
        public static function role($roles = [])
        {
            return \Illuminate\Routing\Route::role($roles);
        }

        /**
         * 
         *
         * @see \Spatie\Permission\PermissionServiceProvider::registerMacroHelpers()
         * @param mixed $permissions
         * @static 
         */
        public static function permission($permissions = [])
        {
            return \Illuminate\Routing\Route::permission($permissions);
        }

            }
    }

namespace Illuminate\View {
    /**
     * 
     *
     */
    class ComponentAttributeBag {
        /**
         * 
         *
         * @see \Livewire\Features\SupportBladeAttributes\SupportBladeAttributes::provide()
         * @param mixed $name
         * @static 
         */
        public static function wire($name)
        {
            return \Illuminate\View\ComponentAttributeBag::wire($name);
        }

            }
    /**
     * 
     *
     */
    class View {
        /**
         * 
         *
         * @see \Livewire\Features\SupportPageComponents\SupportPageComponents::registerLayoutViewMacros()
         * @param mixed $data
         * @static 
         */
        public static function layoutData($data = [])
        {
            return \Illuminate\View\View::layoutData($data);
        }

        /**
         * 
         *
         * @see \Livewire\Features\SupportPageComponents\SupportPageComponents::registerLayoutViewMacros()
         * @param mixed $section
         * @static 
         */
        public static function section($section)
        {
            return \Illuminate\View\View::section($section);
        }

        /**
         * 
         *
         * @see \Livewire\Features\SupportPageComponents\SupportPageComponents::registerLayoutViewMacros()
         * @param mixed $title
         * @static 
         */
        public static function title($title)
        {
            return \Illuminate\View\View::title($title);
        }

        /**
         * 
         *
         * @see \Livewire\Features\SupportPageComponents\SupportPageComponents::registerLayoutViewMacros()
         * @param mixed $slot
         * @static 
         */
        public static function slot($slot)
        {
            return \Illuminate\View\View::slot($slot);
        }

        /**
         * 
         *
         * @see \Livewire\Features\SupportPageComponents\SupportPageComponents::registerLayoutViewMacros()
         * @param mixed $view
         * @param mixed $params
         * @static 
         */
        public static function extends($view, $params = [])
        {
            return \Illuminate\View\View::extends($view, $params);
        }

        /**
         * 
         *
         * @see \Livewire\Features\SupportPageComponents\SupportPageComponents::registerLayoutViewMacros()
         * @param mixed $view
         * @param mixed $params
         * @static 
         */
        public static function layout($view, $params = [])
        {
            return \Illuminate\View\View::layout($view, $params);
        }

        /**
         * 
         *
         * @see \Livewire\Features\SupportPageComponents\SupportPageComponents::registerLayoutViewMacros()
         * @param callable $callback
         * @static 
         */
        public static function response($callback)
        {
            return \Illuminate\View\View::response($callback);
        }

            }
    }


namespace  {
    class Livewire extends \Livewire\Livewire {}
    class PayPal extends \Srmklive\PayPal\Facades\PayPal {}
}





