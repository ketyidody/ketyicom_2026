<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Setting::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $settings = $query->orderBy('key')
            ->orderByDesc('active')
            ->orderByDesc('updated_at')
            ->paginate(20);

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
            'filters' => $request->only('search'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Settings/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255',
            'text' => 'nullable|string',
            'description' => 'nullable|string',
            'active' => 'boolean',
            'image' => 'nullable|image|max:10240', // Max 10MB
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('settings', 'public');
            $validated['text'] = $path;
        }

        Setting::create($validated);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Setting created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Setting $setting)
    {
        return Inertia::render('Admin/Settings/Show', [
            'setting' => $setting,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Setting $setting)
    {
        return Inertia::render('Admin/Settings/Edit', [
            'setting' => $setting,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Setting $setting)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255',
            'text' => 'nullable|string',
            'description' => 'nullable|string',
            'active' => 'boolean',
            'image' => 'nullable|image|max:10240', // Max 10MB
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if it exists and is in settings folder
            if ($setting->text && str_starts_with($setting->text, 'settings/')) {
                \Storage::disk('public')->delete($setting->text);
            }

            $path = $request->file('image')->store('settings', 'public');
            $validated['text'] = $path;
        }

        $setting->update($validated);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Setting updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $setting)
    {
        // Delete associated image if it exists and is in settings folder
        if ($setting->text && str_starts_with($setting->text, 'settings/')) {
            \Storage::disk('public')->delete($setting->text);
        }

        $setting->delete();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Setting deleted successfully.');
    }
}
