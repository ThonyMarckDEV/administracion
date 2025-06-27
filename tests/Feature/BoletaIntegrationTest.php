<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\MyCompany;
use Greenter\Model\Sale\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class BoletaIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock MyCompany to avoid database queries
        $companyMock = Mockery::mock(MyCompany::class);
        $companyMock->shouldReceive('causeError')->andReturn((object)[
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
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_boleta_is_sent_to_sunat()
    {
        // Mock del servicio GenerateComprobante
        $mockService = Mockery::mock('App\Services\Sunat\GenerateComprobante');
        $invoice = Mockery::mock(Invoice::class);
        $invoice->shouldReceive('getTipoDoc')->andReturn('03'); // Boleta document type
        $invoice->shouldReceive('getSerie')->andReturn('B001');
        $invoice->shouldReceive('getCorrelativo')->andReturn('1');
        $invoice->shouldReceive('getName')->andReturn('B001-1');

        $mockService->shouldReceive('createComprobante')
            ->once()
            ->withArgs(function ($data, $type) {
                return $type === 'boleta' && isset($data['id_pago']) && $data['id_pago'] === 999;
            })
            ->andReturn($invoice);

        $mockService->shouldReceive('sendComprobante')
            ->once()
            ->with($invoice, 999)
            ->andReturn([
                'success' => true,
                'xml_path' => storage_path('app/public/boletas/999/xml/B001-1.xml'),
                'cdr_path' => storage_path('app/public/boletas/999/cdr/R-B001-1.zip'),
                'cdr_status' => [
                    'estado' => 'ACCEPTED',
                    'code' => 0,
                    'description' => 'La boleta fue aceptada',
                    'notes' => [],
                ],
            ]);

        // Inyectar el mock en el contenedor
        $this->app->bind(\App\Services\Sunat\GenerateComprobante::class, fn () => $mockService);

        // Usuario autenticado
        $user = User::factory()->create();
        $this->actingAs($user);

        // Payload que simula el request
        $payload = [
            'id_pago' => 999,
            'client' => [
                'tipo_doc' => '1', // DNI for boleta
                'num_doc' => '61883939',
                'razon_social' => 'Anthony Marck Mendoza Sanchez',
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
                    'mto_precio_unitario' => 59.00,
                ],
            ],
        ];

        // Ejecutar la solicitud
        $response = $this->postJson('/api/boleta', $payload);

        // Depuración: Imprimir respuesta si no es 200
        if ($response->getStatusCode() !== 200) {
            $response->dump();
        }

        // Afirmaciones
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Receipt processed successfully', // Match the controller's response
                     'data' => [
                         'success' => true,
                         'xml_path' => storage_path('app/public/boletas/999/xml/B001-1.xml'),
                         'cdr_path' => storage_path('app/public/boletas/999/cdr/R-B001-1.zip'),
                         'cdr_status' => [
                             'estado' => 'ACCEPTED',
                             'code' => 0,
                             'description' => 'La boleta fue aceptada',
                             'notes' => [],
                         ],
                     ],
                 ]);
    }
}