<?php

namespace Tests\Unit\Resources\API\User;

use App\Http\Resources\API\User\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_resource_returns_correct()
    {
        $user = User::factory()->make();

        $expect = [
            "id" => $user->id,
            "email" => $user->email,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "phone" => $user->phone,
            "avatar" => $user->avatar,
        ];

        $resource = new UserResource($user);
        $resourceData = $resource->response()->getData(true)['data'];

        $this->assertEquals($expect, $resourceData);
    }

    public function test_user_resource_can_collection_with_additional_data()
    {
        User::factory(35)->create();

        $currentPage = 2;
        $totalUsers = User::count();
        $itemsPerPage = 7;
        $totalPage = (int) ceil($totalUsers / $itemsPerPage);

        $usersToSkip = $itemsPerPage * ($currentPage - 1);
        $users = User::skip($usersToSkip)->take($itemsPerPage)->get();

        $resources = UserResource::collection($users);

        $expect = [
            'current_page' => $currentPage,
            'items_per_page' => $itemsPerPage,
            'total_items' => $totalUsers,
            'total_page' => $totalPage,
            'data' => $resources->response()->getData(true)['data'],
        ];

        $collection = (UserResource::collection($users))->additional([
            'current_page' => $currentPage,
            'items_per_page' => $itemsPerPage,
            'total_items' => $totalUsers,
            'total_page' => $totalPage,
        ]);

        $collectionData = $collection->response()->getData(true);

        $this->assertEquals($expect, $collectionData);
    }
}
