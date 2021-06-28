<?php

namespace Tests\Feature\Controllers\API;

use App\Http\Resources\API\User\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAPIIndexController extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_should_return_list_of_users()
    {
        User::factory(50)->create();

        $currentPage = 1;
        $totalUsers = User::count();
        $itemsPerPage = config('api-pagination.items_per_page', 5);
        $totalPage = (int) ceil($totalUsers / $itemsPerPage);

        $usersToSkip = $itemsPerPage * ($currentPage - 1);
        $users = User::skip($usersToSkip)->take($itemsPerPage)->get();

        $collection = (UserResource::collection($users))->additional([
            'current_page' => $currentPage,
            'items_per_page' => $itemsPerPage,
            'total_items' => $totalUsers,
            'total_page' => $totalPage,
        ]);

        $collectionData = $collection->response()->getData(true);

        $response = $this->json('GET', '/api/users');

        $response->assertStatus(200)
            ->assertExactJson($collectionData);
    }

    public function test_should_return_422_error_when_passing_letter_in_page_param()
    {
        $response = $this->json('GET', '/api/users', [
            'page' => 'a',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['page']);
    }

    public function test_should_return_422_error_when_passing_negative_number_in_page_param()
    {
        $response = $this->json('GET', '/api/users', [
            'page' => '-3',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['page']);
    }

    public function test_should_return_422_error_when_passing_not_numberic_in_page_param()
    {
        $response = $this->json('GET', '/api/users', [
            'page' => '@',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['page']);
    }

    public function test_should_return_422_error_when_passing_invalid_page_param()
    {
        $response = $this->json('GET', '/api/users', [
            'page' => '9999999999',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['page']);
    }

    public function test_should_return_422_error_when_passing_letter_in_show_param()
    {
        $response = $this->json('GET', '/api/users', [
            'show' => 'a',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['show']);
    }

    public function test_should_return_422_error_when_passing_negative_number_in_show_param()
    {
        $response = $this->json('GET', '/api/users', [
            'show' => '-3',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['show']);
    }

    public function test_should_return_422_error_when_passing_not_numberic_in_show_param()
    {
        $response = $this->json('GET', '/api/users', [
            'show' => '@',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['show']);
    }

    public function test_should_return_422_error_when_passing_invalid_show_param()
    {
        $response = $this->json('GET', '/api/users', [
            'show' => '9999999999',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['show']);
    }

    public function test_should_return_422_error_when_passing_invalid_name_param()
    {
        $response = $this->json('GET', '/api/users', [
            'name' => str_repeat('a', 101),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_should_return_422_error_when_passing_not_string_name_param()
    {
        $response = $this->json('GET', '/api/users', [
            'name' => 1,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }
}
