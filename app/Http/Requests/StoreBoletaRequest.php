<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBoletaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_pago' => 'required|integer|min:1',
            'client.tipo_doc' => 'nullable|string|in:0,1,6', // 0: Various, 1: DNI, 6: RUC
            'client.num_doc' => 'nullable|string',
            'client.razon_social' => 'nullable|string|max:255',
            'company.ruc' => 'required|string|size:11',
            'company.razon_social' => 'required|string|max:255',
            'company.nombre_comercial' => 'required|string|max:255',
            'company.address.ubigueo' => 'required|string|size:6',
            'company.address.departamento' => 'required|string|max:100',
            'company.address.provincia' => 'required|string|max:100',
            'company.address.distrito' => 'required|string|max:100',
            'company.address.urbanizacion' => 'required|string|max:100',
            'company.address.direccion' => 'required|string|max:255',
            'company.address.cod_local' => 'required|string|size:4',
            'tipo_operacion' => 'required|string|size:4', // Ej: 0101
            'serie' => 'required|string|regex:/^B[0-9]{3}$/', // Ej: B001
            'correlativo' => 'required|string|max:8',
            'fecha_emision' => 'required|date_format:Y-m-d\TH:i:sP', // ISO 8601
            'tipo_moneda' => 'required|string|in:PEN,USD',
            'mto_oper_gravadas' => 'required|numeric|min:0',
            'mto_igv' => 'required|numeric|min:0',
            'total_impuestos' => 'required|numeric|min:0',
            'valor_venta' => 'required|numeric|min:0',
            'sub_total' => 'required|numeric|min:0',
            'mto_imp_venta' => 'required|numeric|min:0',
            'legend_value' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.cod_producto' => 'required|string|max:30',
            'items.*.unidad' => 'required|string|size:3', // Ej: NIU
            'items.*.cantidad' => 'required|numeric|min:0',
            'items.*.mto_valor_unitario' => 'required|numeric|min:0',
            'items.*.descripcion' => 'required|string|max:255',
            'items.*.mto_base_igv' => 'required|numeric|min:0',
            'items.*.porcentaje_igv' => 'required|numeric|min:0',
            'items.*.igv' => 'required|numeric|min:0',
            'items.*.tip_afe_igv' => 'required|string|size:2', // Ej: 10
            'items.*.total_impuestos' => 'required|numeric|min:0',
            'items.*.mto_valor_venta' => 'required|numeric|min:0',
            'items.*.mto_precio_unitario' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'id_pago.required' => 'The payment ID is required.',
            'id_pago.integer' => 'The payment ID must be an integer.',
            'id_pago.min' => 'The payment ID must be at least 1.',
            'client.tipo_doc.in' => 'The client document type must be Various (0), DNI (1), or RUC (6).',
            'client.razon_social.max' => 'The client name cannot exceed 255 characters.',
            'company.ruc.required' => 'The company RUC is required.',
            'company.ruc.size' => 'The RUC must be exactly 11 digits.',
            'company.razon_social.required' => 'The company name is required.',
            'company.nombre_comercial.required' => 'The commercial name is required.',
            'company.address.ubigueo.required' => 'The ubigeo is required.',
            'company.address.ubigueo.size' => 'The ubigeo must be 6 characters.',
            'company.address.departamento.required' => 'The department is required.',
            'company.address.provincia.required' => 'The province is required.',
            'company.address.distrito.required' => 'The district is required.',
            'company.address.urbanizacion.required' => 'The urbanization is required.',
            'company.address.direccion.required' => 'The address is required.',
            'company.address.cod_local.required' => 'The local code is required.',
            'company.address.cod_local.size' => 'The local code must be 4 characters.',
            'tipo_operacion.required' => 'The operation type is required.',
            'tipo_operacion.size' => 'The operation type must be 4 characters.',
            'serie.required' => 'The series is required.',
            'serie.regex' => 'The series must start with B followed by 3 digits (e.g., B001).',
            'correlativo.required' => 'The correlative is required.',
            'correlativo.max' => 'The correlative cannot exceed 8 characters.',
            'fecha_emision.required' => 'The issuance date is required.',
            'fecha_emision.date_format' => 'The issuance date must be in ISO 8601 format (Y-m-d\TH:i:sP).',
            'tipo_moneda.required' => 'The currency type is required.',
            'tipo_moneda.in' => 'The currency must be PEN or USD.',
            'mto_oper_gravadas.required' => 'The taxable operations amount is required.',
            'mto_oper_gravadas.numeric' => 'The taxable operations amount must be numeric.',
            'mto_igv.required' => 'The IGV amount is required.',
            'total_impuestos.required' => 'The total taxes are required.',
            'valor_venta.required' => 'The sale value is required.',
            'sub_total.required' => 'The subtotal is required.',
            'mto_imp_venta.required' => 'The total sale amount is required.',
            'legend_value.required' => 'The legend value is required.',
            'items.required' => 'Items are required.',
            'items.array' => 'Items must be an array.',
            'items.min' => 'At least one item is required.',
            'items.*.cod_producto.required' => 'The product code is required.',
            'items.*.unidad.required' => 'The unit is required.',
            'items.*.cantidad.required' => 'The quantity is required.',
            'items.*.mto_valor_unitario.required' => 'The unit value is required.',
            'items.*.descripcion.required' => 'The description is required.',
            'items.*.mto_base_igv.required' => 'The IGV base is required.',
            'items.*.porcentaje_igv.required' => 'The IGV percentage is required.',
            'items.*.igv.required' => 'The IGV is required.',
            'items.*.tip_afe_igv.required' => 'The IGV affectation type is required.',
            'items.*.total_impuestos.required' => 'The total item taxes are required.',
            'items.*.mto_valor_venta.required' => 'The item sale value is required.',
            'items.*.mto_precio_unitario.required' => 'The unit price is required.',
        ];
    }
}