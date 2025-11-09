<?php

namespace Tests\Feature\Transformers\Rental;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LocacaoTransformerTest extends TestCase
{
    protected $locacaoTransformer;

    public function setUp(): void
    {
        parent::setUp();
        $this->locacaoTransformer = new \App\Transformers\Rental\LocacaoTransformer();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_transformer_transforms_locacao_correctly(): void
    {
        $locacao = $this->mockLocacao();

        $transformed = $this->locacaoTransformer->transform($locacao);

        $this->assertEquals($locacao->uuid, $transformed['id']);
        $this->assertEquals($locacao->id, $transformed['code']);
        $this->assertArrayHasKey('client', $transformed);
        $this->assertArrayHasKey('id', $transformed['client']);
        $this->assertArrayHasKey('name', $transformed['client']);
        $this->assertArrayHasKey('email', $transformed['client']);
        $this->assertEquals($locacao->data_inicio, $transformed['rental_date']);
        $this->assertEquals($locacao->data_devolucao, $transformed['return_date']);
        $this->assertEquals($locacao->status, $transformed['status']);
        $this->assertEquals($locacao->multa, $transformed['penalty']);
    }

    public function test_original_attribute_maps_correctly(): void
    {
        $locacao = $this->mockLocacao();

        $atributes_model = $locacao->getAttributes();

        $this->assertEquals('uuid', array_key_exists('uuid', $atributes_model));
        $this->assertEquals('id', array_key_exists('id', $atributes_model));
        $this->assertEquals('usuario_id', array_key_exists('usuario_id', $atributes_model));
        $this->assertEquals('data_inicio', array_key_exists('data_inicio', $atributes_model));
        $this->assertEquals('data_devolucao', array_key_exists('data_devolucao', $atributes_model));
        $this->assertEquals('valor_total', array_key_exists('valor_total', $atributes_model));
        $this->assertEquals('multa', array_key_exists('multa', $atributes_model));
        $this->assertEquals('status', array_key_exists('status', $atributes_model));

        $transformed = $this->locacaoTransformer->transform($locacao);

        $this->assertTrue(array_key_exists('id', $transformed));
        $this->assertTrue(array_key_exists('code', $transformed));
        $this->assertTrue(array_key_exists('client', $transformed));
        $this->assertTrue(array_key_exists('rental_date', $transformed));
        $this->assertTrue(array_key_exists('return_date', $transformed));
        $this->assertTrue(array_key_exists('total_value', $transformed));
        $this->assertTrue(array_key_exists('penalty', $transformed));
        $this->assertTrue(array_key_exists('status', $transformed));
    }
}
