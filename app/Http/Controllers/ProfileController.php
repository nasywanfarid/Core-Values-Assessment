<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\Branch;
use App\Models\Division;
use App\Models\Position;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data = [
            'user' => auth()->user()
        ];

        if (auth()->user()->role === 'admin') {
            $data['branches'] = Branch::all();
            $data['divisions'] = Division::all();
            $data['positions'] = Position::all();
        }

        return view('profile', $data);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'confirmed', Password::defaults()],
        ];

        if ($user->role === 'admin') {
            $rules['branch_id'] = ['required', 'exists:branches,id'];
            $rules['division_id'] = ['required', 'exists:divisions,id'];
            $rules['position_id'] = ['nullable', 'exists:positions,id'];
        }

        $validated = $request->validate($rules);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($user->role === 'admin') {
            $user->branch_id = $validated['branch_id'];
            $user->division_id = $validated['division_id'];
            $user->position_id = $validated['position_id'] ?? null;
        }

        if ($request->filled('new_password')) {
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return back()->with('status', 'Profile updated successfully!');
    }
}
