<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Passport\Token;
use Laravel\Passport\TransientToken;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiLogout extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | API Logout Controller
    |--------------------------------------------------------------------------
    |
    | This logs out the authenticated user from the api. We will also revoke
    | any tokens or devices tied to the user logging out
    |
    */

    /**
     * ApiLogout constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Handle the incoming logout request.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function __invoke(Request $request)
    {
        /* @var $token Token|TransientToken */

        $token = $request->user()->token();

        if( ! $token->transient() )
        {
            $token->revoke();

            return new JsonResponse('', 204);
        }

        throw new HttpException(403, 'Unauthorized to perform that action.');
    }
}
