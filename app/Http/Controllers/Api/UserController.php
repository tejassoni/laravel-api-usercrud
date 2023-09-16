<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserStoreApiRequest;
use App\Http\Requests\UserUpdateApiRequest;
use Illuminate\Support\Facades\Storage;

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

            $fileName = null;
            if ($request->hasFile('inputFile')) {
                $filehandle = $this->_singleFileUploads($request, 'inputFile', 'public/user');
                $fileName = $filehandle['data']['name'];
            }

            $user = User::create(['name' => $request->name, 'email' => $request->email, 'mobile' => $request->mobile, 'gender' => $request->gender, 'pincode' => $request->pincode, 'address' => $request->address, 'image' => $fileName, 'birthdate' => date('Y-m-d', strtotime($request->birthdate)), 'password' => Hash::make('password')]);
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
            if ($user = User::findOrFail($id)) {
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
            $user = User::findOrFail($id);
            if ($request->hasFile('inputFile')) {                
                if (Storage::exists('/public/user/'.$user->image)) {
                    Storage::delete('/public/user/'.$user->image);
                }
                $filehandle = $this->_singleFileUploads($request, 'inputFile', 'public/user');
                $fileName = $filehandle['data']['name'];
            } else {
                $fileName = $user->image;
            }

            $user->update(['name' => $request->name, 'email' => $request->email, 'mobile' => $request->mobile, 'pincode' => $request->pincode, 'address' => $request->address, 'gender' => $request->gender, 'image' => $fileName, 'birthdate' => date('Y-m-d', strtotime($request->birthdate))]);
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

    /**
     * _singleFileUploads : Complete Fileupload Handling
     * @param  Request $request
     * @param  $htmlformfilename : input type file name
     * @param  $uploadfiletopath : Public folder paths 'foldername/subfoldername'
     * @return File save with array return
     */
    private function _singleFileUploads($request = "", $htmlformfilename = "", $uploadfiletopath = "")
    {
        try {

            // check parameter empty Validation
            if (empty($request) || empty($htmlformfilename) || empty($uploadfiletopath)) {
                throw new \Exception("Required Parameters are missing", 400);
            }

            // check if folder exist at public directory if not exist then create folder 0777 permission
            if (!file_exists($uploadfiletopath)) {
                $oldmask = umask(0);
                mkdir($uploadfiletopath, 0777, true);
                umask($oldmask);
            }

            $fileNameOnly = preg_replace("/[^a-z0-9\_\-]/i", '', basename($request->file($htmlformfilename)->getClientOriginalName(), '.' . $request->file($htmlformfilename)->getClientOriginalExtension()));
            $fileFullName = $fileNameOnly . "_" . date('dmY') . "_" . time() . "." . $request->file($htmlformfilename)->getClientOriginalExtension();
            $path = $request->file($htmlformfilename)->storeAs($uploadfiletopath, $fileFullName);
            // $request->file($htmlformfilename)->move(public_path($uploadfiletopath), $fileFullName);
            $resp['status'] = true;
            $resp['data'] = array('name' => $fileFullName, 'url' => url('storage/' . str_replace('public/', '', $uploadfiletopath) . '/' . $fileFullName), 'path' => \storage_path('app/' . $path), 'extenstion' => $request->file($htmlformfilename)->getClientOriginalExtension(), 'type' => $request->file($htmlformfilename)->getMimeType(), 'size' => $request->file($htmlformfilename)->getSize());
            $resp['message'] = "File uploaded successfully..!";
        } catch (\Exception $ex) {
            $resp['status'] = false;
            $resp['data'] = [];
            $resp['message'] = 'File not uploaded...!';
            $resp['ex_message'] = $ex->getMessage();
            $resp['ex_code'] = $ex->getCode();
            $resp['ex_file'] = $ex->getFile();
            $resp['ex_line'] = $ex->getLine();
        }
        return $resp;
    }
}