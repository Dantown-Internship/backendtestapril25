<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\View;
use App\Models\SiteSettings;
use App\Models\ContactUs;
use App\Models\MainSlider;
use App\Models\ServicesSection;
use App\Models\Projects;
use App\Models\AboutSection;
use App\Models\Causes;
use App\Models\Events;
use App\Models\Volunters;
use App\Models\Gallery;
use App\Models\Testimonial;
use App\Models\FAQ;


class SharedViewData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {

        return $next($request);
    }
}