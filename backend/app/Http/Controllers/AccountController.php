<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $userAccountType = $user->account_type;
    
        $search = $request->input('search');
        $accountType = $request->input('account_type');
        $perPage = $request->input('per_page', 10);
    
        $query = User::query()
            ->leftJoin('account_informations', 'users.id', '=', 'account_informations.user_id')
            ->select('users.*', 'account_informations.skills_assessment', 'account_informations.account_acceptor_id', 'account_informations.account_status');
        if ($userAccountType === 1) {
            if ($accountType) {
                $query->where('users.account_type', $accountType);
            }
        } else {
            if ($accountType) {
                $query->where('users.account_type', $accountType);
            } else {
                $query->where('users.account_type', $userAccountType);
            }
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.first_name', 'like', "%{$search}%")
                  ->orWhere('users.last_name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%");
            });
        }
    
        $users = $query->paginate($perPage);
    
        return response()->json($users);
    }

    public function accountValidatorAcceptor(Request $request)
    {
     
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer', // Ensure 'id' is present and is an integer
        ]);
    
        // if ($validator->fails()) {
        //     return response()->json(['errors' => $validator->errors()], 422); // Return validation errors if any
        // }
    
        // Retrieve the validated ID from the request
        $id = $request->input('id');
    
        // Get the currently authenticated user
        $user = $request->user();
        $userId = $user->id;
    
        // Update the account status and account acceptor ID
        $updated = DB::table('account_informations')
            ->where('user_id', $id) // Target records with user_id matching the provided ID
            ->update([
                'account_status' => 2, // Set status to 2
                'account_acceptor_id' => $userId, // Set the acceptor ID to the logged-in user's ID
            ]);
    
        // Check if the update was successful
        if ($updated) {
            return response()->json(['message' => 'Update successful'], 200);
        } else {
            return response()->json(['message' => 'No records updated'], 404);
        }
    }
    
    
    public function pendingAccounts(Request $request)
    {
        // Get the logged-in user and their account type
        $user = $request->user();
        $userAccountType = $user->account_type;

        // Input parameters
        $search = $request->input('search');
        $accountType = $request->input('account_type');
        $status = $request->input('status', 2); // Default to status 2 if not provided
        $perPage = $request->input('per_page', 10);

        // Base query for Users with a left join on AccountInformations
        $query = User::query()
            ->leftJoin('account_informations', 'users.id', '=', 'account_informations.user_id')
            ->select('users.*', 'account_informations.skills_assessment', 'account_informations.account_acceptor_id', 'users.account_type', 'account_informations.account_status')
            ->where('account_informations.account_status', '!=', $status); // Exclude users with the specified status

        // Logic to filter based on the logged-in user's account type
        if ($userAccountType === 1) {
            // Developer (account_type 1): Can see all users
            if ($accountType) {
                $query->where('users.account_type', $accountType);
            }
        } elseif ($userAccountType === 2) {
            // Head Admin (account_type 2): Can see users with account types 3, 4, 5, 6
            $query->whereIn('users.account_type', [3, 4, 5, 6]);
            if ($accountType) {
                $query->where('users.account_type', $accountType);
            }
        } elseif (in_array($userAccountType, [3, 4])) {
            // Admin Staff (account_type 3 or 4): Can see users with account types 5, 6
            $query->whereIn('users.account_type', [5, 6]);
            if ($accountType) {
                $query->where('users.account_type', $accountType);
            }
        } else {
            // Other users: Can only view accounts matching their own account type
            if ($accountType) {
                $query->where('users.account_type', $accountType);
            } else {
                $query->where('users.account_type', $userAccountType);
            }
        }

        // Search functionality
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.first_name', 'like', "%{$search}%")
                  ->orWhere('users.last_name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        // Paginate the filtered results
        $users = $query->paginate($perPage);

        // Return the paginated list of users as a JSON response
        return response()->json($users);
    }
    public function getFilteredAccounts(Request $request)
    {
        $user = $request->user();
        $userAccountType = $user->account_type;

        $search = $request->input('search');
        $accountType = $request->input('account_type');
        $status = $request->input('status', 2); // Default to status 2 if not provided
        $perPage = $request->input('per_page', 10);

        $query = User::query()
            ->leftJoin('account_informations', 'users.id', '=', 'account_informations.user_id')
            ->select('users.*', 'account_informations.skills_assessment', 'account_informations.account_acceptor_id', 'account_informations.account_status')
            ->where('account_informations.account_status', $status)
            ->whereNotNull('account_informations.account_acceptor_id');

        if ($userAccountType === 1) {
            if ($accountType) {
                $query->where('users.account_type', $accountType);
            }
        } else {
            if ($accountType) {
                $query->where('users.account_type', $accountType);
            } else {
                $query->where('users.account_type', $userAccountType);
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.first_name', 'like', "%{$search}%")
                  ->orWhere('users.last_name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate($perPage);

        return response()->json($users);
    }



    public function shortpolling(Request $request)
    {
        $user = $request->user();
        return response()->json($user); // Return the logged-in user's data
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'account_type' => 'required|integer|in:1,2,3,4,5,6', 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->account_type = $request->input('account_type');
        $user->save();

        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * Change a user's password without requiring current password.
     */
    public function changePassword(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Update the password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully']);
    }
}