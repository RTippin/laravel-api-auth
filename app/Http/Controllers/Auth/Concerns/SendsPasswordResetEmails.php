<?php

namespace App\Http\Controllers\Auth\Concerns;

use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Password;
use Illuminate\Validation\ValidationException;

trait SendsPasswordResetEmails
{
    /**
     * Get the needed authentication credentials from the request.
     *
     * @param Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only('email');
    }

    /**
     * Get the response for a password reset link.
     *
     * @param string $response
     * @return JsonResponse|void
     * @throws ValidationException
     * @noinspection PhpVoidFunctionResultUsedInspection
     */
    protected function passwordBrokerResponse($response)
    {
        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($response)
            : $this->sendResetLinkFailedResponse($response);
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  string  $response
     * @return JsonResponse
     */
    protected function sendResetLinkResponse($response)
    {
        return new JsonResponse([
            'message' => trans($response)
        ]);
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param string $response
     * @return void
     * @throws ValidationException
     */
    protected function sendResetLinkFailedResponse($response)
    {
        throw ValidationException::withMessages([
            'email' => [trans($response)],
        ]);
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return PasswordBroker
     */
    public function broker()
    {
        return Password::broker();
    }
}
