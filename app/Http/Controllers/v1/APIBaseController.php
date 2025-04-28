<?php

namespace App\Http\Controllers\v1;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * APIBaseController
 *
 * This controller serves as the base controller for all API endpoints.
 * It provides a common foundation and Swagger documentation references to
 * various models and resources used throughout the application.
 *
 * @OA\Info(
 *     title="ADMIS API v1",
 *     version="1.0",
 *     description="API documentation for ADMIS"
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="Authentication endpoints"
 * )
 * @OA\Tag(
 *     name="Users",
 *     description="User management endpoints."
 * )
 * @OA\Tag(
 *     name="Projects",
 *     description="Project management endpoints"
 * )
 * @OA\Tag(
 *     name="Resources",
 *     description="Enum and View resource endpoints"
 * )
 * @OA\Tag(
 *    name="External API",
 *    description="External API endpoints"
 * )
 */
class APIBaseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}