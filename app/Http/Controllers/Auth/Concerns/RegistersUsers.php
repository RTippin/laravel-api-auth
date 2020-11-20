<?php

namespace App\Http\Controllers\Auth\Concerns;

use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

trait RegistersUsers
{
    /**
     * @param Request $request
     * @throws HttpException|Throwable
     */
    protected function makeUserAccount(Request $request)
    {
        DB::beginTransaction();

        try{
            //make user
            $user = User::create([]);

        }catch (Exception $e){
            DB::rollBack();

            report($e);

            throw new HttpException(500, 'Unable to create account, please try again.');
        }

        DB::commit();

        return $user;
    }
}
