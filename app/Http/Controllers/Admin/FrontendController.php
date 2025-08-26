<?php

namespace App\Http\Controllers\Admin;

use App\Models\Faq;
use App\Models\PageContent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Feature; // Add this line
use App\Models\Benefit; // Assuming you have this
use Illuminate\Support\Facades\File; // Add this line for file operations

class FrontendController extends Controller
{

    // ... your existing hero() and benefit() methods ...

    public function hero()
    {
        $heroContents = PageContent::orderBy('key')->get();
        return view('backends.dashboard.admin.frontend.hero.index', compact('heroContents'));
    }

    public function storeHero(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'button_text' => 'nullable|string|max:255',
            'button_link' => 'nullable|url|max:255',
            'video_url' => 'nullable|url|max:255',
        ]);

        $imagePath = null;
        if ($request->hasFile('image_path')) {
            $image = $request->file('image_path');
            $imageName = uniqid('hero_').'.'.$image->getClientOriginalExtension();
            $image->move(public_path('uploads/hero'), $imageName);
            $imagePath = 'uploads/hero/' . $imageName;
        }

        PageContent::create([
            'key' => 'hero',
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'content' => $validated['content'] ?? null,
            'image_path' => $imagePath,
            'button_text' => $validated['button_text'] ?? null,
            'button_link' => $validated['button_link'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
        ]);

        return redirect()->route('admin.hero')->with('success', 'Hero content created successfully.');
    }

    public function updateHero(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image_path' => 'nullable|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:2048',
            'button_text' => 'nullable|string|max:255',
            'button_link' => 'nullable|url|max:255',
            'video_url' => 'nullable|url|max:255',
        ]);

        $hero = PageContent::findOrFail($id);

        if ($request->hasFile('image_path')) {
            // Optional: Delete old image
            if ($hero->image_path && File::exists(public_path($hero->image_path))) {
                File::delete(public_path($hero->image_path));
            }
            $image = $request->file('image_path');
            $imageName = uniqid('hero_').'.'.$image->getClientOriginalExtension();
            $image->move(public_path('uploads/hero'), $imageName);
            $hero->image_path = 'uploads/hero/' . $imageName;
        }

        $hero->title = $validated['title'];
        $hero->subtitle = $validated['subtitle'] ?? null;
        $hero->content = $validated['content'] ?? null;
        $hero->button_text = $validated['button_text'] ?? null;
        $hero->button_link = $validated['button_link'] ?? null;
        $hero->video_url = $validated['video_url'] ?? null;
        $hero->save();

        return redirect()->route('admin.hero')->with('success', 'Hero content updated successfully.');
    }

    public function benefit()
    {
        $benefits = Benefit::orderBy('order')->get();
        return view('backends.dashboard.admin.frontend.benefit.index', compact('benefits'));
    }

    public function storeBenefit(Request $request)
    {
        $validated = $request->validate([
            'icon_path' => 'nullable|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:2048',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'order' => 'nullable|integer',
        ]);

        $iconPath = null;
        if ($request->hasFile('icon_path')) {
            $icon = $request->file('icon_path');
            $iconName = uniqid('benefit_').'.'.$icon->getClientOriginalExtension();
            $icon->move(public_path('uploads/benefit'), $iconName);
            $iconPath = 'uploads/benefit/' . $iconName;
        }

        Benefit::create([
            'icon_path' => $iconPath,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'order' => $validated['order'] ?? 0,
        ]);

        return redirect()->route('admin.benefit')->with('success', 'Benefit created successfully.');
    }

    public function updateBenefit(Request $request, $id)
    {
        $validated = $request->validate([
            'icon_path' => 'nullable|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:2048',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'order' => 'nullable|integer',
        ]);

        $benefit = Benefit::findOrFail($id);

        if ($request->hasFile('icon_path')) {
            if ($benefit->icon_path && File::exists(public_path($benefit->icon_path))) {
                File::delete(public_path($benefit->icon_path));
            }
            $icon = $request->file('icon_path');
            $iconName = uniqid('benefit_').'.'.$icon->getClientOriginalExtension();
            $icon->move(public_path('uploads/benefit'), $iconName);
            $benefit->icon_path = 'uploads/benefit/' . $iconName;
        }

        $benefit->title = $validated['title'];
        $benefit->description = $validated['description'];
        $benefit->order = $validated['order'] ?? 0;
        $benefit->save();

        return redirect()->route('admin.benefit')->with('success', 'Benefit updated successfully.');
    }

    public function destroyBenefit($id)
    {
        $benefit = Benefit::findOrFail($id);
        if ($benefit->icon_path && File::exists(public_path($benefit->icon_path))) {
            File::delete(public_path($benefit->icon_path));
        }
        $benefit->delete();
        return redirect()->route('admin.benefit')->with('success', 'Benefit deleted successfully.');
    }

    // ========== START: FEATURE METHODS ==========

    /**
     * Display a listing of the features.
     */
    public function feature()
    {
        // This is the admin index page, so we fetch all features to manage them.
        $features = Feature::orderBy('order', 'asc')->get();
        return view('backends.dashboard.admin.frontend.feature.index', compact('features'));
    }

    public function storeFeature(Request $request)
    {
        $validated = $request->validate([
            'image_path' => 'required|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:2048',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'link' => 'nullable|url',
            'bullets' => 'nullable|array',
            'bullets.*' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_highlighted' => 'nullable|boolean', // Added for the new field
        ]);

        // === Logic to ensure only one feature is highlighted ===
        if ($request->boolean('is_highlighted')) {
            // Un-highlight all other features before creating the new one.
            Feature::where('is_highlighted', true)->update(['is_highlighted' => false]);
        }

        $imagePath = null;
        if ($request->hasFile('image_path')) {
            $image = $request->file('image_path');
            $imageName = uniqid('feature_') . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/feature'), $imageName);
            $imagePath = 'uploads/feature/' . $imageName;
        }

        Feature::create([
            'image_path' => $imagePath,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'link' => $validated['link'] ?? '#',
            'bullets' => $validated['bullets'] ?? [],
            'order' => $validated['order'] ?? 0,
            'is_highlighted' => $request->boolean('is_highlighted'), // Save the new value
        ]);

        return redirect()->route('admin.feature')->with('success', 'Feature created successfully.');
    }

    public function updateFeature(Request $request, $id)
    {
        $validated = $request->validate([
            'image_path' => 'nullable|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:2048',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'link' => 'nullable|url',
            'bullets' => 'nullable|array',
            'bullets.*' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_highlighted' => 'nullable|boolean',
        ]);

        $feature = Feature::findOrFail($id);
        
        if ($request->boolean('is_highlighted')) {
            Feature::where('is_highlighted', true)->update(['is_highlighted' => false]);
        }

        if ($request->hasFile('image_path')) {
            if ($feature->image_path && File::exists(public_path($feature->image_path))) {
                File::delete(public_path($feature->image_path));
            }
            $image = $request->file('image_path');
            $imageName = uniqid('feature_') . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/feature'), $imageName);
            $feature->image_path = 'uploads/feature/' . $imageName;
        }

        $feature->title = $validated['title'];
        $feature->description = $validated['description'];
        $feature->link = $validated['link'] ?? '#';
        $feature->bullets = $validated['bullets'] ?? [];
        $feature->order = $validated['order'] ?? 0;
        $feature->is_highlighted = $request->boolean('is_highlighted');
        $feature->save();

        return redirect()->route('admin.feature')->with('success', 'Feature updated successfully.');
    }

    public function destroyFeature($id)
    {
        $feature = Feature::findOrFail($id);
        if ($feature->image_path && File::exists(public_path($feature->image_path))) {
            File::delete(public_path($feature->image_path));
        }
        $feature->delete();
        return redirect()->route('admin.feature')->with('success', 'Feature deleted successfully.');
    }


    public function faq()
    {
        $faqs = Faq::orderBy('order', 'asc')->get();
        return view('backends.dashboard.admin.frontend.faq.index', compact('faqs'));
    }

    /**
     * Store a newly created FAQ in storage.
     */
    public function storeFaq(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'order' => 'nullable|integer',
        ]);

        Faq::create($validated);

        return redirect()->route('admin.faq')->with('success', 'FAQ created successfully.');
    }

    /**
     * Update the specified FAQ in storage.
     */
    public function updateFaq(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'order' => 'nullable|integer',
        ]);

        $faq->update($validated);

        return redirect()->route('admin.faq')->with('success', 'FAQ updated successfully.');
    }

    /**
     * Remove the specified FAQ from storage.
     */
    public function destroyFaq(Faq $faq)
    {
        $faq->delete();
        return redirect()->route('admin.faq')->with('success', 'FAQ deleted successfully.');
    }
}