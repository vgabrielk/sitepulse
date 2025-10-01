<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client) {
            return redirect()->route('login')->with('error', 'Client not found.');
        }

        return view('dashboard.profile.index', compact('user', 'client'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client) {
            return redirect()->route('login')->with('error', 'Client not found.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'nullable|string',
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        try {
            // Update user information
            $user->name = $request->name;
            $user->email = $request->email;
            
            // Update password if provided
            if ($request->filled('password')) {
                if (!$request->filled('current_password') || !Hash::check($request->current_password, $user->password)) {
                    return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
                }
                
                $user->password = Hash::make($request->password);
            }
            
            $user->save();
            
            // Update client information
            $client->name = $request->name;
            $client->email = $request->email;
            $client->save();
            
            return redirect()->route('profile')->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }
}
