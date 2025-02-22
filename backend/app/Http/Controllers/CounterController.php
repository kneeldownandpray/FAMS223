<?php

namespace App\Http\Controllers;

use App\Models\AccountInformations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CounterController extends Controller
{
    public function countStatusOne()
    {
        // Get the logged-in user's account type
        $user = Auth::user();
        $accountType = $user->account_type;

        // Initialize the query for AccountInformations with a join to the Users table
        $query = AccountInformations::where('account_informations.account_status', 1)
            ->join('users', 'account_informations.user_id', '=', 'users.id');


        if ($accountType == 4 || $accountType == 3) {

            $query->whereIn('users.account_type', [5, 6]);
        } elseif ($accountType == 2) {

            $query->whereIn('users.account_type', [4, 3, 5, 6]);
        } elseif ($accountType == 1) {
    
            $query->whereIn('users.account_type', [1, 2, 4, 3, 5, 6]);
        }
        $count = $query->count();

        // Return the count as a JSON response
        return response()->json($count, 200);
    }
}
