<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UserPostRequest;
use App\Http\Requests\UserPutRequest;

class UserController extends Controller
{
	public function profile(Request $request)
	{
		return $this->view($request->user());
	}

	public function list(Request $request)
	{
		$query = User::where('deleted_at', null);

        // filter by type
        if($request->has('type'))
            $query = $query->where('type', $request->input('type'));

        // filter by name
        if($request->has('name'))
            $query = $query->where('name', 'like', '%' . $request->input('name') . '%');

        return UserResource::collection($request->has('page')
                ? $query->paginate()
                : $query->get()
        );
    }

    public function create(UserPostRequest $request)
    {
        $request->validate();
        $user = new User();
        $user->fill($request->all())->save();
    }

	public function view(User $user)
	{
		if($user->type == 'C')
		{
			return User::where('users.id', $user->id)
				->join('customers', 'users.id', 'customers.id')
				->first();
		}
		else
		{
			return $user;
		}
    }

    public function updateProfile(Request $request)
	{
		return $this->update($request, $request->user());
	}

	public function update(UserPutRequest $request, User $user)
	{
		$request->validated();

		$user->fill($request->only('name', 'email', 'type'));

		if($request->has('password'))
			$user->password = Hash::make($request->password);

		if ($request->hasFile('photo')) {
            if(!is_null($user->photo_url))
            {
                $path = 'public/fotos/'.$user->photo_url;
                if(file_exists(storage_path('app/'.$path))) Storage::delete($path);
            }

            $path = $request->photo->store('public/fotos');
            $user->photo_url = basename($path);
        }

        $user->save();

		return response()->json($user);
    }

    public function photoProfile(Request $request)
    {
        return $this->photo($request, $request->user());
    }

    public function photo(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|max:8192'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => 'Bad request. Invalid data.',
                'errors' => $validator->errors()
            ], 400);
        }

        if(!is_null($user->photo_url))
        {
            $path = 'public/fotos/'.$user->photo_url;
            if(Storage::exists($path))
                Storage::delete($path);
        }

        $path = $request->photo->store('public/fotos');
        $user->photo_url = basename($path);
        $user->save();

		return response()->json([
            'status_code' => 200,
            'message' => 'Upload successful'
        ]);
    }

    public function photoDelete(User $user)
    {
        if(!is_null($user->photo_url))
        {
            $path = 'public/fotos/'.$user->photo_url;
            if(Storage::exists($path))
                Storage::delete($path);
        }

        $user->photo_url = null;
        $user->save();

		return response()->json([
            'status_code' => 200,
            'message' => 'Photo deleted!'
        ]);
    }

    public function photoDeleteProfile(Request $request)
    {
        $this->photoDelete($request->user());
    }

    public function delete(Request $request, User $user)
    {
        if($request->user()->type == 'EM' && $request->user()->id == $user->id)
        {
            return response()->json([
                'status_code' => 400,
                'message' => 'Cant delete yourself!',
            ], 400);
        }

        $user->delete();
        return response()->json([
            'status_code' => 200,
            'message' => 'User deleted!'
        ]);
    }

    public static function validateOnUpdate(Request $request, User $user)
	{
        $validator_assocArr = [
            'name' => 'required|regex:/^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/',
            'email' => ['required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignoreModel($user)
            ],
            'photo' => 'nullable|image|max:8192'
        ];

        if($user->type == 'C')
        {
            $validator_assocArr += [
                'address' => 'required',
                'phone' => 'required',
                'nif' => 'nullable|numeric|digits:9'
            ];
        }

		return Validator::make($request->all(), $validator_assocArr);
    }

    private function savePhoto($photo)
    {
        $path = $photo->store('public/fotos');
        return basename($path);
    }

    private function deleteAndSavePhoto($photo, User $user)
    {
        if(!is_null($user->photo_url))
        {
            $path = 'public/fotos/'.$user->photo_url;
            if(Storage::exists($path)) Storage::delete($path);
        }

        return savePhoto($photo);
    }
}
