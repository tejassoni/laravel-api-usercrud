<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserStoreApiRequest;
use App\Http\Requests\UserUpdateApiRequest;

class UserController extends Controller
{
    use HttpResponses;

    /*
     * User Listing api with Pagination
     */
    public function index(Request $request)
    {
        try {
            return $this->success([
                'user' => User::latest()->paginate($request->input('per_page', 5)),
            ], "User List Successfully...!");
        } catch (\Exception $ex) {
            return $this->error([
            ], $ex->getMessage());
        }
    }

    /*
     * User Store api
     */
    public function storeUser(UserStoreApiRequest $request)
    {
        try {
            $user = User::create(['name' => $request->name, 'email' => $request->email, 'contact' => $request->contact, 'gender' => $request->gender, 'birthdate' => date('Y-m-d', strtotime($request->birthdate)), 'password' => Hash::make('password')]);
            return $this->success([
                'user' => $user,
            ], "User Created Successfully...!");
        } catch (\Exception $ex) {
            return $this->error([
            ], $ex->getMessage());
        }
    }

    /*
     * User detail view by ID api
     */
    public function showUser(Request $request, $id)
    {
        try {
            if ($user = User::find($id)) {
                return $this->success([
                    'user' => $user,
                ], "User Details Get Successfully...!");
            } else {
                return $this->error([
                ], "User Details not found...!");
            }
        } catch (\Exception $ex) {
            return $this->error([
            ], $ex->getMessage());
        }
    }

    /*
     * User detail update by ID api
     */
    public function updateUser(UserUpdateApiRequest $request, $id)
    {
        try {
            $user = User::find($id);
            $user->update(['name' => $request->name, 'email' => $request->email, 'contact' => $request->contact, 'gender' => $request->gender, 'birthdate' => date('Y-m-d', strtotime($request->birthdate))]);
            return $this->success([
                'user' => $user,
            ], "User Updated Successfully...!");

        } catch (\Exception $ex) {
            return $this->error([
            ], $ex->getMessage());
        }
    }

    /*
     * User delete by ID api
     */
    public function deleteUser(Request $request, $id)
    {
        try {
            $user = User::where('id', $id)->firstorfail()->delete();
            if ($user) {
                return $this->success([
                ], "User Deleted Successfully...!");
            } else {
                return $this->error([
                ], "User not deleted...!");
            }
        } catch (\Exception $ex) {
            return $this->error([
            ], $ex->getMessage());
        }
    }

    /*
     * User detail filter by params api
     */
    public function filterUser(Request $request, User $user)
    {
        try {
            $user = $user->newQuery();
            // Search for a user based on their name.
            if ($request->has('name')) {
                $user->where('name', $request->input('name'));
            }

            // Search for a user based on their company.
            if ($request->has('gender')) {
                $user->where('gender', $request->input('gender'));
            }

            $user = $user->paginate($request->input('per_page', 5));
            if ($user->isNotEmpty()) {
                return $this->success([
                    'user' => $user,
                ], "User Filter Records Get Successfully...!");
            } else {
                return $this->error([
                ], "User Filter Records not found...!");
            }
        } catch (\Exception $ex) {
            return $this->error([
            ], $ex->getMessage());
        }
    }
}