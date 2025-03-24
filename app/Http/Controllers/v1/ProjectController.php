<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     *     path="/v1/projects/{id?}",
     *     summary="Get all projects or a single project",
     *     description="Retrieves a paginated list of all projects or a single project if ID is provided",
     *     operationId="getProjects",
     *     tags={"Projects"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Project ID to retrieve a single project",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
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

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $query = \App\Models\Project\Project::whereNull('deletedDate');

        // Handle single project by ID from URL parameter or query parameter
        if ($request->route('id')) {
            $query->where('idProject', $request->route('id'));
        }

        // Eager load relationships
        $query->with([
            'projectType',
            'projectSubtype',
            'financialSource',
            'phase',
            'editorUser',
            'authorUser',
            'areas',
            'communications'
        ]);

        // Get paginated results
        $projects = $query->paginate($perPage);

        return response()->json($projects);
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
        $perPage = $request->query('per_page', 15);
        $query = \App\Models\Project\Project::whereNull('deletedDate')
            ->select([
                'idProject',
                'name',
                'idProjectType',
                'idPhase',
                'editor',
                'idFinSource',
                'idFinSourcePD',
                'priorityAtts'
            ]);

        // Handle JSON body filters
        if ($request->has('filter')) {
            $filters = $request->input('filter');

            // Project basic info filters
            if (isset($filters['project'])) {
                $projectFilters = $filters['project'];
                if (isset($projectFilters['id']) && !empty($projectFilters['id'])) {
                    $query->whereIn('idProject', (array)$projectFilters['id']);
                }
                if (isset($projectFilters['editor']) && !empty($projectFilters['editor'])) {
                    $query->whereIn('editor', (array)$projectFilters['editor']);
                }

                if (isset($projectFilters['ou']) && !empty($projectFilters['ou'])) {
                    $query->whereHas('editorUser', function ($q) use ($projectFilters) {
                        $q->whereIn('idOu', (array)$projectFilters['ou']);
                    });
                }

                if (isset($projectFilters['type']) && !empty($projectFilters['type'])) {
                    $query->whereIn('idProjectType', (array)$projectFilters['type']);
                }
                if (isset($projectFilters['subtype']) && !empty($projectFilters['subtype'])) {
                    $query->whereIn('idProjectSubtype', (array)$projectFilters['subtype']);
                }
                if (isset($projectFilters['phase']) && !empty($projectFilters['phase'])) {
                    $query->whereIn('idPhase', (array)$projectFilters['phase']);
                }
                if (isset($projectFilters['financialSource']) && !empty($projectFilters['financialSource'])) {
                    $query->whereIn('idFinSource', (array)$projectFilters['financialSource']);
                }
            }

            // Related entities filters
            if (isset($filters['related'])) {
                $relatedFilters = $filters['related'];
                if (isset($relatedFilters['communications']) && !empty($relatedFilters['communications'])) {
                    $query->whereHas('communications', function ($q) use ($relatedFilters) {
                        $q->whereIn('project2communication.idCommunication', (array)$relatedFilters['communications']);
                    });
                }
                if (isset($relatedFilters['areas']) && !empty($relatedFilters['areas'])) {
                    $query->whereHas('areas', function ($q) use ($relatedFilters) {
                        $q->whereIn('project2area.idArea', (array)$relatedFilters['areas']);
                    });
                }
            }

            // Company filters
            if (isset($filters['companies'])) {
                $companyFilters = $filters['companies'];
                if (isset($companyFilters['supervisor']) && !empty($companyFilters['supervisor'])) {
                    $query->whereHas('companies', function ($q) use ($companyFilters) {
                        $q->where('idCompanyType', 3)
                          ->whereIn('project2company.idCompany', (array)$companyFilters['supervisor']);
                    });
                }
                if (isset($companyFilters['builder']) && !empty($companyFilters['builder'])) {
                    $query->whereHas('companies', function ($q) use ($companyFilters) {
                        $q->where('idCompanyType', 2)
                          ->whereIn('project2company.idCompany', (array)$companyFilters['builder']);
                    });
                }
                if (isset($companyFilters['project']) && !empty($companyFilters['project'])) {
                    $query->whereHas('companies', function ($q) use ($companyFilters) {
                        $q->where('idCompanyType', 1)
                          ->whereIn('project2company.idCompany', (array)$companyFilters['project']);
                    });
                }
            }
        }

        // Handle ordering
        $sortField = $request->query('sort_field', 'idProject');
        $sortOrder = (int) $request->query('sort_order', 1) === -1 ? 'desc' : 'asc';
        $query->orderBy($sortField, $sortOrder);

        // Eager load only the required relationships with specific attributes
        $query->with([
            'projectType:idProjectType,name',
            'phase:idPhase,name,phaseColor,phaseColorClass',
            'editorUser:username,name',
            'financialSource:idFinSource,name',
            'areas:idArea,name',
            'communications'
        ]);

        // Get paginated results
        $projects = $query->paginate($perPage);

        return response()->json($projects);
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
        $editors = \App\Models\Project\ProjectVersion::where('idProject', $id)
            ->select('author as editor')
            ->selectRaw('DATE(MAX(created)) as date')
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
        $project = \App\Models\Project\Project::findOrFail($id);

        // Get paginated action logs for the project
        $perPage = $request->query('per_page', 15);
        
        $query = \App\Models\Logs\ActionLog::join('projectVersions', 'actionsLogs.idLocalProject', '=', 'projectVersions.idLocalProject')
            ->join('projects', 'projectVersions.idProject', '=', 'projects.idProject')
            ->join('rangeActionTypes', 'actionsLogs.idActionType', '=', 'rangeActionTypes.idActionType')
            ->where('projects.idProject', $id)
            ->select([
                'actionsLogs.idAction',
                'actionsLogs.created',
                'actionsLogs.username',
                'rangeActionTypes.name as action_type'
            ]);

        // Handle sorting
        $sortField = $request->query('sort_field', 'created');
        $sortOrder = (int) $request->query('sort_order', -1) === -1 ? 'desc' : 'asc';
        
        // Map frontend field names to database column names
        $fieldMap = [
            'idAction' => 'actionsLogs.idAction',
            'created' => 'actionsLogs.created',
            'username' => 'actionsLogs.username',
            'action_type' => 'rangeActionTypes.name'
        ];

        // Use mapped field name if it exists, otherwise use the original field name
        $sortField = $fieldMap[$sortField] ?? $sortField;
        $query->orderBy($sortField, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * @OA\Get(
     *     path="/v1/projects/{id}/companies",
     *     summary="Get project companies",
     *     description="Retrieves all companies associated with a specific project",
     *     operationId="getProjectCompanies",
     *     tags={"Projects"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Project ID to retrieve companies for",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="object")
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
    public function companies(Request $request, $id)
    {
        $project = \App\Models\Project\Project::findOrFail($id);
        $companies = $project->companies;
        
        // Transform companies to include type directly
        $companies->each(function ($company) {
            $company->pivot->load('companyType');
            $company->type = $company->pivot->companyType ? $company->pivot->companyType->name : null;
            unset($company->pivot);
        });

        return response()->json($companies);
    }

    /**
     * @OA\Get(
     *     path="/v1/projects/{id}/contacts",
     *     summary="Get project contacts",
     *     description="Retrieves all contacts associated with a specific project",
     *     operationId="getProjectContacts",
     *     tags={"Projects"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Project ID to retrieve contacts for",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="object")
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
    public function contacts(Request $request, $id)
    {
        $project = \App\Models\Project\Project::findOrFail($id);
        $contacts = $project->contacts;
        
        // Transform contacts to include type directly
        $contacts->each(function ($contact) {
            $contact->pivot->load('contactType');
            $contact->type = $contact->pivot->contactType ? $contact->pivot->contactType->name : null;
            unset($contact->pivot);
        });

        return response()->json($contacts);
    }
}

