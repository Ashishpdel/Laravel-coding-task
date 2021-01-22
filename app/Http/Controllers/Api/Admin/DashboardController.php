<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;

class DashboardController extends Controller
{
	public function info() {
		$customers = User::where('role', 'customer')->count();
		$companies = Company::count();

		return response()->json([
			'customers' => $customers,
			'companies' => $companies,
		], 200);
	}
}
