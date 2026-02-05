<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        // Validate inputs
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users,username|max:50',
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'middle_name'       => 'nullable|string|max:100',

            'street'            => 'required|string|max:255',
            'barangay'          => 'required|string|max:255',
            'municipality_city' => 'required|string|max:255',
            'province'          => 'required|string|max:255',

            'email' => 'required|email|unique:users,email',
            'contact' => 'required|string|max:20',

            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('registering', true);
        }

        // Create user
        User::create([
            'username' => $request->username,
                        // 🔹 SAVE SPLIT NAME FIELDS
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,



            // 🔹 SAVE SPLIT ADDRESS FIELDS
            'street' => $request->street,
            'barangay' => $request->barangay,
            'municipality_city' => $request->municipality_city,
            'province' => $request->province,

            'email' => $request->email,
            'contact' => $request->contact,

            'password' => $request->password,
            'role' => User::ROLE_USER,
        ]);

        return redirect()->route('home')->with('register_success', 'Registration successful!');
    }

        public function checkUsername(Request $request)
        {
            $exists = User::where('username', $request->value)->exists();
            return response()->json(['exists' => $exists]);
        }


        public function checkEmail(Request $request)
        {
            $exists = User::where('email', $request->value)->exists();
            return response()->json(['exists' => $exists]);
        }


}
