<?php

namespace App\Http\Controllers\API;

use App\Actions\API\IndexUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\UserIndexRequest;

class UserAPIController extends Controller
{
    public function index(UserIndexRequest $request, IndexUserAction $indexUserAction)
    {
        return $indexUserAction->execute($request);
    }
}
