<?php

namespace Tests\Feature\Transformers\Users;

class UserTransformerTest extends \Tests\TestCase
{
    protected $transformer;

    public function setUp(): void
    {
        parent::setUp();
        $this->transformer = new \App\Transformers\Users\UsuarioTransformer();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_user_transformation(): void
    {
        $user = $this->mockUsuarioAdmin();

        $transformed = $this->transformer->transform($user);

        $this->assertIsArray($transformed);
        $this->assertEquals($user->uuid, $transformed['id']);
        $this->assertEquals($user->id, $transformed['code']);
        $this->assertEquals($user->name, $transformed['name']);
        $this->assertEquals($user->email, $transformed['email']);
        $this->assertEquals($user->role->value, $transformed['role']);
        $this->assertEquals($user->created_at, $transformed['created_at']);
        $this->assertEquals($user->updated_at, $transformed['updated_at']);
    }
}
