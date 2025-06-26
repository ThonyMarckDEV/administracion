<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Sunat\GenerateComprobante;
use App\Models\MyCompany;
use Greenter\Model\Sale\Invoice;
use Mockery;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class GenerateComprobanteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Mock the MyCompany model to avoid database queries
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

        // Bind the mock to the container
        $this->app->instance(MyCompany::class, $companyMock);

        // Mock the GeneratePdf service
        $pdfServiceMock = Mockery::mock('App\Services\Sunat\GeneratePdf');
        $this->app->instance('App\Services\Sunat\GeneratePdf', $pdfServiceMock);

        // Mock Log facade to prevent actual logging during tests
        Log::shouldReceive('error')->andReturnNull();
        Log::shouldReceive('debug')->andReturnNull();

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

    public function test_create_factura_structure()
    {
        $service = new GenerateComprobante(app('App\Services\Sunat\GeneratePdf'));

        $data = [
            'id_pago' => 1,
            'client' => [
                'tipo_doc' => '6',
                'num_doc' => '20000000001',
                'razon_social' => 'EMPRESA X',
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
                    'mto_precio_unitario' => 59.00,
                ],
            ],
        ];

        $factura = $service->createComprobante($data, 'factura');

        // Assert that the returned object is an instance of Invoice
        $this->assertInstanceOf(Invoice::class, $factura);

        // Assert specific properties of the Invoice object
        $this->assertEquals('01', $factura->getTipoDoc());
        $this->assertEquals('F001', $factura->getSerie());
        $this->assertEquals('1', $factura->getCorrelativo());
        $this->assertEquals('0101', $factura->getTipoOperacion());
        $this->assertEquals('PEN', $factura->getTipoMoneda());
        $this->assertEquals(100.00, $factura->getMtoOperGravadas());
        $this->assertEquals(18.00, $factura->getMtoIGV());
        $this->assertEquals(118.00, $factura->getMtoImpVenta());

        // Assert client data
        $client = $factura->getClient();
        $this->assertEquals('6', $client->getTipoDoc());
        $this->assertEquals('20000000001', $client->getNumDoc());
        $this->assertEquals('EMPRESA X', $client->getRznSocial());

        // Assert item details
        $details = $factura->getDetails();
        $this->assertCount(1, $details);
        $this->assertEquals('P001', $details[0]->getCodProducto());
        $this->assertEquals(2, $details[0]->getCantidad());
        $this->assertEquals(50.00, $details[0]->getMtoValorUnitario());

        // Assert legend
        $legends = $factura->getLegends();
        $this->assertCount(1, $legends);
        $this->assertEquals('1000', $legends[0]->getCode());
        $this->assertEquals('SON CIENTO DIECIOCHO CON 00/100 SOLES', $legends[0]->getValue());
    }

    public function test_create_boleta_structure()
    {
        $service = new GenerateComprobante(app('App\Services\Sunat\GeneratePdf'));

        $data = [
            'id_pago' => 2,
            'client' => [
                'tipo_doc' => '1',
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

        $boleta = $service->createComprobante($data, 'boleta');

        // Assert that the returned object is an instance of Invoice
        $this->assertInstanceOf(Invoice::class, $boleta);

        // Assert specific properties of the Invoice object
        $this->assertEquals('03', $boleta->getTipoDoc());
        $this->assertEquals('B001', $boleta->getSerie());
        $this->assertEquals('1', $boleta->getCorrelativo());
        $this->assertEquals('0101', $boleta->getTipoOperacion());
        $this->assertEquals('PEN', $boleta->getTipoMoneda());
        $this->assertEquals(100.00, $boleta->getMtoOperGravadas());
        $this->assertEquals(18.00, $boleta->getMtoIGV());
        $this->assertEquals(118.00, $boleta->getMtoImpVenta());

        // Assert client data
        $client = $boleta->getClient();
        $this->assertEquals('1', $client->getTipoDoc());
        $this->assertEquals('61883939', $client->getNumDoc());
        $this->assertEquals('Anthony Marck Mendoza Sanchez', $client->getRznSocial());

        // Assert item details
        $details = $boleta->getDetails();
        $this->assertCount(1, $details);
        $this->assertEquals('P001', $details[0]->getCodProducto());
        $this->assertEquals(2, $details[0]->getCantidad());
        $this->assertEquals(50.00, $details[0]->getMtoValorUnitario());

        // Assert legend
        $legends = $boleta->getLegends();
        $this->assertCount(1, $legends);
        $this->assertEquals('1000', $legends[0]->getCode());
        $this->assertEquals('SON CIENTO DIECIOCHO CON 00/100 SOLES', $legends[0]->getValue());
    }
}