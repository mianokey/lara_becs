<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Helpers\NotificationHelper;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display all users with their roles
     */
    public function index()
    {
        $users = User::with('roles')->whereNull('deleted_at')->latest()->get();
        return view('users.index', compact('users'));
    }


    /**
     * Show the form to create a new user
     */
    public function create()
    {
        $roles = Role::all(); // fetch all roles
        return view('users.create', compact('roles'));
    }

    /**
     * Store the newly created user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'roles' => 'required|array',
            'mobile' => [
                'required',
                'regex:/^\+\d{1,3}\d{9}$/', // must start with +countrycode + 9 digits
            ],
            'whatsapp' => [
                'nullable',
                'regex:/^\+\d{1,3}\d{9}$/', // optional but same format
            ],
        ], [
            'mobile.regex' => 'Mobile number must include a country code and 9 digits, e.g., +254712345678',
            'whatsapp.regex' => 'WhatsApp number must include a country code and 9 digits, e.g., +254712345678',
        ]);

        // Use mobile number as default password
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->mobile), // hash the mobile number
        ]);

        // Assign roles
        $user->syncRoles($request->roles);

        // Save extra details (mobile and whatsapp)
        $extraDetails = [
            'mobile' => $request->mobile,
            'whatsapp' => $request->whatsapp,
        ];

        foreach ($extraDetails as $key => $value) {
            if ($value) {
                $user->details()->updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        // Send WhatsApp notification
        NotificationHelper::sendWhatsApp(
            $request->whatsapp,
            'Hello, welcome to BECS Task & Project Management System! Your account has been created. Your default password is your mobile number.'
        );

        return redirect()->route('users.create')->with('success', 'User created successfully!');
    }

    public function edit($id)
    {
        $user = User::with(['roles', 'details'])->findOrFail($id);
        $roles = Role::all();

        // Extract details as a simple array for easier access in the view
        $userDetails = $user->details->pluck('value', 'key')->toArray();

        return view('users.edit', compact('user', 'roles', 'userDetails'));
    }


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'roles' => 'required|array',
            'mobile' => 'required|regex:/^\+\d{1,3}\d{9}$/',
            'whatsapp' => 'nullable|regex:/^\+\d{1,3}\d{9}$/',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        $user->syncRoles($request->roles);

        foreach (['mobile', 'whatsapp'] as $key) {
            $value = $request->$key;
            $user->details()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        // Optionally detach roles, but keep user record
        $user->roles()->detach();

        // Soft delete the user
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User archived successfully (not permanently deleted).');
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);

        // Generate a random secure password
        $newPassword = Str::random(10);

        // Update user password
        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        // Notify the user (database + broadcast)
        $notificationData = [
            'title' => 'Password Reset',
            'message' => "Your password has been reset. Your new password is: <strong>{$newPassword}</strong>",
            'icon' => 'fa-key',
            'url' => route('login'),
            'sender_id' => auth()->id(),
            'sender_name' => auth()->user()->name ?? 'System',
        ];

        $user->notify(new GeneralNotification($notificationData, $user->id));

        // Optionally send WhatsApp notification
        if ($user->phone) {
            NotificationHelper::sendWhatsApp($user->phone, "Hi {$user->name}, your password has been reset.\nNew password: {$newPassword}");
        }

        return redirect()->back()->with('success', 'User password has been reset and notification sent.');
    }
}
