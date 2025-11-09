<?php

namespace Tests\Feature\Transformers\Movies;

class FilmeTransformerTest extends \Tests\TestCase
{
    protected $filmeTransformer;

    public function setUp(): void
    {
        parent::setUp();
        $this->filmeTransformer = app(\App\Transformers\Movies\FilmeTransformer::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_transformer_transforms_filme_correctly(): void
    {
        $filme = $this->mockFilme();

        $transformed = $this->filmeTransformer->transform($filme);

        $this->assertEquals($filme->uuid, $transformed['id']);
        $this->assertEquals($filme->id, $transformed['code']);
        $this->assertEquals($filme->titulo, $transformed['title']);
        $this->assertEquals($filme->diretor, $transformed['director']);
        $this->assertEquals($filme->sinopse, $transformed['description']);
        $this->assertEquals($filme->ano_lancamento, $transformed['release_year']);
        $this->assertEquals($filme->categoria, $transformed['genre']);
    }

    public function test_original_attribute_maps_correctly(): void
    {
        $filme = $this->mockFilme();

        $atributes_model = $filme->getAttributes();

        $this->assertEquals('uuid', array_key_exists('uuid', $atributes_model));
        $this->assertEquals('id', array_key_exists('id', $atributes_model));
        $this->assertEquals('titulo', array_key_exists('titulo', $atributes_model));
        $this->assertEquals('diretor', array_key_exists('diretor', $atributes_model));
        $this->assertEquals('sinopse', array_key_exists('sinopse', $atributes_model));
        $this->assertEquals('ano_lancamento', array_key_exists('ano_lancamento', $atributes_model));
        $this->assertEquals('categoria', array_key_exists('categoria', $atributes_model));
        $this->assertEquals('quantidade', array_key_exists('quantidade', $atributes_model));

        $transformed = $this->filmeTransformer->transform($filme);

        $this->assertTrue(array_key_exists('id', $transformed));
        $this->assertTrue(array_key_exists('code', $transformed));
        $this->assertTrue(array_key_exists('title', $transformed));
        $this->assertTrue(array_key_exists('director', $transformed));
        $this->assertTrue(array_key_exists('description', $transformed));
        $this->assertTrue(array_key_exists('release_year', $transformed));
        $this->assertTrue(array_key_exists('genre', $transformed));
        $this->assertTrue(array_key_exists('quantity', $transformed));
        $this->assertTrue(array_key_exists('rental_price', $transformed));
    }
}
