<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\MyCompany;
use App\Models\Invoice;
use Greenter\Model\Sale\Invoice as GreenterInvoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class ComprobanteControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock MyCompany to avoid database queries
        $companyMock = Mockery::mock(MyCompany::class);
        $companyMock->shouldReceive('first')->andReturn((object)[
            'ruc' => '12345678901',
            'razon_social' => 'EMPRESA TEST',
            'nombre_comercial' => 'TEST SAC',
            'ubigueo' => '150101',
            'departamento' => 'LIMA',
            'provincia' => 'LIMA',
            'distrito' => 'LIMA',
            'urbanizacion' => 'URB TEST',
            'direccion' => 'AV TEST 123',
            'cod_local' => '0000',
        ]);
        $this->app->bind(MyCompany::class, fn () => $companyMock);

        // Mock Log facade
        Log::shouldReceive('error')->andReturnNull();
        Log::shouldReceive('debug')->andReturnNull();
        Log::shouldReceive('info')->andReturnNull();

        // Mock file system operations
        $certificatePath = config('greenter.certificate_path', storage_path('app/certificates'));
        $fileSystemMock = Mockery::mock('alias:Illuminate\Support\Facades\File');
        $fileSystemMock->shouldReceive('exists')->with($certificatePath)->andReturn(true);
        $fileSystemMock->shouldReceive('glob')->with($certificatePath . '/*.pem')->andReturn([$certificatePath . '/certificate.pem']);
        $fileSystemMock->shouldReceive('get')->with($certificatePath . '/certificate.pem')->andReturn('certificate-content');

        // Mock GenerateComprobante service
        $mockService = Mockery::mock('App\Services\Sunat\GenerateComprobante');
        $invoice = Mockery::mock(GreenterInvoice::class);
        $invoice->shouldReceive('getTipoDoc')->andReturnUsing(function () use ($mockService) {
            return $mockService->tipoDoc ?? '01';
        });
        $invoice->shouldReceive('getSerie')->andReturnUsing(function () use ($mockService) {
            return $mockService->serie ?? 'F001';
        });
        $invoice->shouldReceive('getCorrelativo')->andReturn('1');
        $invoice->shouldReceive('getName')->andReturnUsing(function () use ($mockService) {
            return ($mockService->serie ?? 'F001') . '-1';
        });

        $mockService->shouldReceive('createComprobante')
            ->andReturnUsing(function ($data, $type) use ($mockService, $invoice) {
                $mockService->tipoDoc = $type === 'factura' ? '01' : '03';
                $mockService->serie = $data['serie'];
                return $invoice;
            });

        $mockService->shouldReceive('sendComprobante')
            ->andReturnUsing(function ($invoice, $idPago) {
                return [
                    'success' => true,
                    'xml_path' => storage_path("app/public/facturas/{$idPago}/xml/{$invoice->getName()}.xml"),
                    'cdr_path' => storage_path("app/public/facturas/{$idPago}/cdr/R-{$invoice->getName()}.zip"),
                    'cdr_status' => [
                        'estado' => 'ACCEPTED',
                        'code' => 0,
                        'description' => 'La factura fue aceptada',
                        'notes' => [],
                    ],
                ];
            });

        $this->app->bind(\App\Services\Sunat\GenerateComprobante::class, fn () => $mockService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_user_can_create_factura()
    {
        // Create and authenticate a user
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            'id_pago' => 1,
            'client' => [
                'tipo_doc' => '6',
                'num_doc' => '20000000001',
                'razon_social' => 'EMPRESA X'
            ],
            'tipo_operacion' => '0101',
            'serie' => 'F001',
            'correlativo' => '1',
            'fecha_emision' => '2025-05-25T13:05:00-05:00',
            'tipo_moneda' => 'PEN',
            'mto_oper_gravadas' => 100.00,
            'mto_igv' => 18.00,
            'total_impuestos' => 18.00,
            'valor_venta' => 100.00,
            'sub_total' => 118.00,
            'mto_imp_venta' => 118.00,
            'legend_value' => 'SON CIENTO DIECIOCHO CON 00/100 SOLES',
            'items' => [
                [
                    'cod_producto' => 'P001',
                    'unidad' => 'NIU',
                    'cantidad' => 2,
                    'mto_valor_unitario' => 50.00,
                    'descripcion' => 'PRODUCTO 1',
                    'mto_base_igv' => 100.00,
                    'porcentaje_igv' => 18.00,
                    'igv' => 18.00,
                    'tip_afe_igv' => '10',
                    'total_impuestos' => 18.00,
                    'mto_valor_venta' => 100.00,
                    'mto_precio_unitario' => 59.00
                ]
            ]
        ];

        $response = $this->postJson('/api/factura', $data);

        // Debugging: Print response content if not 200
        if ($response->getStatusCode() !== 200) {
            $response->dump();
        }

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Invoice processed successfully',
                     'data' => [
                         'success' => true,
                         'xml_path' => storage_path('app/public/facturas/1/xml/F001-1.xml'),
                         'cdr_path' => storage_path('app/public/facturas/1/cdr/R-F001-1.zip'),
                         'cdr_status' => [
                             'estado' => 'ACCEPTED',
                             'code' => 0,
                             'description' => 'La factura fue aceptada',
                             'notes' => [],
                         ],
                     ],
                 ]);
    }

    public function test_user_can_create_boleta()
    {
        // Create and authenticate a user
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            'id_pago' => 2,
            'client' => [
                'tipo_doc' => '1',
                'num_doc' => '61883939',
                'razon_social' => 'Anthony Marck Mendoza Sanchez'
            ],
            'tipo_operacion' => '0101',
            'serie' => 'B001',
            'correlativo' => '1',
            'fecha_emision' => '2025-05-25T13:05:00-05:00',
            'tipo_moneda' => 'PEN',
            'mto_oper_gravadas' => 100.00,
            'mto_igv' => 18.00,
            'total_impuestos' => 18.00,
            'valor_venta' => 100.00,
            'sub_total' => 118.00,
            'mto_imp_venta' => 118.00,
            'legend_value' => 'SON CIENTO DIECIOCHO CON 00/100 SOLES',
            'items' => [
                [
                    'cod_producto' => 'P001',
                    'unidad' => 'NIU',
                    'cantidad' => 2,
                    'mto_valor_unitario' => 50.00,
                    'descripcion' => 'PRODUCTO 1',
                    'mto_base_igv' => 100.00,
                    'porcentaje_igv' => 18.00,
                    'igv' => 18.00,
                    'tip_afe_igv' => '10',
                    'total_impuestos' => 18.00,
                    'mto_valor_venta' => 100.00,
                    'mto_precio_unitario' => 59.00
                ]
            ]
        ];

        $response = $this->postJson('/api/boleta', $data);

        // Debugging: Print response content if not 200
        if ($response->getStatusCode() !== 200) {
            $response->dump();
        }

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Receipt processed successfully',
                     'data' => [
                         'success' => true,
                         'xml_path' => storage_path('app/public/facturas/2/xml/B001-1.xml'),
                         'cdr_path' => storage_path('app/public/facturas/2/cdr/R-B001-1.zip'),
                         'cdr_status' => [
                             'estado' => 'ACCEPTED',
                             'code' => 0,
                             'description' => 'La factura fue aceptada',
                             'notes' => [],
                         ],
                     ],
                 ]);
    }
}