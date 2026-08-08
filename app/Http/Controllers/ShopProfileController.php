<?php

namespace App\Http\Controllers;

use App\Models\Junkshop;
use Illuminate\Http\Request;

class ShopProfileController extends Controller
{
    public const MATERIALS = ['PET', 'HDPE', 'cardboard', 'scrap_metal', 'aluminum', 'copper', 'e_waste'];

    public function create(Request $request)
    {
        if ($request->user()->junkshop) {
            return redirect()->route('operator.dashboard');
        }

        return view('operator.profile.create', ['materials' => self::MATERIALS]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $request->user()->junkshop()->create($validated + [
            'is_accredited_tsd' => (bool) $request->boolean('is_accredited_tsd'),
        ]);

        return redirect()->route('operator.dashboard')
            ->with('status', 'Shop registered! You\'re now discoverable in JunkConnect search.');
    }

    public function edit(Request $request)
    {
        return view('operator.profile.edit', [
            'junkshop' => $request->user()->junkshop,
            'materials' => self::MATERIALS,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $this->validated($request);

        $request->user()->junkshop->update($validated + [
            'is_accredited_tsd' => (bool) $request->boolean('is_accredited_tsd'),
        ]);

        return redirect()->route('operator.profile.edit')->with('status', 'Shop profile updated.');
    }

    private function validated(Request $request): array
    {
        // UC-05 business rule: must accept at least one material to be listed as active.
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'operating_hours' => ['required', 'string', 'max:100'],
            'materials_accepted' => ['required', 'array', 'min:1'],
            'materials_accepted.*' => ['in:'.implode(',', self::MATERIALS)],
        ]);
    }
}
