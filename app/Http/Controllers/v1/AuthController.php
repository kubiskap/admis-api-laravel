<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\v1\APIBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Users\User;

class AuthController extends APIBaseController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     tags={"Authentication"},
     *     summary="Login to get JWT token",
     *     description="Authenticates a user and returns a JWT token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username","password"},
     *             @OA\Property(property="username", type="string", format="username"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     )
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/me",
     *     tags={"Authentication"},
     *     summary="Retrieve authenticated user details",
     *     description="Fetches the details of the currently authenticated user based on the provided JWT token.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User information retrieved successfully.",
     *         @OA\JsonContent(
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
     *         response=401,
     *         description="Unauthenticated. Token is missing or invalid.",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     tags={"Authentication"},
     *     summary="Logout user and invalidate token",
     *     description="Logs out the authenticated user and invalidates their JWT token.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful. Token invalidated.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successfully logged out")
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
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/refresh",
     *     tags={"Authentication"},
     *     summary="Refresh JWT token",
     *     description="Generates a new JWT token using the current valid token. The old token will be invalidated.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully. Returns the new token.",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", description="New JWT access token"),
     *             @OA\Property(property="token_type", type="string", example="bearer", description="Token type"),
     *             @OA\Property(property="expires_in", type="integer", description="Token expiration time in seconds")
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
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}