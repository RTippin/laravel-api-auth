<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\HandlesApiAuth;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApiLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Lcobucci\JWT\Parser;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiLogin extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | API Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application through
    | the API. We will return the access and refresh tokens, and fire the
    | event that handles linking user device to the access token
    |
    */

    use HandlesApiAuth;

    /**
     * @var Parser
     */
    protected $jwt;

    /**
     * ApiLogin constructor.
     *
     * @param Parser $jwt
     */
    public function __construct(Parser $jwt)
    {
        $this->middleware('guest:api');

        $this->jwt = $jwt;
    }

    /**
     * Handle a login request to the application.
     *
     * @param ApiLoginRequest $request
     * @return Response|\Symfony\Component\HttpFoundation\Response|void
     * @throws ValidationException|HttpException
     * @noinspection PhpVoidFunctionResultUsedInspection
     */
    public function __invoke(ApiLoginRequest $request)
    {
        if($this->hasTooManyAttempts($request))
        {
            return $this->sendLockoutResponse($request);
        }

        if($this->attemptLogin($request))
        {
            $this->addTokenRequestParameters($request);

            return $this->completeTokenRequest(
                $request,
                $this->issueTokenFromServer($request)
            );
        }

        $this->incrementAttempts($request);

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    /**
     * Complete token flow, fire events, return access token
     *
     * @param Request $request
     * @param Response $token
     * @return Response
     */
    private function completeTokenRequest(Request $request, Response $token)
    {
        $this->clearAttempts($request);

        //fire events

        return $token;
    }

    /**
     * Add oauth params to request before creating server request instance
     *
     * @param Request $request
     */
    private function addTokenRequestParameters(Request $request)
    {
        $request->merge([
            'grant_type' => 'password',
            'client_id' => config('passport.password_grant_client.id'),
            'client_secret' => $request->input('client_secret'),
            'username' => $request->input('email'),
            'password' => $request->input('password'),
            'scope' => ''
        ]);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param Request $request
     * @return array
     */
    private function credentials(Request $request)
    {
        return array_merge($request->only('email', 'password'), ['active' => 1]);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param Request $request
     * @return bool
     */
    private function attemptLogin(Request $request)
    {
        return $this->guard()->once($this->credentials($request));
    }

}
