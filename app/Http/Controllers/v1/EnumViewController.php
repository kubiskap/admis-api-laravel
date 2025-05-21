<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\v1\APIBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

/**
 * Class EnumViewController
 *
 * Provides endpoints to interact with enum or view resources.
 *
 * @package App\Http\Controllers\v1
 *
 * @OA\Tag(
 *     name="Resources",
 *     description="Endpoints for enum and view resources."
 * )
 */
class EnumViewController extends APIBaseController
{
    /**
     * Retrieves one or more resources from the specified model.
     *
     * @OA\Get(
     *     path="/api/v1/{type}/{model}/{id}",
     *     tags={"Resources"},
     *     summary="Get resource(s)",
     *     description="Retrieves a single resource by ID or a collection of resources from the specified model type. Supports filtering, sorting, and pagination.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         description="Resource type: either 'enums' or 'views'",
     *         @OA\Schema(type="string", enum={"enums", "views"})
     *     ),
     *     @OA\Parameter(
     *         name="model",
     *         in="path",
     *         required=true,
     *         description="Model name (e.g., 'roleType', 'projectDetails')",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=false,
     *         description="Resource identifier; if omitted, returns all resources",
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
     *         description="Value to use for filtering (partial matching)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_field",
     *         in="query",
     *         required=false,
     *         description="Field to sort by; defaults to primary key",
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
     *         description="Resource(s) retrieved successfully",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *                     @OA\Property(property="meta", type="object", @OA\Property(property="pagination", type="object"))
     *                 ),
     *                 @OA\Schema(type="object")
     *             }
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
     *
     * @param Request $request
     * @param string $type
     * @param string $model
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
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

        // Apply filtering if provided.
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

        // Apply sorting if provided.
        $sortField = $request->query('sort_field', $primaryKey);
        $sortOrder = (int) $request->query('sort_order', 1) === -1 ? 'desc' : 'asc';
        $query->orderBy($sortField, $sortOrder);

        // Apply pagination if requested.
        if ($request->has('page') || $request->has('per_page')) {
            $perPage = (int) $request->query('per_page', 10);
            $resources = $query->paginate($perPage);
            return response()->json($resources);
        }

        $resources = $query->get();
        return response()->json($resources);
    }

    /**
     * Creates a new resource for enum types.
     *
     * @OA\Post(
     *     path="/api/v1/{type}/{model}",
     *     tags={"Resources"},
     *     summary="Create a new resource",
     *     description="Creates a new resource in the specified model type (enum).",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         description="Resource type: enums",
     *         @OA\Schema(type="string", enum={"enums"})
     *     ),
     *     @OA\Parameter(
     *         name="model",
     *         in="path",
     *         required=true,
     *         description="Model name (e.g., 'roleType')",
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
     *
     * @param Request $request
     * @param string $type
     * @param string $model
     * @return \Illuminate\Http\JsonResponse
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
     * Updates an existing resource.
     *
     * @OA\Put(
     *     path="/api/v1/{type}/{model}/{id}",
     *     tags={"Resources"},
     *     summary="Update a resource",
     *     description="Updates an existing resource with provided data.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         description="Resource type (enum or view)",
     *         @OA\Schema(type="string", enum={"enums", "views"})
     *     ),
     *     @OA\Parameter(
     *         name="model",
     *         in="path",
     *         required=true,
     *         description="Model name (e.g., 'roleType')",
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
     *
     * @param Request $request
     * @param string $type
     * @param string $model
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
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
     * Deletes an existing resource.
     *
     * @OA\Delete(
     *     path="/api/v1/{type}/{model}/{id}",
     *     tags={"Resources"},
     *     summary="Delete a resource",
     *     description="Permanently deletes the specified resource.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         description="Resource type (enum or view)",
     *         @OA\Schema(type="string", enum={"enums", "views"})
     *     ),
     *     @OA\Parameter(
     *         name="model",
     *         in="path",
     *         required=true,
     *         description="Model name (e.g., 'roleType')",
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
     *
     * @param Request $request
     * @param string $type
     * @param string $model
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
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

    /**
     * Retrieves related resources from a model's relationship.
     *
     * @OA\Get(
     *     path="/api/v1/{type}/{model}/{id}/{relation}",
     *     tags={"Resources"},
     *     summary="Get related resources",
     *     description="Retrieves related resources through a model's relationship. Supports filtering, sorting, and pagination.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         description="Resource type: either 'enums' or 'views'",
     *         @OA\Schema(type="string", enum={"enums", "views"})
     *     ),
     *     @OA\Parameter(
     *         name="model",
     *         in="path",
     *         required=true,
     *         description="Model name (e.g., 'projectType')",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Resource identifier",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="relation",
     *         in="path",
     *         required=true,
     *         description="Relationship name (e.g., 'subtypes')",
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
     *         description="Related resources retrieved successfully",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *                     @OA\Property(property="meta", type="object", @OA\Property(property="pagination", type="object"))
     *                 ),
     *                 @OA\Schema(type="array", @OA\Items(type="object"))
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Model, resource, or relation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Resource not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid resource type or relation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid resource type or relation")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @param string $type
     * @param string $model
     * @param mixed $id
     * @param string $relation
     * @return \Illuminate\Http\JsonResponse
     */
    public function relation(Request $request, $type, $model, $id, $relation)
    {
        if (!in_array($type, ['enums', 'views'])) {
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

        // Check if the relation method exists
        if (!method_exists($resource, $relation)) {
            return response()->json(['message' => 'Relation not found'], 404);
        }

        // Get the relation
        $relationQuery = $resource->$relation();
        
        // Get the base query if it's a relation
        if (method_exists($relationQuery, 'getQuery')) {
            $query = $relationQuery->getQuery();
            
            // Apply sorting if provided
            $sortField = $request->query('sort_field', 'name');
            $sortOrder = (int) $request->query('sort_order', 1) === -1 ? 'desc' : 'asc';
            $query->orderBy($sortField, $sortOrder);

            // Apply pagination if requested
            if ($request->has('page') || $request->has('per_page')) {
                $perPage = (int) $request->query('per_page', 10);
                $relatedResources = $relationQuery->paginate($perPage);
                return response()->json($relatedResources);
            }

            // Get all results
            $relatedResources = $relationQuery->get();
            return response()->json($relatedResources);
        } 
        
        // If it's already executed relation (not a query builder)
        $relatedResources = $resource->$relation;
        return response()->json($relatedResources);
    }

    /**
     * Resolves the fully qualified model class name based on type and model parameters.
     *
     * @param string $type The resource type: 'enums' or 'views'.
     * @param string $model The model name (e.g., 'roleType').
     * @return string|null Fully qualified class name if exists, otherwise null.
     */
    private function resolveModel($type, $model)
    {
        $namespace = $type === 'enums' ? 'App\\Models\\Enums\\' : 'App\\Models\\Views\\';
        $modelClass = $namespace . Str::studly($model);
        return class_exists($modelClass) ? $modelClass : null;
    }
}