<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\SellermindAccountLink;
use Illuminate\Http\Request;

class IntegrationsController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->attributes->get('currentCompany');

        $sellermindLink = SellermindAccountLink::where('company_id', $company->id)
            ->whereIn('status', ['active', 'pending'])
            ->first();

        $integrations = [
            [
                'key' => 'sellermind',
                'name' => __('integrations.sellermind_name'),
                'description' => __('integrations.sellermind_description'),
                'icon' => 'link',
                'status' => $this->getSellermindStatus($sellermindLink),
                'route' => route('cabinet.integrations.sellermind'),
                'available' => true,
            ],
            [
                'key' => '1c',
                'name' => __('integrations.1c_name'),
                'description' => __('integrations.1c_description'),
                'icon' => 'database',
                'status' => 'coming_soon',
                'route' => null,
                'available' => false,
            ],
            [
                'key' => 'moysklad',
                'name' => __('integrations.moysklad_name'),
                'description' => __('integrations.moysklad_description'),
                'icon' => 'archive',
                'status' => 'coming_soon',
                'route' => null,
                'available' => false,
            ],
        ];

        return view('cabinet.integrations.index', compact('integrations'));
    }

    private function getSellermindStatus(?SellermindAccountLink $link): string
    {
        if (!$link) {
            return 'disconnected';
        }

        return $link->status === 'active' ? 'connected' : 'pending';
    }
}
