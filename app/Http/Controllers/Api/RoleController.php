<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RoleStoreRequest;
use App\Http\Requests\Api\RoleUpdateRequest;
use App\Http\Resources\Api\RoleCollection;
use App\Http\Resources\Api\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\Api\RoleCollection
     */
    public function index(Request $request)
    {
        $roles = Role::all();

        return new RoleCollection($roles);
    }

    /**
     * @param \App\Http\Requests\Api\RoleStoreRequest $request
     * @return \App\Http\Resources\Api\RoleResource
     */
    public function store(RoleStoreRequest $request)
    {
        $role = Role::create($request->validated());

        return new RoleResource($role);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Role $role
     * @return \App\Http\Resources\Api\RoleResource
     */
    public function show(Request $request, Role $role)
    {
        return new RoleResource($role);
    }

    /**
     * @param \App\Http\Requests\Api\RoleUpdateRequest $request
     * @param \App\Models\Role $role
     * @return \App\Http\Resources\Api\RoleResource
     */
    public function update(RoleUpdateRequest $request, Role $role)
    {
        $role->update($request->validated());

        return new RoleResource($role);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Role $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Role $role)
    {
        $role->delete();

        return response()->noContent();
    }
}
