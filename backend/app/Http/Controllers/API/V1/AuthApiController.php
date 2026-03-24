<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\InvalidOperationException;
use App\Http\Controllers\BaseController;
use App\Http\Requests\AuthRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class AuthApiController extends BaseController
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(AuthRequest $request)
    {
        $this->logRequest($request->validated(), 'login request');

        try {
            $token = $this->authService->login($request->validated());

            return response()->json([
                'success' => true,
                'token' => $token,
            ], 200);

        } catch (InvalidOperationException $e) {
            return $this->errorResponse($e->getMessage(), 400);
            
        } catch (\Exception $e) {

            $this->logException($e, 'login failed');

            return $this->errorResponse();
        }
    }

    public function logout(Request $request)
    {
        /** @var PersonalAccessToken|null $token */
        $token = $request->user()->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}
