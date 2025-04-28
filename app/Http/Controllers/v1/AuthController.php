<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\v1\APIBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Users\User;

/**
 * Class AuthController
 *
 * Handles authentication related endpoints. This controller uses the User model and related resources to
 * authenticate users, issue and refresh JWT tokens.
 *
 * @package App\Http\Controllers\v1
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoints for user authentication."
 * )
 */
class AuthController extends APIBaseController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Controller constructor can be used to apply middleware if required.
    }

    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     tags={"Authentication"},
     *     summary="Authenticate user & obtain JWT token",
     *     description="Authenticates a user using their credentials and returns a JWT token. See the User model for details on user fields.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "password"},
     *             @OA\Property(property="username", type="string", description="User's unique username"),
     *             @OA\Property(property="password", type="string", description="The user's password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successfully authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", description="JWT access token"),
     *             @OA\Property(property="token_type", type="string", example="bearer", description="Token type"),
     *             @OA\Property(property="expires_in", type="integer", description="Time in seconds until the token expires")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
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
     *     summary="Retrieve details of the authenticated user",
     *     description="Returns the details of the authenticated user. Data is based on the User model. Refer to App\Models\Users\User for field definitions.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Authenticated user retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="username", type="string", description="User's unique identifier"),
     *             @OA\Property(property="name", type="string", description="Full name of the user"),
     *             @OA\Property(property="email", type="string", format="email", description="Email address"),
     *             @OA\Property(property="idOu", type="integer", description="Organizational unit ID"),
     *             @OA\Property(property="idRoleType", type="integer", description="Role type identifier"),
     *             @OA\Property(property="idAuthorityType", type="integer", description="Authority type identifier"),
     *             @OA\Property(property="accessDenied", type="boolean", description="Access status"),
     *             @OA\Property(property="idReportConfig", type="integer", description="Report configuration identifier"),
     *             @OA\Property(property="editorReport", type="boolean", description="Flag for report editor rights")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated - token is missing or invalid",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     tags={"Authentication"},
     *     summary="Logout user and invalidate JWT token",
     *     description="Logs out the authenticated user and invalidates their JWT token.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successfully logged out")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated - token is missing or invalid",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
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
     *     description="Generates a new JWT token using the current valid token, invalidating the old one. See the User model for token claim structure.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="JWT token refreshed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", description="New JWT access token"),
     *             @OA\Property(property="token_type", type="string", example="bearer", description="Type of token"),
     *             @OA\Property(property="expires_in", type="integer", description="Token expiration time in seconds")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated - token is missing or invalid",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Formats the JWT token response.
     *
     * @param string $token The JWT token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60
        ]);
    }
}
