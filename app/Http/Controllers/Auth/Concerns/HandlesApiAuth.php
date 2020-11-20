<?php

namespace App\Http\Controllers\Auth\Concerns;

use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Lcobucci\JWT\Parser;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait HandlesApiAuth
{
    use ThrottlesAttempts;

    /**
     * Request access token from oauth server
     *
     * @param Request $request
     * @return Response
     */
    protected function issueTokenFromServer(Request $request)
    {
        try
        {
            return $this->tokenServer()->issueToken($this->serverRequest());
        }
        catch (Exception $e)
        {
            $this->incrementAttempts($request);

            throw new HttpException(422, $e->getMessage());
        }
    }

    /**
     * Get the AccessTokenController instance
     *
     * @return Application|AccessTokenController|mixed
     */
    protected function tokenServer()
    {
        return app(AccessTokenController::class);
    }

    /**
     * Get the ServerRequestInterface instance
     *
     * @return Application|mixed|ServerRequestInterface
     */
    protected function serverRequest()
    {
        return app(ServerRequestInterface::class);
    }

    /**
     * Get oauth_access_token ID from the parsed access token itself
     *
     * @param Parser $jwt
     * @param Response $token
     * @return mixed
     */
    protected function parseJwtTokenId(Parser $jwt, Response $token)
    {
        return $jwt->parse(
            json_decode(
                $token->getContent()
            )->access_token
        )->getClaim('jti');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
