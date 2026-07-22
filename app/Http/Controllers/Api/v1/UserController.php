<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('manage-users');

        $filters = $request->only(['search', 'role', 'status', 'trashed']);
        $perPage = $request->integer('per_page', 15);

        $users = $this->userService->getPaginatedUsers($filters, $perPage);

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('manage-users');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'status' => 'required|string|in:active,inactive,suspended',
            'roles' => 'required|array|min:1',
        ]);

        $user = $this->userService->createUser($validated);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        Gate::authorize('manage-users');

        $user = $this->userService->findUser($id);

        if (! $user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Gate::authorize('manage-users');

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,'.$id,
            'password' => 'sometimes|nullable|string|min:8',
            'status' => 'sometimes|required|string|in:active,inactive,suspended',
            'roles' => 'sometimes|required|array|min:1',
        ]);

        $user = $this->userService->updateUser($id, $validated);

        if (! $user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Gate::authorize('manage-users');

        $user = $this->userService->findUser($id);

        if (! $user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $this->userService->deleteUser($id);

        return response()->json(['message' => 'User deleted successfully.']);
    }
}
