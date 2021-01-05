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
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Requests\AuthDeleteRequest;
use App\Http\Requests\AuthPutRequest;

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
            $user->photo = savePhoto($request->photo_url);

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

    public function updateProfile(AuthPutRequest $request)
	{
        $request->validated();
        $user = $request->user();
        $user->fill($request->only('name', 'email'));

		if($request->has('password'))
			$user->password = Hash::make($request->password);

		if ($request->hasFile('photo'))
            $user->photo_url = $this->deleteAndSavePhoto($request->photo_url, $user);

        if ($request->user()->type == 'C')
        {
            $customer = Customer::find($user->id);
            $customer->fill($request->only('nif', 'phone', 'address'));
            $customer->save();
        }

        $user->save();
		return response()->json($user);
	}

	public function update(UserPutRequest $request, User $user)
	{
		$request->validated();
		$user->fill($request->only('name', 'email', 'type'));

		if($request->has('password'))
			$user->password = Hash::make($request->password);

		if ($request->hasFile('photo'))
            $user->photo_url = $this->deleteAndSavePhoto($request->photo_url, $user);

        $user->save();
		return response()->json($user);
    }

    public function photoProfile(PhotoRequest $request)
    {
        $request->validated();
        $user = $request->user();
        $user->photo_url = $this->deleteAndSavePhoto($request->photo_url, $user);
        $user->save();
    }

    public function photo(UserPhotoRequest $request, User $user)
    {
        $request->validated();
        $user->photo_url = $this->deleteAndSavePhoto($request->photo_url, $user);
        $user->save();
    }

    public function photoDelete(UserRequest $request, User $user)
    {
        $request->validated();
        $this->deletePhoto($user->photo_url, $user);
        $user->photo_url = null;
        $user->save();
    }

    public function photoDeleteProfile(Request $request)
    {
        $user = $request->user();
        $this->deletePhoto($user->photo_url, $user);
        $user->photo_url = null;
        $user->save();
    }

    public function delete(UserDeleteRequest $request, User $user)
    {
        $request->validated();
        $user->tokens()->delete();
        $user->delete();
    }

    public function deleteProfile(AuthDeleteRequest $request)
    {
        $request->validated();
        $request->user()->delete();
    }

    public function block(Request $request, User $user)
    {
        $this->validate($request, ['blocked' => 'required|boolean']);

        $user->blocked = $request->blocked;
        $user->save();

        return response()->json($user);
    }

    private function deletePhoto(User $user)
    {
        if(!is_null($user->photo_url))
        {
            $path = 'public/fotos/'.$user->photo_url;
            if(Storage::exists($path)) Storage::delete($path);
        }
    }

    private function savePhoto($photo)
    {
        $path = $photo->store('public/fotos');
        return basename($path);
    }

    private function deleteAndSavePhoto($photo, User $user)
    {
        $this->deletePhoto($photo, $user);
        return $this->savePhoto($photo);
    }
}
