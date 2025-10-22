<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Helpers\NotificationHelper;

// Send a message
NotificationHelper::sendWhatsApp('+254712345678', 'Hello, this is a test message!');


class UserController extends Controller
{
    public function create()
    {
        $roles = Role::all(); // fetch all roles
        return view('users.create', compact('roles'));
    }

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

// Send a message
NotificationHelper::sendWhatsApp($request->whatsapp, 'Hello, welcome to BECS Task & Project Management System! Your account has been created. Your default password is your mobile number.');

return redirect()->route('users.create')->with('success', 'User created successfully!');
}


}
