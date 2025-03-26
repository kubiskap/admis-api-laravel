<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\ProjectResource;
use App\Http\Resources\ActionLogResource;
use App\Models\Project\Project;

/**
 * @OA\Tag(
 *     name="Projects",
 *     description="API endpoints for managing projects"
 * )
 */
class ProjectController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/projects/{id}",
     *     summary="Get project details",
     *     description="Retrieves details of a single project by ID",
     *     operationId="show",
     *     tags={"Projects"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Project ID to retrieve a single project",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */

     public function show($id)
    {
        $project = Project::with([
            'projectType',
            'projectSubtype',
            'financialSource',
            'financialSourcePD',
            'phase',
            'editorUser',
            'authorUser',
            'areas',
            'prices',
            'deadlines',
            'communications',
            'suspensions',
            'tasks',
            'contacts',
            'companies',
        ])
        ->findOrFail($id);

        return new ProjectResource($project);
    }


    /**
     * @OA\Post(
     *     path="/v1/projects/search",
     *     summary="Search projects with filters",
     *     description="Search projects using various filters provided in the request body",
     *     operationId="searchProjects",
     *     tags={"Projects"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="sort_field",
     *         in="query",
     *         description="Field to sort by",
     *         required=false,
     *         @OA\Schema(type="string", default="idProject")
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Sort order: 1 for ascending, -1 for descending",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="filter",
     *                 type="object",
     *                 @OA\Property(
     *                     property="project",
     *                     type="object",
     *                     @OA\Property(property="id", type="array", @OA\Items(type="integer")),
     *                     @OA\Property(property="editor", type="array", @OA\Items(type="string")),
     *                     @OA\Property(property="ou", type="array", @OA\Items(type="integer")),
     *                     @OA\Property(property="type", type="array", @OA\Items(type="integer")),
     *                     @OA\Property(property="subtype", type="array", @OA\Items(type="integer")),
     *                     @OA\Property(property="phase", type="array", @OA\Items(type="integer")),
     *                     @OA\Property(property="financialSource", type="array", @OA\Items(type="integer"))
     *                 ),
     *                 @OA\Property(
     *                     property="related",
     *                     type="object",
     *                     @OA\Property(property="communications", type="array", @OA\Items(type="integer")),
     *                     @OA\Property(property="areas", type="array", @OA\Items(type="integer"))
     *                 ),
     *                 @OA\Property(
     *                     property="companies",
     *                     type="object",
     *                     @OA\Property(property="supervisor", type="array", @OA\Items(type="integer")),
     *                     @OA\Property(property="builder", type="array", @OA\Items(type="integer")),
     *                     @OA\Property(property="project", type="array", @OA\Items(type="integer"))
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function search(Request $request)
    {
        $perPage = (int) $request->input('per_page', 15); // Ensure per_page is an integer
        $page = (int) $request->input('page', 1); // Ensure page is an integer
        $filters = $request->input('filter', []);
        $sortField = $request->input('sort_field', 'idProject');
        $sortOrder = (int) $request->input('sort_order', 1) === -1 ? 'desc' : 'asc';

        $query = Project::whereNull('deletedDate')
             ->with([
                 'projectType',
                 'projectSubtype',
                 'financialSource',
                 'phase',
                 'editorUser',
                 'areas',
                 'communications',
             ]);

        // Apply filters
        if (!empty($filters['project'])) {
            $projectFilters = $filters['project'];
            if (!empty($projectFilters['id'])) {
                $query->whereIn('idProject', (array)$projectFilters['id']);
            }
            if (!empty($projectFilters['editor'])) {
                $query->whereIn('editor', (array)$projectFilters['editor']);
            }
            if (!empty($projectFilters['ou'])) {
                $query->whereHas('editorUser', function ($q) use ($projectFilters) {
                    $q->whereIn('idOu', (array)$projectFilters['ou']);
                });
            }
            if (!empty($projectFilters['type'])) {
                $query->whereIn('idProjectType', (array)$projectFilters['type']);
            }
            if (!empty($projectFilters['subtype'])) {
                $query->whereIn('idProjectSubtype', (array)$projectFilters['subtype']);
            }
            if (!empty($projectFilters['phase'])) {
                $query->whereIn('idPhase', (array)$projectFilters['phase']);
            }
            if (!empty($projectFilters['financialSource'])) {
                $query->whereIn('idFinSource', (array)$projectFilters['financialSource']);
            }
        }

        if (isset($filters['related'])) {
            $relatedFilters = $filters['related'];
            if (!empty($relatedFilters['communications'])) {
                $query->whereHas('communications', function ($q) use ($relatedFilters) {
                    $q->whereIn('project2communication.idCommunication', (array)$relatedFilters['communications']);
                });
            }
            if (!empty($relatedFilters['areas'])) {
                $query->whereHas('areas', function ($q) use ($relatedFilters) {
                    $q->whereIn('project2area.idArea', (array)$relatedFilters['areas']);
                });
            }
        }

        if (isset($filters['companies'])) {
            $companyFilters = $filters['companies'];
            if (!empty($companyFilters['supervisor'])) {
                $query->whereHas('companies', function ($q) use ($companyFilters) {
                    $q->where('idCompanyType', 3)
                      ->whereIn('project2company.idCompany', (array)$companyFilters['supervisor']);
                });
            }
            if (!empty($companyFilters['builder'])) {
                $query->whereHas('companies', function ($q) use ($companyFilters) {
                    $q->where('idCompanyType', 2)
                      ->whereIn('project2company.idCompany', (array)$companyFilters['builder']);
                });
            }
            if (!empty($companyFilters['project'])) {
                $query->whereHas('companies', function ($q) use ($companyFilters) {
                    $q->where('idCompanyType', 1)
                      ->whereIn('project2company.idCompany', (array)$companyFilters['project']);
                });
            }
        }

        // Apply sorting
        $query->orderBy($sortField, $sortOrder);

        // Paginate results
        $projects = $query->paginate($perPage, ['*'], 'page', $page);

        return ProjectResource::collection($projects);
    }

    /**
     * @OA\Get(
     *     path="/v1/projects/{id}/editors-history",
     *     summary="Get distinct authors who edited the project with their latest edit date",
     *     description="Retrieves a list of unique authors who have created versions of the project, including their latest edit date",
     *     operationId="getProjectEditorsHistory",
     *     tags={"Projects"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Project ID to retrieve editors for",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="author", type="string"),
     *                 @OA\Property(property="latest_date", type="string", format="date")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Project not found"
     *     )
     * )
     */
    public function editorsHistory(Request $request, $id)
    {
        // Check if project exists
        $project = \App\Models\Project\Project::findOrFail($id);

        // Get distinct authors with their latest edit date
        $editors = $project->versions()
            ->select('author as editor')
            ->selectRaw('MAX(created) as date')
            ->groupBy('author')
            ->orderBy('author')
            ->get();

        return response()->json($editors);
    }

    /**
     * @OA\Get(
     *     path="/v1/projects/{id}/log",
     *     summary="Get project action logs",
     *     description="Retrieves a list of all actions performed on the project, including action type, user, and timestamp",
     *     operationId="getProjectLog",
     *     tags={"Projects"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Project ID to retrieve logs for",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="sort_field",
     *         in="query",
     *         description="Field to sort by (idAction, created, username, action_type)",
     *         required=false,
     *         @OA\Schema(type="string", default="created")
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Sort order: 1 for ascending, -1 for descending",
     *         required=false,
     *         @OA\Schema(type="integer", default=-1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="idAction", type="integer"),
     *                     @OA\Property(property="project_name", type="string"),
     *                     @OA\Property(property="created", type="string", format="date-time"),
     *                     @OA\Property(property="action_type", type="string"),
     *                     @OA\Property(property="username", type="string")
     *                 )
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Project not found"
     *     )
     * )
     */
    public function projectLog(Request $request, $id)
    {
        // Check if project exists
        $project = Project::findOrFail($id);

        $query = $project->actions();

        // Apply sorting
        $sortField = $request->query('sort_field', 'date');
        $sortOrder = (int) $request->query('sort_order', 1) === -1 ? 'desc' : 'asc';
        
        // Translate database column names to field names
        $fieldMap = [
            'date'            => 'actionsLogs.created',
            'user.username'   => 'actionsLogs.username',
            'action'          => 'rangeActionTypes.name',
            'project.name'    => 'projects.name',
        ];
        $dbColumn = $fieldMap[$sortField] ?? 'actionsLogs.created';
        
        // Join tables for sorting
        if ($sortField === 'user.username') {
            // join users to get `users.username`
            $query->leftJoin('users', 'actionsLogs.username', '=', 'users.username');
        } elseif ($sortField === 'action') {
            // join rangeActionTypes to get `rangeActionTypes.name`
            $query->leftJoin('rangeActionTypes', 'actionsLogs.idActionType', '=', 'rangeActionTypes.idActionType');
        } elseif ($sortField === 'project.name') {
            // join projectVersions -> projects to get `projects.name`
            $query->leftJoin('projectVersions', 'actionsLogs.idLocalProject', '=', 'projectVersions.idLocalProject')
                  ->leftJoin('projects', 'projectVersions.idProject', '=', 'projects.idProject');
        }

        $query->orderBy($dbColumn, $sortOrder);

        // Apply pagination
        $perPage = (int) $request->query('per_page', 15);
        $logs = $query->paginate($perPage);

        // Return resource
        return ActionLogResource::collection($logs);
    }
}
