<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\v1\APIBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Relations\Relation;

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
     *         name="filter",
     *         in="query",
     *         required=false,
     *         description="Filter results. Usage: `filter[column_name]=value`. Supports partial matching for string columns defined as filterable.",
     *         style="deepObject",
     *         explode=true,
     *         @OA\Schema(type="object", additionalProperties=@OA\Schema(type="string"))
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         required=false,
     *         description="Sort results. Usage: `sort=column_name` for ascending, `sort=-column_name` for descending. Default sort is by primary key, ascending.",
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
     *     @OA\Response(
     *         response=200,
     *         description="Resource(s) retrieved successfully",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     description="Paginated list of resources",
     *                     @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *                     @OA\Property(property="links", type="object",
     *                         @OA\Property(property="first", type="string", format="url", nullable=true),
     *                         @OA\Property(property="last", type="string", format="url", nullable=true),
     *                         @OA\Property(property="prev", type="string", format="url", nullable=true),
     *                         @OA\Property(property="next", type="string", format="url", nullable=true)
     *                     ),
     *                     @OA\Property(property="meta", type="object",
     *                         @OA\Property(property="current_page", type="integer"),
     *                         @OA\Property(property="from", type="integer", nullable=true),
     *                         @OA\Property(property="last_page", type="integer"),
     *                         @OA\Property(property="links", type="array", @OA\Items(type="object",
     *                              @OA\Property(property="url", type="string", format="url", nullable=true),
     *                              @OA\Property(property="label", type="string"),
     *                              @OA\Property(property="active", type="boolean")
     *                         )),
     *                         @OA\Property(property="path", type="string", format="url"),
     *                         @OA\Property(property="per_page", type="integer"),
     *                         @OA\Property(property="to", type="integer", nullable=true),
     *                         @OA\Property(property="total", type="integer")
     *                     )
     *                 ),
     *                 @OA\Schema(
     *                     type="array",
     *                     description="List of resources (if not paginated and no ID provided)",
     *                     @OA\Items(type="object")
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     description="Single resource (if ID is provided)"
     *                 )
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

        $instance = new $modelClass();
        $primaryKey = $instance->getKeyName();

        if ($id) {
            $resource = $modelClass::where($primaryKey, $id)->first();
            if (!$resource) {
                return response()->json(['message' => 'Resource not found'], 404);
            }
            return response()->json($resource);
        }

        $allowedFilters = [];
        $allowedSorts = [];
        $table = $instance->getTable();

        if (Schema::hasTable($table)) {
            $columns = Schema::getColumnListing($table);
            foreach ($columns as $column) {
                // By default, allow partial filtering for all columns.
                // For more specific filtering (exact, scope, etc.), configure AllowedFilter explicitly.
                $allowedFilters[] = AllowedFilter::partial($column);
            }
            $allowedSorts = $columns;
        }
        
        $query = QueryBuilder::for($modelClass)
            ->allowedFilters($allowedFilters)
            ->allowedSorts($allowedSorts)
            ->defaultSort($primaryKey);

        if ($request->filled('page') || $request->filled('per_page')) {
            $perPage = (int) $request->query('per_page', 10);
            $resources = $query->paginate($perPage)->appends($request->query());
        } else {
            $resources = $query->get();
        }

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
     *     description="Retrieves related resources through a model's relationship. Supports sorting and pagination.",
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
     *         name="sort",
     *         in="query",
     *         required=false,
     *         description="Sort results. Usage: `sort=column_name` for ascending, `sort=-column_name` for descending. Default sort is by 'name' or primary key of the related model.",
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
     *     @OA\Response(
     *         response=200,
     *         description="Related resources retrieved successfully",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     description="Paginated list of related resources",
     *                     @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *                     @OA\Property(property="links", type="object",
     *                         @OA\Property(property="first", type="string", format="url", nullable=true),
     *                         @OA\Property(property="last", type="string", format="url", nullable=true),
     *                         @OA\Property(property="prev", type="string", format="url", nullable=true),
     *                         @OA\Property(property="next", type="string", format="url", nullable=true)
     *                     ),
     *                     @OA\Property(property="meta", type="object",
     *                         @OA\Property(property="current_page", type="integer"),
     *                         @OA\Property(property="from", type="integer", nullable=true),
     *                         @OA\Property(property="last_page", type="integer"),
     *                         @OA\Property(property="links", type="array", @OA\Items(type="object",
     *                              @OA\Property(property="url", type="string", format="url", nullable=true),
     *                              @OA\Property(property="label", type="string"),
     *                              @OA\Property(property="active", type="boolean")
     *                         )),
     *                         @OA\Property(property="path", type="string", format="url"),
     *                         @OA\Property(property="per_page", type="integer"),
     *                         @OA\Property(property="to", type="integer", nullable=true),
     *                         @OA\Property(property="total", type="integer")
     *                     )
     *                 ),
     *                 @OA\Schema(
     *                     type="array",
     *                     description="List of related resources (if not paginated)",
     *                     @OA\Items(type="object")
     *                 )
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

        if (!method_exists($resource, $relation)) {
            return response()->json(['message' => 'Relation not found'], 404);
        }

        $relationInstance = $resource->$relation();

        if (!($relationInstance instanceof Relation)) {
            // If it's already a collection (e.g., from an accessor) or array, return it.
            if ($relationInstance instanceof \Illuminate\Support\Collection || is_array($relationInstance)) {
                return response()->json($relationInstance);
            }
            return response()->json(['message' => 'Invalid relation type for querying'], 400);
        }
        
        $allowedSorts = [];
        $defaultSortKey = 'id'; // Fallback default sort key

        $relatedModelInstance = $relationInstance->getRelated();
        $relatedTable = $relatedModelInstance->getTable();

        if (Schema::hasTable($relatedTable)) {
            $allowedSorts = Schema::getColumnListing($relatedTable);
            $defaultSortKey = $relatedModelInstance->getKeyName();
            if (in_array('name', $allowedSorts)) { // Prefer 'name' if it exists and is sortable
                $defaultSortKey = 'name';
            }
        }

        $query = QueryBuilder::for($relationInstance) // Pass the Eloquent Relation object
            ->allowedSorts($allowedSorts)
            ->defaultSort($defaultSortKey);
        
        // Note: Filtering for relations is not implemented here as it wasn't in the original code.
        // To add filtering, use ->allowedFilters() similar to the index method.

        if ($request->filled('page') || $request->filled('per_page')) {
            $perPage = (int) $request->query('per_page', 10);
            $relatedResources = $query->paginate($perPage)->appends($request->query());
        } else {
            $relatedResources = $query->get();
        }

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