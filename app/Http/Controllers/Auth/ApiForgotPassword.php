<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\SendsPasswordResetEmails;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ApiForgotPassword extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | API Forgot Password Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails over API
    |
    */

    use SendsPasswordResetEmails;

    /**
     * ApiForgotPassword constructor.
     */
    public function __construct()
    {
        $this->middleware([
            'guest:api',
            'throttle:6,1'
        ]);
    }

    /**
     * Send a password reset link to the given user.
     *
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function __invoke(ForgotPasswordRequest $request)
    {
        return $this->passwordBrokerResponse(
            $this->broker()->sendResetLink(
                $this->credentials($request)
            )
        );
    }
}
