<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\CalculatorResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalculatorResultController extends Controller
{
    public function index()
    {
        $calculations = CalculatorResult::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('cabinet.calculations.index', [
            'calculations' => $calculations,
        ]);
    }

    public function destroy(CalculatorResult $calculation)
    {
        if ($calculation->user_id !== Auth::id()) {
            abort(403);
        }

        $calculation->delete();

        return redirect()->route('cabinet.calculations.index')
            ->with('success', __('Calculation deleted.'));
    }
}
