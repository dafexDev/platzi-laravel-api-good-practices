<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    public function test_index()
    {
        Sanctum::actingAs(User::factory()->create());

        Tag::factory(2)->create();

        $response = $this->getJson('/api/v1/tags');
        $response->assertStatus(Response::HTTP_OK) // 200
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'type',
                        'attributes' => ['name'],
                        'relationships' => [
                            'recipes' => []
                        ]
                    ]
                ]
            ]);
    }

    public function test_show()
    {
        Sanctum::actingAs(User::factory()->create());

        $tag = Tag::factory()->create();

        $response = $this->getJson('/api/v1/tags/' . $tag->id);
        $response->assertStatus(Response::HTTP_OK) // 200
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'type',
                    'attributes' => ['name'],
                    'relationships' => [
                        'recipes' => []
                    ]
                ]
            ]);
    }
}
