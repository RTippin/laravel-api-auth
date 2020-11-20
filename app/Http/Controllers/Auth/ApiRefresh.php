<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\HandlesApiAuth;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApiRefreshRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Token;
use Lcobucci\JWT\Parser;

class ApiRefresh extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | API Refresh Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles refreshing access tokens. If successful, we
    | fire the event to link the user device to the new token!
    |
    */

    use HandlesApiAuth;

    /**
     * Name of the key from request to use on throttle attempts
     *
     * @var string
     */
    protected $throttleKeyName = 'refresh_token';

    /**
     * @var Parser
     */
    protected $jwt;

    /**
     * ApiRefresh constructor.
     *
     * @param Parser $jwt
     */
    public function __construct(Parser $jwt)
    {
        $this->middleware('guest:api');

        $this->jwt = $jwt;
    }

    /**
     * Handle the refresh access token request
     *
     * @param ApiRefreshRequest $request
     * @return Response|\Symfony\Component\HttpFoundation\Response|void
     * @throws ValidationException
     * @noinspection PhpVoidFunctionResultUsedInspection
     */
    public function __invoke(ApiRefreshRequest $request)
    {
        if($this->hasTooManyAttempts($request))
        {
            return $this->sendLockoutResponse($request);
        }

        $this->addRefreshRequest($request);

        return $this->completeTokenRefresh(
            $request,
            $this->issueTokenFromServer($request)
        );
    }

    /**
     * Complete token flow, fire events, return access token
     *
     * @param Request $request
     * @param Response $token
     * @return Response
     */
    private function completeTokenRefresh(Request $request, Response $token)
    {
        $this->clearAttempts($request);

        $parsedTokenId = $this->parseJwtTokenId($this->jwt, $token);

        /** @var Token $accessToken */

        $accessToken = Token::with('user')->findOrFail($parsedTokenId);

        //fire events

        return $token;
    }

    /**
     * Add oauth params to request before creating server request instance
     *
     * @param Request $request
     */
    private function addRefreshRequest(Request $request)
    {
        $request->merge([
            'grant_type' => 'refresh_token',
            'client_id' => config('passport.password_grant_client.id'),
            'client_secret' => $request->input('client_secret'),
            'refresh_token' => $request->input('refresh_token'),
            'scope' => ''
        ]);
    }
}
