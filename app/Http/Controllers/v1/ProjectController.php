<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/projects/",
     *     tags={"Projects"},
     *     summary="Get projects",
     *     description="Retrieves a paginated list of projects with their related data. Projects are in this list when they have not been deleted.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Projects retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="string", description="Project identifier"),
     *                 @OA\Property(property="name", type="string", description="Project name"),
     *                 @OA\Property(property="description", type="string", description="Project description"),
     *                 @OA\Property(property="projectType", type="object", description="Project type information"),
     *                 @OA\Property(property="projectSubtype", type="object", description="Project subtype information"),
     *                 @OA\Property(property="financialSource", type="object", description="Financial source information"),
     *                 @OA\Property(property="phase", type="object", description="Project phase information"),
     *                 @OA\Property(property="areas", type="array", @OA\Items(type="object"), description="Project areas"),
     *                 @OA\Property(property="communications", type="array", @OA\Items(type="object"), description="Project communications"),
     *                 @OA\Property(property="companies", type="array", @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", description="Company identifier"),
     *                     @OA\Property(property="name", type="string", description="Company name"),
     *                     @OA\Property(property="type", type="string", description="Company type name")
     *                 ), description="Related companies with their types"),
     *                 @OA\Property(property="contacts", type="array", @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", description="Contact identifier"),
     *                     @OA\Property(property="name", type="string", description="Contact name"),
     *                     @OA\Property(property="type", type="string", description="Contact type name")
     *                 ), description="Related contacts with their types"),
     *                 @OA\Property(property="editorUser", type="object", description="User who last edited the project"),
     *                 @OA\Property(property="authorUser", type="object", description="User who created the project")
     *             )),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="pagination", type="object",
     *                     @OA\Property(property="total", type="integer", description="Total number of records"),
     *                     @OA\Property(property="per_page", type="integer", description="Number of records per page"),
     *                     @OA\Property(property="current_page", type="integer", description="Current page number"),
     *                     @OA\Property(property="last_page", type="integer", description="Last page number")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated. Token is missing or invalid.",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     *
     * Display a paginated list of projects.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15); // Allow customization via query parameter
        $projects = \App\Models\Project\Project::whereNull('deletedDate')
            ->with([
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
            ])
            ->paginate($perPage);
            
        // Load company and contact relations with their types
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

        // Return paginated results as JSON
        return response()->json($projects);
    }
}
