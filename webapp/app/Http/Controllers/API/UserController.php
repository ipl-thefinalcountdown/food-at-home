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
use App\Http\Requests\PhotoRequest;
use App\Http\Requests\UserPhotoRequest;

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
        $validated = $request->validated();
        $user = new User();
        $user->fill($request->only('name', 'email', 'type'));
        $user->password = Hash::make($request->password);

        if ($request->has('photo'))
            $user->photo = savePhoto($request->photo);

        $user->save();

        return response()->json($user);
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

		if ($request->hasFile('photo'))
            $user->photo_url = deleteAndSavePhoto($request->photo, $user);

        $user->save();

		return response()->json($user);
    }

    public function photoProfile(PhotoRequest $request)
    {
        $request->validated();
        $user = $request->user();
        $user->photo_ur = $this->deleteAndSavePhoto($request->photo, $user);
        $user->save();
    }

    public function photo(UserPhotoRequest $request, User $user)
    {
        $request->validated();
        $user->photo_url = $this->deleteAndSavePhoto($request->photo, $user);
        $user->save();
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

    public function block(Request $request, User $user)
    {
        $this->validate($request, ['blocked' => 'required|boolean']);

        $user->blocked = $request->blocked;
        $user->save();

        return response()->json($user);
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
