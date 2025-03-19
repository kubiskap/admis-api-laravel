<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a paginated list of projects.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $query = \App\Models\Project\Project::whereNull('deletedDate');

        // Handle single project by ID from URL parameter or query parameter
        if ($request->route('id')) {
            $query->where('idProject', $request->route('id'));
        }

        // Handle ordering
        $orderBy = $request->query('order_by', 'idProject');
        $orderDirection = $request->query('order_direction', 'asc');
        
        // Validate order direction
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'asc';
        }

        // Validate and apply ordering
        switch ($orderBy) {
            case 'priority':
                $query->orderBy('priority', $orderDirection);
                break;
            case 'phase':
                $query->orderBy('idPhase', $orderDirection);
                break;
            case 'idProject':
            default:
                $query->orderBy('idProject', $orderDirection);
        }

        // Eager load relationships
        $query->with([
            'projectType',
            'projectSubtype',
            'financialSource',
            'phase',
            'areas',
            'communications',
            'companies',
            'contacts',
            'editorUser',
            'authorUser'
        ]);

        // Get paginated results
        $projects = $query->paginate($perPage);
            
        // Transform company and contact relations with their types
        $projects->each(function ($project) {
            // Load with eager loading to begin with
            $project->load(['companies', 'contacts']);
            
            // Transform companies to include type directly
            $project->companies->each(function ($company) {
                $company->pivot->load('companyType');
                // Add company type as direct property
                $company->type = $company->pivot->companyType ? $company->pivot->companyType->name : null;
            });
            
            // Transform contacts to include type directly
            $project->contacts->each(function ($contact) {
                $contact->pivot->load('contactType');
                // Add contact type as direct property
                $contact->type = $contact->pivot->contactType ? $contact->pivot->contactType->name : null;
            });
        });

        return response()->json($projects);
    }

    /**
     * Search projects with filters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
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
        $orderBy = $request->query('order_by', 'idProject');
        $orderDirection = $request->query('order_direction', 'asc');
        
        // Validate order direction
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'asc';
        }

        // Validate and apply ordering
        switch ($orderBy) {
            case 'priority':
                $query->orderBy('priority', $orderDirection);
                break;
            case 'phase':
                $query->orderBy('idPhase', $orderDirection);
                break;
            case 'idProject':
            default:
                $query->orderBy('idProject', $orderDirection);
        }

        // Eager load only the required relationships with specific attributes
        $query->with([
            'projectType:idProjectType,name',
            'phase:idPhase,name,phaseColor,phaseColorClass',
            'editorUser:username,name',
            'financialSource:idFinSource,name',
            'areas:idArea,name',
            'communications:idCommunication,name'
        ]);

        // Get paginated results
        $projects = $query->paginate($perPage);

        return response()->json($projects);
    }
}
