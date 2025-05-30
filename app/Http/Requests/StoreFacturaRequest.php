<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacturaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id_pago' => 'required|integer|min:1',
            'client.tipo_doc' => 'required|string|in:1,6', // 1: DNI, 6: RUC
            'client.num_doc' => 'required|string',
            'client.razon_social' => 'required|string|max:255',
            'tipo_operacion' => 'required|string|size:4', // Ej: 0101
            'serie' => 'required|string', // Ej: F001
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

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'id_pago.required' => 'El ID de pago es obligatorio.',
            'id_pago.integer' => 'El ID de pago debe ser un número entero.',
            'id_pago.min' => 'El ID de pago debe ser mayor o igual a 1.',
            'client.tipo_doc.required' => 'El tipo de documento del cliente es obligatorio.',
            'client.tipo_doc.in' => 'El tipo de documento debe ser DNI (1) o RUC (6).',
            'client.num_doc.required' => 'El número de documento del cliente es obligatorio.',
            'client.razon_social.required' => 'La razón social del cliente es obligatoria.',
            'client.razon_social.max' => 'La razón social no puede exceder los 255 caracteres.',
            'tipo_operacion.required' => 'El tipo de operación es obligatorio.',
            'tipo_operacion.size' => 'El tipo de operación debe tener 4 caracteres.',
            'serie.required' => 'La serie es obligatoria.',
            'serie.size' => 'La serie debe tener 4 caracteres.',
            'correlativo.required' => 'El correlativo es obligatorio.',
            'correlativo.max' => 'El correlativo no puede exceder los 8 caracteres.',
            'fecha_emision.required' => 'La fecha de emisión es obligatoria.',
            'fecha_emision.date_format' => 'La fecha de emisión debe estar en formato ISO 8601 (Y-m-d\TH:i:sP).',
            'tipo_moneda.required' => 'El tipo de moneda es obligatorio.',
            'tipo_moneda.in' => 'La moneda debe ser PEN o USD.',
            'mto_oper_gravadas.required' => 'El monto de operaciones gravadas es obligatorio.',
            'mto_oper_gravadas.numeric' => 'El monto de operaciones gravadas debe ser numérico.',
            'mto_igv.required' => 'El monto del IGV es obligatorio.',
            'total_impuestos.required' => 'El total de impuestos es obligatorio.',
            'valor_venta.required' => 'El valor de venta es obligatorio.',
            'sub_total.required' => 'El subtotal es obligatorio.',
            'mto_imp_venta.required' => 'El monto total de la venta es obligatorio.',
            'legend_value.required' => 'El valor de la leyenda es obligatorio.',
            'items.required' => 'Los ítems son obligatorios.',
            'items.array' => 'Los ítems deben ser un arreglo.',
            'items.min' => 'Debe haber al menos un ítem.',
            'items.*.cod_producto.required' => 'El código del producto es obligatorio.',
            'items.*.unidad.required' => 'La unidad es obligatoria.',
            'items.*.cantidad.required' => 'La cantidad es obligatoria.',
            'items.*.mto_valor_unitario.required' => 'El valor unitario es obligatorio.',
            'items.*.descripcion.required' => 'La descripción es obligatoria.',
            'items.*.mto_base_igv.required' => 'La base del IGV es obligatoria.',
            'items.*.porcentaje_igv.required' => 'El porcentaje del IGV es obligatorio.',
            'items.*.igv.required' => 'El IGV es obligatorio.',
            'items.*.tip_afe_igv.required' => 'El tipo de afectación del IGV es obligatorio.',
            'items.*.total_impuestos.required' => 'El total de impuestos del ítem es obligatorio.',
            'items.*.mto_valor_venta.required' => 'El valor de venta del ítem es obligatorio.',
            'items.*.mto_precio_unitario.required' => 'El precio unitario es obligatorio.',
        ];
    }
}