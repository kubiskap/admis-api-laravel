<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\v1\APIBaseController;
use App\Models\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends APIBaseController
{
    /**
     * @OA\Get(
     *     path="/api/v1/users",
     *     tags={"Users"},
     *     summary="Get all users",
     *     description="Retrieves a paginated list of all users with their related data",
     *     security={{"bearerAuth":{}}},
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
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of users retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="username", type="string", description="User's unique identifier"),
     *                 @OA\Property(property="name", type="string", description="User's full name"),
     *                 @OA\Property(property="email", type="string", format="email", description="User's email address"),
     *                 @OA\Property(property="idOu", type="string", description="User's organization unit identifier"),
     *                 @OA\Property(property="idRoleType", type="string", description="User's role type identifier"),
     *                 @OA\Property(property="idAuthorityType", type="string", description="User's authority type identifier"),
     *                 @OA\Property(property="accessDenied", type="boolean", description="Whether user access is denied"),
     *                 @OA\Property(property="idReportConfig", type="string", description="User's report configuration identifier"),
     *                 @OA\Property(property="editorReport", type="boolean", description="Whether user can edit reports"),
     *                 @OA\Property(property="ou", type="object", description="Organization unit information"),
     *                 @OA\Property(property="roleType", type="object", description="Role type information"),
     *                 @OA\Property(property="authorityType", type="object", description="Authority type information"),
     *                 @OA\Property(property="reportConfig", type="object", description="Report configuration information")
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
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $users = User::with(['ou', 'roleType', 'authorityType', 'reportConfig'])
            ->paginate($perPage);

        return response()->json($users);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users/{username}",
     *     tags={"Users"},
     *     summary="Get user by username",
     *     description="Retrieves detailed information about a specific user by their username",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="username",
     *         in="path",
     *         required=true,
     *         description="User's unique identifier",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="username", type="string", description="User's unique identifier"),
     *             @OA\Property(property="name", type="string", description="User's full name"),
     *             @OA\Property(property="email", type="string", format="email", description="User's email address"),
     *             @OA\Property(property="idOu", type="string", description="User's organization unit identifier"),
     *             @OA\Property(property="idRoleType", type="string", description="User's role type identifier"),
     *             @OA\Property(property="idAuthorityType", type="string", description="User's authority type identifier"),
     *             @OA\Property(property="accessDenied", type="boolean", description="Whether user access is denied"),
     *             @OA\Property(property="idReportConfig", type="string", description="User's report configuration identifier"),
     *             @OA\Property(property="editorReport", type="boolean", description="Whether user can edit reports"),
     *             @OA\Property(property="ou", type="object", description="Organization unit information"),
     *             @OA\Property(property="roleType", type="object", description="Role type information"),
     *             @OA\Property(property="authorityType", type="object", description="Authority type information"),
     *             @OA\Property(property="reportConfig", type="object", description="Report configuration information")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found")
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
     */
    public function show($username)
    {
        $user = User::with(['ou', 'roleType', 'authorityType', 'reportConfig'])
            ->findOrFail($username);

        return response()->json($user);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/users",
     *     tags={"Users"},
     *     summary="Create a new user",
     *     description="Creates a new user with the provided data",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "name", "email", "password", "idOu", "idRoleType", "idAuthorityType"},
     *             @OA\Property(property="username", type="string", description="User's unique identifier"),
     *             @OA\Property(property="name", type="string", description="User's full name"),
     *             @OA\Property(property="email", type="string", format="email", description="User's email address"),
     *             @OA\Property(property="password", type="string", format="password", description="User's password"),
     *             @OA\Property(property="idOu", type="string", description="User's organization unit identifier"),
     *             @OA\Property(property="idRoleType", type="string", description="User's role type identifier"),
     *             @OA\Property(property="idAuthorityType", type="string", description="User's authority type identifier"),
     *             @OA\Property(property="idReportConfig", type="string", nullable=true, description="User's report configuration identifier"),
     *             @OA\Property(property="editorReport", type="boolean", nullable=true, description="Whether user can edit reports"),
     *             @OA\Property(property="accessDenied", type="boolean", nullable=true, description="Whether user access is denied")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="username", type="string", description="User's unique identifier"),
     *             @OA\Property(property="name", type="string", description="User's full name"),
     *             @OA\Property(property="email", type="string", format="email", description="User's email address"),
     *             @OA\Property(property="idOu", type="string", description="User's organization unit identifier"),
     *             @OA\Property(property="idRoleType", type="string", description="User's role type identifier"),
     *             @OA\Property(property="idAuthorityType", type="string", description="User's authority type identifier"),
     *             @OA\Property(property="accessDenied", type="boolean", description="Whether user access is denied"),
     *             @OA\Property(property="idReportConfig", type="string", description="User's report configuration identifier"),
     *             @OA\Property(property="editorReport", type="boolean", description="Whether user can edit reports")
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
     *         response=401,
     *         description="Unauthenticated. Token is missing or invalid.",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Password::defaults()],
            'idOu' => ['required', 'exists:ou,idOu'],
            'idRoleType' => ['required', 'exists:rangeRoleTypes,idRoleType'],
            'idAuthorityType' => ['required', 'exists:rangeAuthorityTypes,idAuthorityType'],
            'idReportConfig' => ['nullable', 'exists:reportConfig,idReportConfig'],
            'editorReport' => ['nullable', 'boolean'],
            'accessDenied' => ['nullable', 'boolean'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        return response()->json($user, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/users/{username}",
     *     tags={"Users"},
     *     summary="Update a user",
     *     description="Updates an existing user with the provided data",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="username",
     *         in="path",
     *         required=true,
     *         description="User's unique identifier",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", description="User's full name"),
     *             @OA\Property(property="email", type="string", format="email", description="User's email address"),
     *             @OA\Property(property="password", type="string", format="password", description="User's password"),
     *             @OA\Property(property="idOu", type="string", description="User's organization unit identifier"),
     *             @OA\Property(property="idRoleType", type="string", description="User's role type identifier"),
     *             @OA\Property(property="idAuthorityType", type="string", description="User's authority type identifier"),
     *             @OA\Property(property="idReportConfig", type="string", nullable=true, description="User's report configuration identifier"),
     *             @OA\Property(property="editorReport", type="boolean", nullable=true, description="Whether user can edit reports"),
     *             @OA\Property(property="accessDenied", type="boolean", nullable=true, description="Whether user access is denied")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="username", type="string", description="User's unique identifier"),
     *             @OA\Property(property="name", type="string", description="User's full name"),
     *             @OA\Property(property="email", type="string", format="email", description="User's email address"),
     *             @OA\Property(property="idOu", type="string", description="User's organization unit identifier"),
     *             @OA\Property(property="idRoleType", type="string", description="User's role type identifier"),
     *             @OA\Property(property="idAuthorityType", type="string", description="User's authority type identifier"),
     *             @OA\Property(property="accessDenied", type="boolean", description="Whether user access is denied"),
     *             @OA\Property(property="idReportConfig", type="string", description="User's report configuration identifier"),
     *             @OA\Property(property="editorReport", type="boolean", description="Whether user can edit reports")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found")
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
     *         response=401,
     *         description="Unauthenticated. Token is missing or invalid.",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $username)
    {
        $user = User::findOrFail($username);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($username, 'username')],
            'password' => ['sometimes', Password::defaults()],
            'idOu' => ['sometimes', 'exists:ou,idOu'],
            'idRoleType' => ['sometimes', 'exists:rangeRoleTypes,idRoleType'],
            'idAuthorityType' => ['sometimes', 'exists:rangeAuthorityTypes,idAuthorityType'],
            'idReportConfig' => ['nullable', 'exists:reportConfig,idReportConfig'],
            'editorReport' => ['nullable', 'boolean'],
            'accessDenied' => ['nullable', 'boolean'],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/users/{username}",
     *     tags={"Users"},
     *     summary="Delete a user",
     *     description="Permanently deletes an existing user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="username",
     *         in="path",
     *         required=true,
     *         description="User's unique identifier",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="User deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found")
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
     */
    public function destroy($username)
    {
        $user = User::findOrFail($username);
        $user->delete();

        return response()->json(null, 204);
    }
} 