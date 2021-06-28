<?php

namespace App\Actions\API;

use App\Http\Requests\API\UserIndexRequest;
use App\Http\Resources\API\User\UserResource;
use App\Models\User;
use App\Rules\LimitPage;
use Illuminate\Support\Facades\Validator;

class IndexUserAction
{
    public function execute(UserIndexRequest $request)
    {
        $name = $request->input('name');
        $page = $request->input('page', 1);

        [$totalUsers, $itemsPerPage, $totalPage] = $this->getData($name, $page);

        $this->validatePage($page, $totalUsers);

        $users = $this->getUsers($name, $page, $itemsPerPage);

        $collection = (UserResource::collection($users))->additional([
            'current_page' => $page,
            'items_per_page' => $itemsPerPage,
            'total_items' => $totalUsers,
            'total_page' => $totalPage,
        ]);

        return $collection;
    }
    private function getData($name)
    {
        $totalUsers = User::whereName($name)->count();
        $itemsPerPage = config('api-pagination.items_per_page', 5);
        $totalPage = (int) ceil($totalUsers / $itemsPerPage);
        return [
            $totalUsers,
            $itemsPerPage,
            $totalPage,
        ];
    }

    private function getUsers($name, $currentPage, $itemsPerPage)
    {
        return User::whereName($name)->getPage($currentPage, $itemsPerPage)->get();
    }

    private function validatePage($currentPage, $totalUsers)
    {
        Validator::validate(['page' => $currentPage], [
            'page' => new LimitPage($totalUsers),
        ]);
    }
}
