<?php

namespace App\Http\Controllers;

use App\Models\Religion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ReligionController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Religions/Index', [
            'religions' => Religion::query()
                ->withCount('places')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Religion::create($request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:religions,name'],
        ]));

        return back(303);
    }

    public function update(Request $request, Religion $religion): RedirectResponse
    {
        $religion->update($request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('religions', 'name')->ignore($religion)],
        ]));

        return back(303);
    }

    public function destroy(Religion $religion): RedirectResponse
    {
        if ($religion->places()->exists()) {
            return back(303)->withErrors([
                'religion' => 'Move this religion’s places before deleting it.',
            ]);
        }

        $religion->delete();

        return back(303);
    }
}
