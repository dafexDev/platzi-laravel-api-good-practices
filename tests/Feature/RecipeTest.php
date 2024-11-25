<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Recipe;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RecipeTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_index()
    {
        Sanctum::actingAs(User::factory()->create());

        Category::factory()->create();

        Recipe::factory(2)->create();

        $response = $this->getJson('/api/recipes');
        $response->assertStatus(Response::HTTP_OK) // 200
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'type',
                        'attributes' => [
                            'category',
                            'title',
                            'description',
                            'ingredients',
                            'instructions',
                            'image'
                        ]
                    ]
                ]
            ]);
    }

    public function test_store()
    {
        Sanctum::actingAs(User::factory()->create());

        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $data = [
            'category_id'  => $category->id,
            'title'        => $this->faker->sentence,
            'description'  => $this->faker->paragraph,
            'ingredients'  => $this->faker->text,
            'instructions' => $this->faker->text,
            'tags'         => $tag->id,
            'image'        => UploadedFile::fake()->image('recipe.jpg')
        ];

        $response = $this->postJson('/api/recipes', $data);
        $response->assertStatus(Response::HTTP_CREATED); // 201
    }

    public function test_store_wrongMimeType()
    {
        Sanctum::actingAs(User::factory()->create());

        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $data = [
            'category_id'  => $category->id,
            'title'        => $this->faker->sentence,
            'description'  => $this->faker->paragraph,
            'ingredients'  => $this->faker->text,
            'instructions' => $this->faker->text,
            'tags'         => $tag->id,
            'image'        => UploadedFile::fake()->image('recipe.png')
        ];

        $response = $this->postJson('/api/recipes', $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY); // 422
    }

    public function test_show()
    {
        Sanctum::actingAs(User::factory()->create());

        Category::factory()->create();

        $recipe = Recipe::factory()->create();

        $response = $this->getJson('/api/recipes/' . $recipe->id);
        $response->assertStatus(Response::HTTP_OK) // 200
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'type',
                    'attributes' => [
                        'category',
                        'title',
                        'description',
                        'ingredients',
                        'instructions',
                        'image'
                    ]
                ]
            ]);
    }

    public function test_update()
    {
        Sanctum::actingAs(User::factory()->create());

        $category = Category::factory()->create();
        $recipe = Recipe::factory()->create();

        $data = [
            'category_id' => $category->id,
            'title' => 'updated title',
            'description' => 'updated description',
            'ingredients' => $this->faker->text(),
            'instructions' => $this->faker->text()
        ];

        $response = $this->putJson('/api/recipes/' . $recipe->id, $data);
        $response->assertStatus(Response::HTTP_OK); // 200

        $this->assertDatabaseHas('recipes', [
            'title' => 'updated title',
            'description' => 'updated description'
        ]);
    }

    public function test_destroy()
    {
        Sanctum::actingAs(User::factory()->create());

        Category::factory()->create();

        $recipe = Recipe::factory()->create();

        $response = $this->deleteJson('/api/recipes/' . $recipe->id);
        $response->assertStatus(Response::HTTP_NO_CONTENT); // 204
    }
}
