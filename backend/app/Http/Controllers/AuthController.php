<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\AccountInformations;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'birthday' => 'required|date',
            'gender' => 'required|string|max:10',
            'age' => 'required|integer',
            'account_type' => 'required|integer',
            'address' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Get the currently authenticated user
        $currentUser = Auth::user();

        // Check if the current user is authorized to create the account
        if ($currentUser && !$this->canCreateAccount($currentUser->account_type, $request->account_type)) {
            return response()->json(['message' => 'Unauthorized to create this type of account.'], 403);
        }

        // Create a new user
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'birthday' => $request->birthday,
            'gender' => $request->gender,
            'age' => $request->age,
            'account_type' => $request->account_type,
            'address' => $request->address,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create account information
        AccountInformations::create([
            'user_id' => $user->id,
            'requirements' => 0, // Default value
            'account_status' => 1, // Default value for "accepted"
            'account_acceptor_id' => $currentUser ? $currentUser->id : null, // Dynamic or null
            'deleted' => 0, // Default value for "not deleted"
            'company' => '', // Default value, adjust as needed
            'skills_assessment' => 1, // Default value
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Register a new applicant with account type 6.
     */
    public function registerApplicant(Request $request)
    {
        return $this->registerWithType($request, 6);
    }

    /**
     * Register a new employer with account type 5.
     */
    public function registerEmployer(Request $request)
    {
        return $this->registerWithType($request, 5);
    }

    /**
     * Register a new user with a specific account type.
     */
    private function registerWithType(Request $request, $accountType)
    {
        $request->merge(['account_type' => $accountType]);
        return $this->register($request);
    }

    /**
     * Check if the current user is allowed to create a specific account type.
     */
    private function canCreateAccount($currentAccountType, $newAccountType)
    {
        // Define the hierarchy
        $hierarchy = [
            1 => [1, 2, 3, 4, 5, 6], // Developer can create any account type
            2 => [3, 4, 5, 6],       // Head Admin can create Admin Staff and below
            3 => [4, 5, 6],          // Admin Staff can create Lending Account, Employer, Applicant
            4 => [],                 // Lending Account cannot create accounts
            5 => [],                 // Employer cannot create accounts
            6 => [],                 // Applicant cannot create accounts
        ];

        return in_array($newAccountType, $hierarchy[$currentAccountType] ?? []);
    }

    /**
     * Log in an applicant or employer (account types 5 or 6).
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
    
        // Join the User table with AccountInformations table
        $user = User::where('email', $request->email)
                    ->whereIn('account_type', [6,5]) // Applicant and Employer account types
                    ->whereHas('accountInformation', function ($query) {
                        $query->where('account_status', 2)
                              ->whereNotNull('account_acceptor_id'); //uncomment this if need accept
                    })
                    ->first();
    
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid login details or account not authorized.'], 401);
        }
    
        $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }
    
    /**
     * Log in an admin (account types 1, 2, 3, 4).
     */
    public function loginAdmin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
    
        // Join the User table with AccountInformations table
        $user = User::where('email', $request->email)
                    ->whereIn('account_type', [1, 2, 3, 4]) // Admin account types
                    ->whereHas('accountInformation', function ($query) {
                        $query->where('account_status', 2)
                              ->whereNotNull('account_acceptor_id');
                    })
                    ->first();
    
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid login details or account not authorized.'], 401);
        }
    
        $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }
    

    /**
     * Log out the current user by revoking their tokens.
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
