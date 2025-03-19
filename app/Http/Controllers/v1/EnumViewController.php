<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\v1\APIBaseController;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class EnumViewController extends APIBaseController
{
    /**
     * @OA\Get(
     *     path="/api/v1/{type}/{model}/{id}",
     *     tags={"Resources"},
     *     summary="Get resources",
     *     description="Retrieves resources from the specified model type (enum or view). Supports filtering, sorting, and pagination.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         description="Resource type (enum or view)",
     *         @OA\Schema(type="string", enum={"enum", "view"})
     *     ),
     *     @OA\Parameter(
     *         name="model",
     *         in="path",
     *         required=true,
     *         description="Model name (e.g., 'roleType', 'projectDetails', etc.)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=false,
     *         description="Resource identifier (optional - if not provided, returns all resources)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number for pagination",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="filter_field",
     *         in="query",
     *         required=false,
     *         description="Field to filter by",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filter_value",
     *         in="query",
     *         required=false,
     *         description="Value to filter by (uses partial matching)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_field",
     *         in="query",
     *         required=false,
     *         description="Field to sort by",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         required=false,
     *         description="Sort order: 1 for ascending, -1 for descending",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *                     @OA\Property(property="meta", type="object", @OA\Property(property="pagination", type="object"))
     *                 ),
     *                 @OA\Schema(type="object")
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource type, model, or resource not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Resource not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid resource type",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid resource type")
     *         )
     *     )
     * )
     */
    public function index(Request $request, $type, $model, $id = null)
    {
        if (!in_array($type, ['enums', 'views'])) {
            return response()->json(['message' => 'Invalid resource type'], 400);
        }

        $modelClass = $this->resolveModel($type, $model);
        if (!$modelClass) {
            return response()->json(['message' => 'Model not found'], 404);
        }

        $primaryKey = (new $modelClass)->getKeyName();

        if ($id) {
            $resource = $modelClass::where($primaryKey, $id)->first();
            if (!$resource) {
                return response()->json(['message' => 'Resource not found'], 404);
            }
            return response()->json($resource);
        }

        $query = $modelClass::query();

        // Add filter
        $filterField = $request->query('filter_field');
        $filterValue = $request->query('filter_value');
        if ($filterValue) {
            if ($filterField) {
                $query->where($filterField, 'like', '%' . $filterValue . '%');
            } else {
                $fields = Schema::getColumnListing((new $modelClass)->getTable());
                $query->where(function ($q) use ($fields, $filterValue) {
                    foreach ($fields as $field) {
                        $q->orWhere($field, 'like', '%' . $filterValue . '%');
                    }
                });
            }
        }

        $sortField = $request->query('sort_field', $primaryKey);
        $sortOrder = (int) $request->query('sort_order', 1) === -1 ? 'desc' : 'asc';
        $query->orderBy($sortField, $sortOrder);

        if ($request->has('page') || $request->has('per_page')) {
            $perPage = (int) $request->query('per_page', 10);
            $resources = $query->paginate($perPage);
            return response()->json($resources);
        }

        $resources = $query->get();
        return response()->json($resources);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/{type}/{model}",
     *     tags={"Resources"},
     *     summary="Create a new resource",
     *     description="Creates a new resource in the specified model type (enum or view)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         description="Resource type (enum or view)",
     *         @OA\Schema(type="string", enum={"enum", "view"})
     *     ),
     *     @OA\Parameter(
     *         name="model",
     *         in="path",
     *         required=true,
     *         description="Model name (e.g., 'roleType', 'projectDetails', etc.)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Resource data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\AdditionalProperties(type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Resource created successfully",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Model not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Model not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid resource type",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid resource type")
     *         )
     *     )
     * )
     */
    public function store(Request $request, $type, $model)
    {
        if ($type != 'enums') {
            return response()->json(['message' => 'Invalid resource type'], 400);
        }

        $modelClass = $this->resolveModel($type, $model);
        if (!$modelClass) {
            return response()->json(['message' => 'Model not found'], 404);
        }

        $resource = $modelClass::create($request->all());
        return response()->json($resource, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/{type}/{model}/{id}",
     *     tags={"Resources"},
     *     summary="Update a resource",
     *     description="Updates an existing resource with the provided data",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         description="Resource type (enum or view)",
     *         @OA\Schema(type="string", enum={"enum", "view"})
     *     ),
     *     @OA\Parameter(
     *         name="model",
     *         in="path",
     *         required=true,
     *         description="Model name (e.g., 'roleType', 'projectDetails', etc.)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Resource identifier",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated resource data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\AdditionalProperties(type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Resource updated successfully",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Model or resource not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Resource not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid resource type",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid resource type")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $type, $model, $id)
    {
        if ($type != 'enums') {
            return response()->json(['message' => 'Invalid resource type'], 400);
        }

        $modelClass = $this->resolveModel($type, $model);
        if (!$modelClass) {
            return response()->json(['message' => 'Model not found'], 404);
        }

        $primaryKey = (new $modelClass)->getKeyName();
        $resource = $modelClass::where($primaryKey, $id)->first();

        if (!$resource) {
            return response()->json(['message' => 'Resource not found'], 404);
        }

        $resource->update($request->all());
        return response()->json($resource);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/{type}/{model}/{id}",
     *     tags={"Resources"},
     *     summary="Delete a resource",
     *     description="Permanently deletes the specified resource",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         description="Resource type (enum or view)",
     *         @OA\Schema(type="string", enum={"enum", "view"})
     *     ),
     *     @OA\Parameter(
     *         name="model",
     *         in="path",
     *         required=true,
     *         description="Model name (e.g., 'roleType', 'projectDetails', etc.)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Resource identifier",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Resource deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Model or resource not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Resource not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid resource type",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid resource type")
     *         )
     *     )
     * )
     */
    public function destroy(Request $request, $type, $model, $id)
    {
        if ($type != 'enums') {
            return response()->json(['message' => 'Invalid resource type'], 400);
        }

        $modelClass = $this->resolveModel($type, $model);
        if (!$modelClass) {
            return response()->json(['message' => 'Model not found'], 404);
        }

        $primaryKey = (new $modelClass)->getKeyName();
        $resource = $modelClass::where($primaryKey, $id)->first();

        if (!$resource) {
            return response()->json(['message' => 'Resource not found'], 404);
        }

        $resource->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    private function resolveModel($type, $model)
    {
        $namespace = $type === 'enums' ? 'App\\Models\\Enums\\' : 'App\\Models\\Views\\';
        $modelClass = $namespace . Str::studly($model);
        return class_exists($modelClass) ? $modelClass : null;
    }
} 