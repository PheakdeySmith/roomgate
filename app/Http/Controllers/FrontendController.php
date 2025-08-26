<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Benefit;
use App\Models\Feature;
use App\Models\PageContent;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;

class FrontendController extends Controller
{
    // public function index()
    // {
    //     return view("frontends.layouts.app(original)");
    // }

    public function index()
    {

        $pageContents = PageContent::all()->keyBy('key');
        $benefits = Benefit::orderBy('order')->get();
        
        $highlightFeature = Feature::where('is_highlighted', true)->first();
        $otherFeatures = Feature::where('is_highlighted', false)->orderBy('order', 'asc')->get();

        $plans = SubscriptionPlan::where('is_active', true)
                 ->orderBy('price')
                 ->get()
                 ->groupBy('plan_group');


        $faqs = Faq::orderBy('order')->get();

        return view("frontends.layouts.home", [
            'content' => $pageContents,
            'benefits' => $benefits,
            'highlightFeature' => $highlightFeature,
            'otherFeatures' => $otherFeatures,
            'plans' => $plans,
            'faqs' => $faqs,
        ]);
    }
    
    public function feature()
    {
        $pageContents = PageContent::all()->keyBy('key');
        $benefits = Benefit::orderBy('order')->get();
        $otherFeatures = Feature::where('is_highlighted', false)->orderBy('order', 'asc')->get();

        return view("frontends.layouts.feature_page", [
            'content' => $pageContents,
            'benefits' => $benefits,
            'otherFeatures' => $otherFeatures,
        ]);
    }

    public function terms()
    {
        $pageContents = PageContent::all()->keyBy('key');

        return view("frontends.layouts.term", [
            'content' => $pageContents,
        ]);
    }
}
