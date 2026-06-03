<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;
    
    public function showRegistrationForm()
    {
        $divisions = \App\Models\Division::where('name', '!=', 'Direktur')->get();
        $branches = \App\Models\Branch::all();
        $positions = \App\Models\Position::whereNotIn('name', ['Direktur Utama', 'Kepala Cabang'])->get();
        return view('auth.register', compact('divisions', 'branches', 'positions'));
    }

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'branch_id' => ['required', 'exists:branches,id'],
            'division_id' => ['required', 'exists:divisions,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'branch_id' => $data['branch_id'],
            'division_id' => $data['division_id'],
            'position_id' => $data['position_id'] ?? null,
            'role' => 'karyawan',
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(\Illuminate\Http\Request $request)
    {
        $this->validator($request->all())->validate();

        event(new \Illuminate\Auth\Events\Registered($user = $this->create($request->all())));

        // Do NOT log the user in automatically.
        // Redirect them to the login page with a premium success notification.

        return $request->wantsJson()
            ? new \Illuminate\Http\JsonResponse([], 201)
            : redirect()->route('login')->with('success', 'Akun berhasil dibuat! Silakan masuk menggunakan akun baru Anda.');
    }
}
