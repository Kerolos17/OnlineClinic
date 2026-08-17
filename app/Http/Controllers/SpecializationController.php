<?php

namespace App\Http\Controllers;

use App\Models\Specialization;

class SpecializationController extends Controller
{
    public function index()
    {
        $specializations = Specialization::where('is_active', true)
            ->with(['doctors.user'])
            ->get();

        return view('specializations.index', compact('specializations'));
    }

    public function show(int $id)
    {
        $specialization = Specialization::where('is_active', true)
            ->with(['doctors.user'])
            ->findOrFail($id);

        return view('specializations.show', compact('specialization'));
    }
}
