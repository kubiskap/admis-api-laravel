<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Resources\ProjectResource;
use App\Http\Resources\ActionLogResource;
use App\Http\Resources\CommunicationGeometryResource;

use App\Models\Logs\ActionLog;
use App\Models\Project\Project;
use App\Models\Pivots\ProjectCommunication;

use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;


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
     *     path="/api/v1/projects/{id}",
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
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="type", type="string"),
     *                 @OA\Property(property="subtype", type="string", nullable=true),
     *                 @OA\Property(property="in_concept", type="boolean"),
     *                 @OA\Property(
     *                     property="phase",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="color", type="string"),
     *                     @OA\Property(property="color_class", type="string")
     *                 ),
     *                 @OA\Property(
     *                     property="editor",
     *                     type="object",
     *                     @OA\Property(property="username", type="string"),
     *                     @OA\Property(property="name", type="string")
     *                 ),
     *                 @OA\Property(
     *                     property="author",
     *                     type="object",
     *                     @OA\Property(property="username", type="string"),
     *                     @OA\Property(property="name", type="string")
     *                 ),
     *                 @OA\Property(property="priority_attributes", type="object"),
     *                 @OA\Property(property="financial_source", type="string"),
     *                 @OA\Property(property="financial_source_pd", type="string"),
     *                 @OA\Property(property="areas", type="array", @OA\Items(type="string")),
     *                 @OA\Property(
     *                     property="communications",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="stationing_from", type="number", format="float"),
     *                         @OA\Property(property="stationing_to", type="number", format="float"),
     *                         @OA\Property(property="gps_n1", type="number", format="float"),
     *                         @OA\Property(property="gps_n2", type="number", format="float"),
     *                         @OA\Property(property="gps_e1", type="number", format="float"),
     *                         @OA\Property(property="gps_e2", type="number", format="float"),
     *                         @OA\Property(property="allPointsWgs", type="string", nullable=true),
     *                         @OA\Property(property="allPointsSjtsk", type="string", nullable=true),
     *                         @OA\Property(property="geometryWgs", type="string", nullable=true),
     *                         @OA\Property(property="geometrySjtsk", type="string", nullable=true)
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="contacts",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="phone", type="string"),
     *                         @OA\Property(property="email", type="string"),
     *                         @OA\Property(
     *                             property="type",
     *                             type="object",
     *                             @OA\Property(property="name", type="string")
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="companies",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="address", type="string"),
     *                         @OA\Property(property="ic", type="string"),
     *                         @OA\Property(property="dic", type="string"),
     *                         @OA\Property(property="www", type="string"),
     *                         @OA\Property(
     *                             property="type",
     *                             type="object",
     *                             @OA\Property(property="name", type="string")
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(property="prices", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="deadlines", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="suspensions", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="tasks", type="array", @OA\Items(type="object"))
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
     *     path="/api/v1/projects/search",
     *     summary="Search projects with filters",
     *     description="Search projects using various filters provided in the request body. Only projects with no deletedDate are returned.",
     *     operationId="searchProjects",
     *     tags={"Projects"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="page",
     *                 type="integer",
     *                 default=1,
     *                 description="Page number"
     *             ),
     *             @OA\Property(
     *                 property="per_page",
     *                 type="integer",
     *                 default=15,
     *                 description="Number of items per page"
     *             ),
     *             @OA\Property(
     *                 property="sort_field",
     *                 type="string",
     *                 default="idProject",
     *                 description="Field to sort by"
     *             ),
     *             @OA\Property(
     *                 property="sort_order",
     *                 type="integer",
     *                 default=1,
     *                 description="Sort order: 1 for ascending, -1 for descending"
     *             ),
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
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="type", type="string"),
     *                     @OA\Property(property="subtype", type="string", nullable=true),
     *                     @OA\Property(property="in_concept", type="boolean"),
     *                     @OA\Property(
     *                         property="phase",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="color", type="string"),
     *                         @OA\Property(property="color_class", type="string")
     *                     ),
     *                     @OA\Property(
     *                         property="editor",
     *                         type="object",
     *                         @OA\Property(property="username", type="string"),
     *                         @OA\Property(property="name", type="string")
     *                     ),
     *                     @OA\Property(property="communications", type="array", @OA\Items(type="object")),
     *                     @OA\Property(property="areas", type="array", @OA\Items(type="string"))
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="from", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="links", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="path", type="string"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="to", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */

    /**
     * Reusable filter method that detects if $query is for Project or ProjectCommunication.
     * If it's ProjectCommunication, filters are applied through the related 'project' relationship.
     */
    private function applyFilters(Request $request, $query)
    {
        $filters = $request->input('filter', []);
        $isProjectCommunication = ($query->getModel() instanceof ProjectCommunication);

        $applyProjectFilters = function ($q) use ($filters) {
            // Only include non-deleted projects
            $q->whereNull('deletedDate');

            // Project-level filters
            if (!empty($filters['project'])) {
                $projectFilters = $filters['project'];

                if (!empty($projectFilters['id'])) {
                    $q->whereIn('idProject', (array) $projectFilters['id']);
                }
                if (!empty($projectFilters['editor'])) {
                    $q->whereIn('editor', (array) $projectFilters['editor']);
                }
                if (!empty($projectFilters['ou'])) {
                    $q->whereHas('editorUser', function ($subQ) use ($projectFilters) {
                        $subQ->whereIn('idOu', (array) $projectFilters['ou']);
                    });
                }
                if (!empty($projectFilters['type'])) {
                    $q->whereIn('idProjectType', (array) $projectFilters['type']);
                }
                if (!empty($projectFilters['subtype'])) {
                    $q->whereIn('idProjectSubtype', (array) $projectFilters['subtype']);
                }
                if (!empty($projectFilters['phase'])) {
                    $q->whereIn('idPhase', (array) $projectFilters['phase']);
                }
                if (!empty($projectFilters['financialSource'])) {
                    $q->whereIn('idFinSource', (array) $projectFilters['financialSource']);
                }
            }

            // Related filters
            if (isset($filters['related'])) {
                $relatedFilters = $filters['related'];
                if (!empty($relatedFilters['communications'])) {
                    $q->whereHas('communications', function ($subQ) use ($relatedFilters) {
                        $subQ->whereIn('project2communication.idCommunication', (array) $relatedFilters['communications']);
                    });
                }
                if (!empty($relatedFilters['areas'])) {
                    $q->whereHas('areas', function ($subQ) use ($relatedFilters) {
                        $subQ->whereIn('project2area.idArea', (array) $relatedFilters['areas']);
                    });
                }
            }

            // Company-related filters
            if (isset($filters['companies'])) {
                $companyFilters = $filters['companies'];
                if (!empty($companyFilters['supervisor'])) {
                    $q->whereHas('companies', function ($subQ) use ($companyFilters) {
                        $subQ->where('idCompanyType', 3)
                             ->whereIn('project2company.idCompany', (array) $companyFilters['supervisor']);
                    });
                }
                if (!empty($companyFilters['builder'])) {
                    $q->whereHas('companies', function ($subQ) use ($companyFilters) {
                        $subQ->where('idCompanyType', 2)
                             ->whereIn('project2company.idCompany', (array) $companyFilters['builder']);
                    });
                }
                if (!empty($companyFilters['project'])) {
                    $q->whereHas('companies', function ($subQ) use ($companyFilters) {
                        $subQ->where('idCompanyType', 1)
                             ->whereIn('project2company.idCompany', (array) $companyFilters['project']);
                    });
                }
            }
        };

        // If this is ProjectCommunication, apply project filters via `whereHas('project')`
        if ($isProjectCommunication) {
            $query->whereHas('project', function ($q) use ($applyProjectFilters) {
                $applyProjectFilters($q);
            });
        } else {
            // It's a Project query—apply filters directly
            $applyProjectFilters($query);
        }

        return $query;
    }

    public function search(Request $request)
    {
        $perPage = (int) $request->input('per_page', 15);
        $page = (int) $request->input('page', 1);
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

        // Apply filters using the reusable method
        $query = $this->applyFilters($request, $query);

        // Apply sorting
        $query->orderBy($sortField, $sortOrder);

        // Paginate results
        $projects = $query->paginate($perPage, ['*'], 'page', $page);

        return ProjectResource::collection($projects);
    }

    public function map(Request $request)
    {
        $spatialFilters = $request->input('filter.spatial', []);
        $boundingBox = $spatialFilters['bounding_box'] ?? null;
        $zoom = $spatialFilters['zoom'] ?? null;
    
        // Base query for ProjectCommunication, ensuring the project is not deleted
        $query = ProjectCommunication::query()
            ->whereHas('project', function ($q) {
                // Filter only projects with no deletedDate
                $q->whereNull('deletedDate');
            })
            ->with(['project.phase'])
            ->select('idProject', 'idCommunication', 'gpsN1', 'gpsN2', 'gpsE1', 'gpsE2');
    
        // Add geometryWgs if zoom level is more than 10
        if ($zoom >= 11) {
            $query->addSelect('geometryWgs');
        }
    
        // If a valid bounding box is provided, apply the spatial filter
        if (is_array($boundingBox) && count($boundingBox) === 4) {
            [$minLng, $minLat, $maxLng, $maxLat] = $boundingBox;
    
            $polygon = new Polygon([
                new LineString([
                    new Point($minLat, $minLng),
                    new Point($minLat, $maxLng),
                    new Point($maxLat, $maxLng),
                    new Point($maxLat, $minLng),
                    new Point($minLat, $minLng), // Close the polygon
                ])
            ]);
    
            // Apply the spatial filter
            $query->whereNotNull('geometryWgs')
            ->whereIntersects('geometryWgs', $polygon);
        }
    
        // Apply the reusable filters (they will run via project relationship)
        $query = $this->applyFilters($request, $query);
    
        // Fetch communications with related project
        $communications = $query->get();
    
        return CommunicationGeometryResource::collection($communications);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/projects/{id}/editors-history",
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
     *                 @OA\Property(property="editor", type="string"),
     *                 @OA\Property(property="date", type="string", format="date-time")
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
     *     path="/api/v1/projects/{id}/log",
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
     *         description="Field to sort by (date, user.username, action, project.name)",
     *         required=false,
     *         @OA\Schema(type="string", default="date")
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
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="date", type="string", format="date-time"),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="username", type="string"),
     *                         @OA\Property(property="name", type="string")
     *                     ),
     *                     @OA\Property(property="action", type="string"),
     *                     @OA\Property(
     *                         property="project",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="phase", type="string")
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="from", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="links", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="path", type="string"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="to", type="integer"),
     *                 @OA\Property(property="total", type="integer")
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

    public function store(Request $request)
    {

        $type = $request->query('type');

        // Validate the incoming request
        $rules = [
            'name' => 'required|string|max:255',
            'subject' => 'required|string',
            'project_type' => 'required|integer|exists:rangeProjectTypes,idProjectType',
            'project_subtype' => 'required|integer|exists:rangeProjectSubtypes,idProjectSubtype',
            'editor' => 'required|string|exists:users,username',
            'areas' => 'required|array',
            'areas.*' => 'integer|exists:rangeAreas,idArea',
            'communications' => 'required|array',
            'communications.*.idCommunication' => 'required|integer|exists:rangeCommunications,idCommunication',
            'communications.*.stationing_from' => 'required|numeric',
            'communications.*.stationing_to' => 'required|numeric',
            'communications.*.gps_n1' => 'required|numeric',
            'communications.*.gps_n2' => 'required|numeric',
            'communications.*.gps_e1' => 'required|numeric',
            'communications.*.gps_e2' => 'required|numeric',
            'communications.*.geometry' => 'nullable|string',
            'objects' => 'nullable|array',
            'objects.*.idObjectType' => 'required_with:objects|integer|exists:rangeObjectTypes,idObjectType',
            'objects.*.idObject' => 'required_with:objects|integer|exists:rangeObjects,idObject',
            'prices' => 'required|array',
            'prices.*.idPriceType' => 'required_with:prices|integer|exists:rangePriceTypes,idPriceType',
            'prices.*.idPrice' => 'required_with:prices|integer|exists:rangePrices,idPrice',
            'prices.*.value' => 'required_with:prices|numeric',
            'fin_source' => 'required|integer|exists:rangeFinancialSources,idFinSource',
            'relations' => 'nullable|array',
            'relations.*.idRelationType' => 'required_with:relations|integer|exists:rangeRelationTypes,idRelationType',
            'relations.*.idProjectRelation' => 'required_with:relations|integer|exists:projects,idProject',
        ];

        // Add rules for project-specific fields
        if ($type === 'namet' || $type === 'stavba' ) {
            $rules['fin_source_pd'] = 'required|integer|exists:rangeFinancialSources,idFinSource';
        }
        elseif ($type === 'udrzba') {
            $rules['fin_source_pd'] = 'nullable|integer|exists:rangeFinancialSources,idFinSource';
        }
        else {
            return response()->json(['error' => 'Invalid project type'], 400);
        }

        //Validate the request
        $validatedData = $request->validate($rules);

        // Create the project
        $project = Project::create([
            'idProjectType' => $validatedData['project_type'],
            'idProjectSubtype' => $validatedData['project_subtype'],
            'technologicalProjectType' => match ($type) {
                'namet' => 'topic',
                'stavba' => 'normal',
                'udrzba' => 'lite',
            },
            'created' => now(),
            'name' => $validatedData['name'],
            'subject' => $validatedData['subject'],
            'editor' => $validatedData['editor'],
            'author' => Auth::user()->username,
            'idFinSource' => $validatedData['fin_source'],
            'idFinSourcePD' => $validatedData['fin_source_pd'] ?? null,
            'idPhase' => 1,
            'idLocalProject' => 0,
            'inConcept' => false,
        ]);

        // Versionate the project
        $project->createVersion();

        // Attach areas to the project
        $project->areas()->sync($validatedData['areas']);

        // Attach communications to the project
        foreach ($validatedData['communications'] as $communication) {
            $project->communications()->attach($communication['idCommunication'], [
                'stationingFrom' => $communication['stationing_from'],
                'stationingTo' => $communication['stationing_to'],
                'gpsN1' => $communication['gps_n1'],
                'gpsN2' => $communication['gps_n2'],
                'gpsE1' => $communication['gps_e1'],
                'gpsE2' => $communication['gps_e2'],
                'geometryWgs' => $communication['geometry'] ?? null,
            ]);
        }

        foreach ($validatedData['prices'] as $price) {
            $project->prices()->attach($price['idPrice'], [
                'idPriceType' => $price['idPriceType'],
                'value' => $price['value'],
            ]);
        }

        

        // Insert into project relations
        if (!empty($validatedData['relations'])) {
            foreach ($validatedData['relations'] as $relation) {
                \App\Models\Project\ProjectRelation::create([
                    'username' => Auth::user()->username,
                    'idProject' => $project->idProject,
                    'idRelationType' => $relation['idRelationType'],
                    'idProjectRelation' => $relation['idProjectRelation'],
                    'created' => now(),
                ]);
            }
        }

        // Log the action in ActionLog
        ActionLog::create([
            'idActionType' => 1, // 1 is the action type for "Create Project"
            'idLocalProject' => $project->idLocalProject,
            'username' => Auth::user()->username, // Log the current user's username
            'created' => now(), // Log the timestamp
        ]);

        // Return the created project as a resource
        return new ProjectResource($project);
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'idProjectType' => 'sometimes|integer|exists:rangeProjectTypes,idProjectType',
            'idProjectSubtype' => 'nullable|integer|exists:rangeProjectSubtypes,idProjectSubtype',
            'idFinSource' => 'nullable|integer|exists:rangeFinancialSources,idFinSource',
            'idPhase' => 'nullable|integer|exists:rangePhases,idPhase',
            'inConcept' => 'nullable|boolean',
            'priorityAtts' => 'nullable|json',
            // Add other fields as necessary
        ]);

        // Find the project
        $project = Project::findOrFail($id);

        // Update the project fields
        $project->update(array_merge($validatedData, [
            'editor' => Auth::user()->username, // Update the editor to the current user
        ]));

        // Create a new version of the project
        $project->createVersion();

        // Log the action in ActionLog
        ActionLog::logAction(
            2, // 2 is the action type for "Edit Project"
            $project->idLocalProject // Use the updated idLocalProject from the project
        );

        // Return the updated project as a resource
        return new ProjectResource($project);
    }
}
