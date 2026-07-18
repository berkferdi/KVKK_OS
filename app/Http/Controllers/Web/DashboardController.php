<?php

namespace App\Http\Controllers\Web;

use App\Domain\Organization\Models\Company;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $companyCount = Company::query()->count();

        return view('dashboard.index', [
            'companyCount' => $companyCount,
        ]);
    }
}
