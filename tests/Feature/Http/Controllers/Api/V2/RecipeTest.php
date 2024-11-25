<?php

namespace Tests\Feature\Http\Controllers\Api\V2;

use App\Models\Category;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RecipeTest extends TestCase
{
    use RefreshDatabase;

    public function test_index()
    {
        Sanctum::actingAs(User::factory()->create());

        Category::factory()->create();

        Recipe::factory(5)->create();

        $response = $this->getJson('/api/v2/recipes');
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data'  => [],
                'links' => [],
                'meta'  => [],
            ]);
    }
}
