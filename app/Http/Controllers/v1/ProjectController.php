<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

use App\Http\Resources\ProjectResource;
use App\Http\Resources\ActionLogResource;
use App\Http\Resources\CommunicationGeometryResource;

use App\Rules\WktLineString;

use App\Support\MapTolerance;
use App\Services\LineSimplifierService;

use App\Models\Logs\ActionLog;
use App\Models\Project\Project;
use App\Models\Pivots\ProjectCommunication;

use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;

use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedSort;

/**
 * Class ProjectController
 *
 * Handles project-related endpoints including retrieval, search, creation, updates,
 * and logging of project changes. The Swagger documentation refers to the resources
 * used in this controller (e.g., ProjectResource, ActionLogResource).
 *
 * @OA\Tag(
 *     name="Projects",
 *     description="API endpoints for managing projects."
 * )
 */
class ProjectController extends Controller
{
    /**
     * Retrieves details of a single project by ID.
     *
     * @OA\Get(
     *     path="/api/v1/projects/{id}",
     *     summary="Get project details",
     *     description="Retrieves details of a single project by ID.",
     *     operationId="showProject",
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
     *         @OA\JsonContent(ref="#/components/schemas/ProjectResource")
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
     *
     * @param int $id
     * @return ProjectResource
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
        ])->findOrFail($id);

        return new ProjectResource($project);
    }

    /**
     * Searches projects using various filters and paginates the results.
     *
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
     *             @OA\Property(property="page", type="integer", default=1, description="Page number"),
     *             @OA\Property(property="per_page", type="integer", default=15, description="Number of items per page"),
     *             @OA\Property(property="sort_field", type="string", default="idProject", description="Field to sort by"),
     *             @OA\Property(property="sort_order", type="integer", default=1, description="Sort order: 1 for ascending, -1 for descending"),
     *             @OA\Property(
     *                 property="filter",
     *                 type="object",
     *                 description="Filter object for project level and related filters",
     *                 @OA\Property(
     *                     property="project",
     *                     type="object",
     *                     description="Per-project filters",
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
     *                     description="Filters for related resources",
     *                     @OA\Property(property="communications", type="array", @OA\Items(type="integer")),
     *                     @OA\Property(property="areas", type="array", @OA\Items(type="integer"))
     *                 ),
     *                 @OA\Property(
     *                     property="companies",
     *                     type="object",
     *                     description="Filters for company relationships",
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
     *         @OA\JsonContent(ref="#/components/schemas/ProjectResource")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
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

        $query = $this->applyFilters($request, $query);
        $query->orderBy($sortField, $sortOrder);
        $projects = $query->paginate($perPage, ['*'], 'page', $page);

        return ProjectResource::collection($projects);
    }

    /**
     * Retrieves communication geometry data via spatial filtering.
     *
     * @OA\Get(
     *     path="/api/v1/projects/map",
     *     summary="Retrieve communications geometry",
     *     description="Retrieves a collection of project communication geometries based on spatial filters.",
     *     operationId="mapCommunications",
     *     tags={"Projects"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="filter[spatial][bounding_box]", in="query", description="Bounding box array [minLng, minLat, maxLng, maxLat]", required=false, @OA\Schema(type="array", @OA\Items(type="number"))),
     *     @OA\Parameter(name="filter[spatial][zoom]", in="query", description="Zoom level", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/CommunicationGeometryResource")),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function map(Request $request, LineSimplifierService $simplifier)
    {
        /* ------------------------------------------------------------------
         * 1.  Read spatial filter
         * -----------------------------------------------------------------*/
        $spatial  = $request->input('filter.spatial', []);
        $bbox     = $spatial['bounding_box'] ?? null;          // [minLng, minLat, maxLng, maxLat]
        $zoom     = (int) ($spatial['zoom'] ?? 13);
        $tol      = MapTolerance::forZoom($zoom);
    
        /* ------------------------------------------------------------------
         * 2.  Base query (only columns we really need)
         * -----------------------------------------------------------------*/
        $query = ProjectCommunication::query()
            ->whereHas('project', fn ($q) => $q->whereNull('deletedDate'))
            ->with('project.phase')
            ->select([
                'idProject',
                'idCommunication',
                'gpsN1', 'gpsN2', 'gpsE1', 'gpsE2',
                'geometryWgs',                              // full geometry
            ]);
    
        /* ------------------------------------------------------------------
         * 3.  Bounding-box spatial filter (index-friendly)
         * -----------------------------------------------------------------*/
        if (is_array($bbox) && count($bbox) === 4) {
            [$minLng, $minLat, $maxLng, $maxLat] = $bbox;
    
            $polyWkt = "POLYGON(($minLng $minLat, $maxLng $minLat, "
                     . "$maxLng $maxLat, $minLng $maxLat, $minLng $minLat))";
    
            // 1st stage – R-Tree friendly
            $query->whereRaw(
                'MBRIntersects(geometryWgs, ST_GeomFromText(?,0))',
                [$polyWkt]
            )
            // 2nd stage – exact test (still SRID 0)
            ->whereRaw(
                'ST_Intersects(geometryWgs, ST_GeomFromText(?,0))',
                [$polyWkt]
            );
        }
    
        /* ------------------------------------------------------------------
         * 4.  Apply the reusable filters you already have
         * -----------------------------------------------------------------*/
        $query = $this->applyFilters($request, $query);
    
        /* ------------------------------------------------------------------
         * 5.  Fetch and simplify in PHP (cached 10 s)
         * -----------------------------------------------------------------*/
        $communications = $query->get()->map(function ($comm) use ($simplifier, $tol) {
            if ($comm->geometryWgs) {
                // geometryWgs is a LineString object provided by Yadaev’s cast
                $wkt = $comm->geometryWgs->toWkt();
    
                $cacheKey = "simpl:{$comm->idCommunication}:{$tol}";
                $simplifiedWkt = Cache::remember($cacheKey, 10, function () use ($simplifier, $wkt, $tol) {
                    return $simplifier->simplifyToWkt($wkt, $tol) ?? $wkt;
                });
    
                // Re-hydrate back to a LineString so resources/casts work the same
                $comm->geometryWgs = LineString::fromWkt($simplifiedWkt);
            }
    
            return $comm;
        });
    
        return CommunicationGeometryResource::collection($communications);
    }

    /**
     * Retrieves distinct editors with their latest edit date for a given project.
     *
     * @OA\Get(
     *     path="/api/v1/projects/{id}/editors-history",
     *     summary="Get distinct project editors with latest edit date",
     *     description="Retrieves a list of unique project editors along with their latest edit date.",
     *     operationId="getProjectEditorsHistory",
     *     tags={"Projects"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="Project ID", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(type="array", @OA\Items(type="object", @OA\Property(property="editor", type="string"), @OA\Property(property="date", type="string", format="date-time")))),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Project not found")
     * )
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editorsHistory(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $editors = $project->versions()
            ->select('author as editor')
            ->selectRaw('MAX(created) as date')
            ->groupBy('author')
            ->orderBy('author')
            ->get();

        return response()->json($editors);
    }

    /**
     * Retrieves project action logs with pagination and sorting.
     *
     * @OA\Get(
     *     path="/api/v1/projects/{id}/log",
     *     summary="Get project action logs",
     *     description="Retrieves a list of project action logs including user, action type, and timestamp.",
     *     operationId="getProjectLog",
     *     tags={"Projects"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="Project ID", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", required=false, @OA\Schema(type="integer", default=15)),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort results. Usage: `sort=field` for ascending, `sort=-field` for descending. Allowed fields: `date`, `user.username`, `action`, `project.name`. Default: `-date`",
     *         required=false,
     *         @OA\Schema(type="string", default="-date")
     *     ),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/ActionLogResource")),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Project not found")
     * )
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function projectLog(Request $request, $id)
    {
        // Nejprve ověříme, zda projekt s daným ID existuje.
        // Toto je důležité pro kontext a pro vrácení 404, pokud projekt neexistuje.
        Project::findOrFail($id);

        // Základní dotaz nyní vychází přímo z modelu ActionLog.
        // Eager loading relací zůstává důležitý pro ActionLogResource a pro řazení.
        $query = ActionLog::query()->with([
            'user',         // Pro ActionLogResource: $this->user->name
            'actionType',   // Pro ActionLogResource: $this->actionType->name
            'project.phase' // Pro ActionLogResource: $this->project->name, $this->project->phase->name
                            // a také pro řazení podle 'project.name'
        ]);

        // Filtrujeme ActionLog záznamy, které patří k danému projektu.
        // Využíváme relaci project() definovanou v ActionLog modelu (ActionLog -> ProjectVersion -> Project).
        $query->whereHas('project', function ($q) use ($id) {
            $q->where('projects.idProject', $id);
        });

        $perPage = (int) $request->query('per_page', 15);

        $logs = QueryBuilder::for($query)
            ->allowedSorts([
                AllowedSort::field('date', 'created'), // Řadí podle sloupce 'created' v tabulce 'actionsLogs'
                AllowedSort::field('user.username', 'username'), // Řadí podle sloupce 'username' v tabulce 'actionsLogs'
                                                                // ActionLogResource zobrazuje user->name, ale řadíme podle username na ActionLog
                AllowedSort::field('action', 'actionType.name'), // Řadí podle 'name' z relace 'actionType'
                AllowedSort::field('project.name', 'project.name') // Řadí podle 'name' z relace 'project'
            ])
            ->defaultSort('-created') // Změněno z '-date' na '-created'
            ->paginate($perPage);

        return ActionLogResource::collection($logs);
    }

    /**
     * Creates a new project.
     *
     * @OA\Post(
     *     path="/api/v1/projects",
     *     operationId="storeProject",
     *     tags={"Projects"},
     *     summary="Create a new project",
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Project category (namet, stavba, udrzba)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"namet","stavba","udrzba"})
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","subject","project_type","project_subtype","editor","areas","communications","prices","fin_source"},
     *             @OA\Property(property="name", type="string", maxLength=255),
     *             @OA\Property(property="subject", type="string"),
     *             @OA\Property(property="project_type", type="integer", description="ID of project type"),
     *             @OA\Property(property="project_subtype", type="integer", description="ID of project subtype"),
     *             @OA\Property(property="editor", type="string", description="Username of editor"),
     *             @OA\Property(
     *                 property="areas",
     *                 type="array",
     *                 @OA\Items(type="integer", description="ID of area")
     *             ),
     *             @OA\Property(
     *                 property="communications",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"id","stationing_from","stationing_to","gps_n1","gps_n2","gps_e1","gps_e2"},
     *                     @OA\Property(property="id", type="integer", description="ID of communication"),
     *                     @OA\Property(property="stationing_from", type="number"),
     *                     @OA\Property(property="stationing_to", type="number"),
     *                     @OA\Property(property="gps_n1", type="number"),
     *                     @OA\Property(property="gps_n2", type="number"),
     *                     @OA\Property(property="gps_e1", type="number"),
     *                     @OA\Property(property="gps_e2", type="number"),
     *                     @OA\Property(property="geometry", type="string", nullable=true)
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="objects",
     *                 type="array",
     *                 nullable=true,
     *                 @OA\Items(
     *                     type="object",
     *                     required={"type_id","id","name"},
     *                     @OA\Property(property="type_id", type="integer", description="ID of object type"),
     *                     @OA\Property(property="name", type="string")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="prices",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"type_id","value"},
     *                     @OA\Property(property="type_id", type="integer", description="ID of price type"),
     *                     @OA\Property(property="value", type="number")
     *                 )
     *             ),
     *             @OA\Property(property="fin_source", type="integer", description="ID of financial source"),
     *             @OA\Property(property="fin_source_pd", type="integer", description="ID of project documentation source", nullable=true),
     *             @OA\Property(
     *                 property="relations",
     *                 type="array",
     *                 nullable=true,
     *                 @OA\Items(
     *                     type="object",
     *                     required={"type_id","id"},
     *                     @OA\Property(property="type_id", type="integer", description="ID of relation type"),
     *                     @OA\Property(property="id", type="integer", description="ID of related project")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Project created",
     *         @OA\JsonContent(ref="#/components/schemas/ProjectResource")
     *     ),
     *     @OA\Response(response=400, description="Invalid input"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     *
     * @param Request $request
     * @return ProjectResource
     */
    public function store(Request $request)
    {
        $type = $request->query('type');

        // Define base validation rules for all project types
        $rules = [
            'name' => 'required|string|max:255',
            'subject' => 'required|string',
            'project_type' => 'required|integer|exists:rangeProjectTypes,idProjectType',
            'project_subtype' => 'required|integer|exists:rangeProjectSubtypes,idProjectSubtype',
            'editor' => 'required|string|exists:users,username',
            
            'areas' => 'required|array',
            'areas.*' => 'integer|exists:rangeAreas,idArea',
            
            'communications' => 'required|array',
            'communications.*.id' => 'required|integer|exists:rangeCommunications,idCommunication',
            'communications.*.stationing_from' => 'required|numeric',
            'communications.*.stationing_to' => 'required|numeric',
            'communications.*.gps_n1' => 'required|numeric',
            'communications.*.gps_n2' => 'required|numeric',
            'communications.*.gps_e1' => 'required|numeric',
            'communications.*.gps_e2' => 'required|numeric',
            'communications.*.geometry' => ['nullable', 'string', new WktLineString],
            
            'objects' => 'nullable|array',
            'objects.*.type_id' => 'required_with:objects|integer|exists:rangeObjectTypes,idObjectType',
            'objects.*.name' => 'required_with:objects|string',
            
            'prices' => 'required|array',
            'prices.*.type_id' => 'required_with:prices|integer|exists:rangePriceTypes,idPriceType',
            'prices.*.value' => 'required_with:prices|numeric',
            
            'fin_source' => 'required|integer|exists:rangeFinancialSources,idFinSource',
            'fin_source_pd' => 'required|integer|exists:rangeFinancialSources,idFinSource',
            
            'relations' => 'nullable|array',
            'relations.*.type_id' => 'required_with:relations|integer|exists:rangeRelationTypes,idRelationType',
            'relations.*.id' => 'required_with:relations|integer|exists:projects,idProject',
        ];

        // Set default phase ID
        $idPhase = 5;

        // Apply project type specific rules and set phase ID
        switch ($type) {
            case 'namet':
                $idPhase = 6;
                break;
                
            case 'stavba':
                break;
                
            case 'udrzba':
                $rules['fin_source_pd'] = 'nullable|integer|exists:rangeFinancialSources,idFinSource';
                break;
                
            default:
                return response()->json(['error' => 'Invalid project type'], 400);
        }

        $validatedData = $request->validate($rules);

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
            'idPhase' => $idPhase,
            'idLocalProject' => 0,
            'inConcept' => false,
        ]);

        $project->createVersion();

        $project->areas()->sync($validatedData['areas']);

        foreach ($validatedData['communications'] as $communication) {
            $project->communications()->attach($communication['id'], [
                'stationingFrom' => $communication['stationing_from'],
                'stationingTo' => $communication['stationing_to'],
                'gpsN1' => $communication['gps_n1'],
                'gpsN2' => $communication['gps_n2'],
                'gpsE1' => $communication['gps_e1'],
                'gpsE2' => $communication['gps_e2'],
                'geometryWgs' => LineString::fromWkt($communication['geometry']) ?? null,
            ]);
        }

        if (!empty($validatedData['relations'])) {
            foreach ($validatedData['relations'] as $relation) {
                $project->relatedProjects()->attach($relation['id'], [
                    'idRelationType' => $relation['type_id'],
                    'username' => Auth::user()->username,
                    'created' => now(),
                ]);
            }
        }

        foreach ($validatedData['prices'] as $price) {
            $project->prices()->create([
                'idPriceType' => $price['type_id'], // Foreign key for PriceType
                'value' => $price['value'],
            ]);
        }

        if (!empty($validatedData['objects'])) {
            foreach ($validatedData['objects'] as $object) {
                $project->objects()->create([
                    'idObjectType' => $object['type_id'],
                    'name' => $object['name'],
                ]);
            }
        }

        ActionLog::create([
            'idActionType' => 1,
            'idLocalProject' => $project->idLocalProject,
            'username' => Auth::user()->username,
            'created' => now(),
        ]);

        $project->load([
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
        ]);

        return new ProjectResource($project);
    }

    /**
     * Updates an existing project.
     *
     * @OA\Put(
     *     path="/api/v1/projects/{id}",
     *     summary="Update a project",
     *     description="Updates project fields and creates a new version upon modification.",
     *     operationId="updateProject",
     *     tags={"Projects"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="Project ID", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", maxLength=255),
     *             @OA\Property(property="idProjectType", type="integer"),
     *             @OA\Property(property="idProjectSubtype", type="integer"),
     *             @OA\Property(property="idFinSource", type="integer"),
     *             @OA\Property(property="idPhase", type="integer"),
     *             @OA\Property(property="inConcept", type="boolean"),
     *             @OA\Property(property="priorityAtts", type="string", format="json")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Project updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ProjectResource")
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Project not found")
     * )
     *
     * @param Request $request
     * @param int $id
     * @return ProjectResource
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'sometimes|string|max:255',
            'subject' => 'sometimes|string',
            'project_type' => 'sometimes|integer|exists:rangeProjectTypes,idProjectType',
            'project_subtype' => 'sometimes|integer|exists:rangeProjectSubtypes,idProjectSubtype',
            'editor' => 'sometimes|string|exists:users,username',
            
            'areas' => 'sometimes|array',
            'areas.*' => 'integer|exists:rangeAreas,idArea',
            
            'communications' => 'sometimes|array',
            'communications.*.id' => 'sometimes|integer|exists:rangeCommunications,idCommunication',
            'communications.*.stationing_from' => 'sometimes|numeric',
            'communications.*.stationing_to' => 'required|numeric',
            'communications.*.gps_n1' => 'sometimes|numeric',
            'communications.*.gps_n2' => 'sometimes|numeric',
            'communications.*.gps_e1' => 'sometimes|numeric',
            'communications.*.gps_e2' => 'sometimes|numeric',
            'communications.*.geometry' => ['sometimes', 'string', new WktLineString],
            
            'objects' => 'sometimes|array',
            'objects.*.type_id' => 'integer|exists:rangeObjectTypes,idObjectType',
            'objects.*.name' => 'string',
            
            'prices' => 'sometimes|array',
            'prices.*.type_id' => 'integer|exists:rangePriceTypes,idPriceType',
            'prices.*.value' => 'numeric',
            
            'fin_source' => 'required|integer|exists:rangeFinancialSources,idFinSource',
            'fin_source_pd' => 'required|integer|exists:rangeFinancialSources,idFinSource',
            
            'relations' => 'nullable|array',
            'relations.*.type_id' => 'required_with:relations|integer|exists:rangeRelationTypes,idRelationType',
            'relations.*.id' => 'required_with:relations|integer|exists:projects,idProject',
        ];

        $validatedData = $request->validate($rules);

        $project = Project::findOrFail($id);
        $project->createVersion();

        $project->areas()->sync($validatedData['areas']);

        foreach ($validatedData['communications'] as $communication) {
            $project->communications()->attach($communication['id'], [
                'stationingFrom' => $communication['stationing_from'],
                'stationingTo' => $communication['stationing_to'],
                'gpsN1' => $communication['gps_n1'],
                'gpsN2' => $communication['gps_n2'],
                'gpsE1' => $communication['gps_e1'],
                'gpsE2' => $communication['gps_e2'],
                'geometryWgs' => LineString::fromWkt($communication['geometry']) ?? null,
            ]);
        }

        if (!empty($validatedData['relations'])) {
            foreach ($validatedData['relations'] as $relation) {
                $project->relatedProjects()->attach($relation['id'], [
                    'idRelationType' => $relation['type_id'],
                    'username' => Auth::user()->username,
                    'created' => now(),
                ]);
            }
        }

        foreach ($validatedData['prices'] as $price) {
            $project->prices()->create([
                'idPriceType' => $price['type_id'], // Foreign key for PriceType
                'value' => $price['value'],
            ]);
        }

        if (!empty($validatedData['objects'])) {
            foreach ($validatedData['objects'] as $object) {
                $project->objects()->create([
                    'idObjectType' => $object['type_id'],
                    'name' => $object['name'],
                ]);
            }
        }

        ActionLog::create([
            'idActionType' => 1,
            'idLocalProject' => $project->idLocalProject,
            'username' => Auth::user()->username,
            'created' => now(),
        ]);

        $project->load([
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
        ]);

        return new ProjectResource($project);
    }

    /**
     * Applies project-level filters to a query.
     *
     * @param Request $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyFilters(Request $request, $query)
    {
        $filters = $request->input('filter', []);
        $isProjectCommunication = ($query->getModel() instanceof ProjectCommunication);

        $applyProjectFilters = function ($q) use ($filters) {
            $q->whereNull('deletedDate');
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

        if ($isProjectCommunication) {
            $query->whereHas('project', function ($q) use ($applyProjectFilters) {
                $applyProjectFilters($q);
            });
        } else {
            $applyProjectFilters($query);
        }

        return $query;
    }
}
