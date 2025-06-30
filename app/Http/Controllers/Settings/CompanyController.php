<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\MyCompany;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    public function edit()
    {
        $company = MyCompany::first();

        if (!$company) {

            $company = MyCompany::create([
                'ruc' => '20000000001',
                'razon_social' => 'GREEN SAC',
                'nombre_comercial' => 'GREEN',
                'ubigueo' => '150101',
                'departamento' => 'LIMA',
                'provincia' => 'LIMA',
                'distrito' => 'LIMA',
                'urbanizacion' => '-',
                'direccion' => 'Av. Villa Nueva 221',
                'cod_local' => '0000',
            ]);
        }

        return Inertia::render('settings/Company', [
            'company' => [
                'ruc' => $company->ruc,
                'razon_social' => $company->razon_social,
                'nombre_comercial' => $company->nombre_comercial,
                'address' => [
                    'ubigueo' => $company->ubigueo,
                    'departamento' => $company->departamento,
                    'provincia' => $company->provincia,
                    'distrito' => $company->distrito,
                    'urbanizacion' => $company->urbanizacion,
                    'direccion' => $company->direccion,
                    'cod_local' => $company->cod_local,
                ],
            ],
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'ruc' => 'required|string|size:11|regex:/^[0-9]+$/',
            'razon_social' => 'required|string|max:255',
            'nombre_comercial' => 'required|string|max:255',
            'ubigueo' => 'required|string|size:6',
            'departamento' => 'required|string|max:100',
            'provincia' => 'required|string|max:100',
            'distrito' => 'required|string|max:100',
            'urbanizacion' => 'nullable|string|max:100',
            'direccion' => 'required|string|max:255',
            'cod_local' => 'required|string|size:4',
        ]);

        try {
            $company = MyCompany::first();

            if (!$company) {
                return redirect()->route('company.edit')->with('error', 'No company record found.');
            }

            $company->update([
                'ruc' => $request->ruc,
                'razon_social' => $request->razon_social,
                'nombre_comercial' => $request->nombre_comercial,
                'ubigueo' => $request->ubigueo,
                'departamento' => $request->departamento,
                'provincia' => $request->provincia,
                'distrito' => $request->distrito,
                'urbanizacion' => $request->urbanizacion,
                'direccion' => $request->direccion,
                'cod_local' => $request->cod_local,
            ]);

            return redirect()->route('company.edit')->with('success', 'Company details updated successfully');
        } catch (\Exception $e) {
            Log::error('Company update failed', ['error' => $e->getMessage()]);
            return redirect()->route('company.edit')->with('error', 'Failed to update company details: ' . $e->getMessage());
        }
    }
}