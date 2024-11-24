<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use App\Http\Resources\RecipeResource;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RecipeController extends Controller
{
    public function index()
    {
        $recipes = Recipe::with('category', 'tags', 'user')->get();

        return RecipeResource::collection($recipes);
    }

    public function store(StoreRecipeRequest $request)
    {
        $recipe = $request->user()->recipes()->create($request->validated());
        $recipe->tags()->attach(json_decode($request->tags));

        return response()->json(new RecipeResource($recipe), Response::HTTP_CREATED); // 201
    }

    public function show(Recipe $recipe)
    {
        $recipe = $recipe->load('category', 'tags', 'user');

        return new RecipeResource($recipe);
    }

    public function update(UpdateRecipeRequest $request, Recipe $recipe)
    {
        $this->authorize('update', $recipe);

        $recipe->update($request->validated());

        if ($tags = $request->tags) {
            $recipe->tags()->sync(json_decode($tags));
        }

        return response()->json(new RecipeResource($recipe), Response::HTTP_OK); // 200
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT); // 204
    }
}
