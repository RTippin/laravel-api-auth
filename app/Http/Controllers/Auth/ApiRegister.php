<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\RegistersUsers;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\JsonResponse;
use Throwable;

class ApiRegister extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | API Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users through the API.
    | Upon success, we will fire the verify email notification.
    |
    */

    use RegistersUsers;

    /**
     * ApiRegister constructor.
     */
    public function __construct()
    {
        $this->middleware([
            'guest:api',
            'register.enabled'
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function __invoke(RegisterRequest $request)
    {
        $this->makeUserAccount($request)
            ->sendEmailVerificationNotification();

        return new JsonResponse([
            'message' => 'Registration complete. Please login with your new credentials'
        ]);
    }
}
