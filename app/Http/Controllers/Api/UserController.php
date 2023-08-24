<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    use HttpResponses;    
    public function index(Request $request){
        $user = User::paginate($request->page ?? 10);
        return $this->success([
            'user' => $user,
        ],"User List Successfully...!");
    }
}
