<?php

namespace App\Http\Controllers;

use App\Models\Hired;
use Illuminate\Http\Request;
use App\Models\User;

class HiredController extends Controller
{
    public function hire(Request $request)
    {
        if (auth()->user()->account_type == 5) { 
            $request->validate([
                'applicant_id' => 'required|exists:users,id', // Only require applicant_id
            ]);

            $hired = Hired::create([
                'employer_id' => auth()->user()->id, // Use logged-in user's ID as employer_id
                'applicant_id' => $request->applicant_id,
                'approval_of_admin' => false, // Default to false until admin approves
            ]);

            return response()->json($hired, 201);
        } else {
            return response()->json(['message' => 'This account is not an employer'], 404);
        }
    }

    public function showInterested($applicant_id, $selected_employer_id)
    {
        // Get all employers interested in this worker but exclude the selected employer
        $hiredRecords = Hired::with(['applicant', 'applicant.resume', 'employer']) // Eager load applicant and employer relationships
                            ->where('applicant_id', $applicant_id)
                            ->where('approval_of_admin', 0) // Not yet approved
                            ->where('employer_id', '!=', $selected_employer_id) // Exclude the selected employer
                            ->get();
        
        // If there are no other interested employers, return true with status
        if ($hiredRecords->isEmpty()) {
            return response()->json([
                'status' => true, // No other employers are interested
                'message' => 'No other employers are interested in this worker.'
            ]);
        }
    
        // Add the employer's first and last name to the response and remove other details
        foreach ($hiredRecords as $hired) {
            $hired->employer_name = $hired->employer ? $hired->employer->first_name . ' ' . $hired->employer->last_name : null;
            
            // Remove unnecessary fields from the employer and applicant data
            unset($hired->employer);
            unset($hired->applicant);
        }
    
        // If there are other interested employers, return false with the list and employer names
        return response()->json([
            'status' => false, // There are other employers interested
            'interested_employers' => $hiredRecords
        ]);
    }
    
    

    public function approve($id)
    {
        if (auth()->user()->account_type <= 3) { // Employer check
            $hired = Hired::findOrFail($id);
    
            // Approve the selected employer and worker
            $hired->approval_of_admin = true;
            $hired->save();
    
            // Decline all other employers for the same worker
            Hired::where('applicant_id', $hired->applicant_id)
                ->where('id', '!=', $id) // Exclude the approved record
                ->update(['approval_of_admin' => 4]); // Set status to declined (4)
    
            return response()->json($hired);
        } else {
            return response()->json(['message' => 'This account is not an admin'], 404);
        }
    }

    public function rejected($id)
    {
        if (auth()->user()->account_type <= 3) { // Employer check}
        $hired = Hired::findOrFail($id);
        $hired->approval_of_admin = 3;
        $hired->save();

        return response()->json($hired);
    }
    else {
        return response()->json(['message' => 'This account is not an admin'], 404);
    }
    }

    public function restore($id)
    {
        if (auth()->user()->account_type <= 3) { // Employer check}
        $hired = Hired::findOrFail($id);
        $hired->approval_of_admin = 0;
        $hired->save();

        return response()->json($hired); }
        else{
            return response()->json(['message' => 'This account is not an admin'], 404);
        }
        
    }

    public function unlinkPermanently($applicantId)
    {
        if (auth()->user()->account_type <= 5) {
        try {
            $hired = Hired::where('applicant_id', $applicantId)->firstOrFail();
            $hired->delete();
    
            return response()->json(['message' => 'Hired record deleted permanently', 'applicant_id' => $applicantId]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Hired record not found for this applicant'], 404);
        }
    }
    else{
        return response()->json(['message' => 'This account is not an admin'], 404);
    }
    }



    public function isUserHired($applicantId)
    {
        $userId = auth()->user()->id;
    
        // Check if the logged-in user has hired the specified applicant
        $isHired = Hired::where('employer_id', $userId)
            ->where('applicant_id', $applicantId)
            ->exists();
    
        return response()->json(['is_hired' => $isHired]);
    }
    public function displayAllNotAcceptable(Request $request)
{
    if (auth()->user()->account_type <= 3) { // Admin check
        $perPage = $request->query('perPage', 10); // Default to 10 if perPage is not provided

        $hiredRecords = Hired::with(['employer', 'applicant', 'applicant.resume']) // Eager load resumes
            ->where('approval_of_admin', 0)
            ->paginate($perPage); // Paginate with dynamic perPage

        return response()->json($hiredRecords);
    } else {
        return response()->json(['message' => 'This account is not an admin'], 403);
    }
}

public function displayAllHired(Request $request)
{
    if (auth()->user()->account_type <= 3) { // Admin check
        $perPage = $request->query('perPage', 10); // Default to 10 if perPage is not provided

        $hiredRecords = Hired::with(['employer', 'applicant', 'applicant.resume']) // Eager load resumes
            ->where('approval_of_admin', 1)
            ->paginate($perPage); // Paginate with dynamic perPage

        return response()->json($hiredRecords);
    } else {
        return response()->json(['message' => 'This account is not an admin'], 403);
    }
}

public function displayAllRejected(Request $request)
{
    if (auth()->user()->account_type <= 3) { // Admin check
        $perPage = $request->query('perPage', 10); // Default to 10 if perPage is not provided

        $hiredRecords = Hired::with(['employer', 'applicant', 'applicant.resume']) // Eager load resumes
            ->where('approval_of_admin', 3)
            ->paginate($perPage); // Paginate with dynamic perPage

        return response()->json($hiredRecords);
    } else {
        return response()->json(['message' => 'This account is not an admin'], 403);
    }
}
// Method to get all applicants hired by each employer


public function displayAllDataDropdown(Request $request)
{
    
    if (auth()->user()->account_type == 1) {
    $perPage = $request->input('per_page', 10); // Get per_page parameter or default to 10
    $currentPage = $request->input('page', 1); // Get current page number or default to 1

    // Get hired applicants with approval_of_admin = 1, paginating by employer
    $hiredApplicants = Hired::with(['applicant', 'employer'])
        ->where('approval_of_admin', 1) // Filter by approval_of_admin
        ->get()
        ->groupBy('employer_id'); // Group by employer ID

    // Prepare the paginated response
    $response = [];
    $employerCount = 0;

    foreach ($hiredApplicants as $employerId => $hired) {
        if ($employerCount >= ($currentPage - 1) * $perPage && $employerCount < $currentPage * $perPage) {
            $employer = User::find($employerId); // Get employer details

            if ($employer) {
                $applicantDetails = $hired->map(function ($hire) {
                    $applicant = $hire->applicant; // Get the applicant
                    $resume = $applicant->resume; // Get the resume

                    return [
                        'id' => $applicant->id, // Include ID
                        'full_name' => $resume->full_name, // Get full name from Resume
                        'address' => $resume->address, // Get address from Resume
                        'birth_address' => $resume->birth_address,
                        'height' => $resume->height,
                        'weight' => $resume->weight,
                        'objectives' => $resume->objectives,
                        'civil_status' => $resume->civil_status,
                        'religion' => $resume->religion,
                        'nationality' => $resume->nationality,
                        'contact_no' => $resume->contact_no,
                        'profession' => $resume->profession,
                    ];
                })->toArray();

                $response[] = [
                    'employer' => [
                        'id' => $employer->id,
                        'first_name' => $employer->first_name,
                        'last_name' => $employer->last_name,
                        'email' => $employer->email,
                        'contact_no' => $employer->contact_no,
                        // 'company' => $employer->accountInformation->company, // If the company info is in AccountInformations
                    ],
                    'applicants' => $applicantDetails,
                ];
            }
        }
        $employerCount++;
    }

    // Create the paginator
    $totalEmployers = count($hiredApplicants); // Total number of employers
    $paginatedResponse = [
        'data' => $response,
        'current_page' => $currentPage,
        'per_page' => $perPage,
        'total' => $totalEmployers,
        'last_page' => ceil($totalEmployers / $perPage),
    ];

    return response()->json($paginatedResponse);
}
else{
    return response()->json(['message' => 'This account is not an admin'], 403);
}
}
public function displayAllApplicantUnderByLoggedInEmployer(Request $request)
{
    if (auth()->user()->account_type == 5) { // Employer check
        $perPage = $request->query('perPage', 10); // Default to 10 if perPage is not provided

        $hiredRecords = Hired::with(['applicant', 'applicant.resume']) // Eager load resumes
            ->where('employer_id', auth()->user()->id) // Filter by logged-in employer
            ->where('approval_of_admin', 1) // Filter rejected applicants
            ->paginate($perPage); // Paginate with dynamic perPage

        return response()->json($hiredRecords);
    } else {
        return response()->json(['message' => 'This account is not an employer'], 403);
    }
}
public function displayAllApplicantUnderByLoggedInEmployerHired(Request $request)
{
    if (auth()->user()->account_type == 5) { // Employer check
        $perPage = $request->query('perPage', 10); // Default to 10 if perPage is not provided

        $hiredRecords = Hired::with(['applicant', 'applicant.resume']) // Eager load resumes
            ->where('employer_id', auth()->user()->id) // Filter by logged-in employer
            ->where('approval_of_admin', 1) // Filter accepted applicants
            ->paginate($perPage); // Paginate with dynamic perPage

        return response()->json($hiredRecords);
    } else {
        return response()->json(['message' => 'This account is not an employer'], 403);
    }
}
public function displayAllApplicantUnderByLoggedInEmployerPending(Request $request)
{
    if (auth()->user()->account_type == 5) { // Employer check
        $perPage = $request->query('perPage', 10); // Default to 10 if perPage is not provided

        // Fetch hired records with eager loading of applicant and applicant's resume
        $hiredRecords = Hired::with(['applicant', 'applicant.resume']) // Eager load relationships
            ->where('employer_id', auth()->user()->id) // Filter by logged-in employer
            ->where('approval_of_admin', 0) // Filter pending applicants
            ->paginate($perPage); // Paginate with dynamic perPage

        // Customize the response format
        return response()->json([
            'current_page' => $hiredRecords->currentPage(),
            'data' => $hiredRecords->map(function ($record) {
                return [
                    'id' => $record->id,
                    'employer_id' => $record->employer_id,
                    'applicant_id' => $record->applicant_id,
                    'approval_of_admin' => $record->approval_of_admin,
                    'created_at' => $record->created_at,
                    'updated_at' => $record->updated_at,
                    'applicant' => [
                        'id' => $record->applicant->id,
                        'first_name' => $record->applicant->first_name,
                        'created_at' => $record->applicant->created_at,
                        'updated_at' => $record->applicant->updated_at,
                        'account_type' => $record->applicant->account_type,
                        'resume' => [
                            'id' => $record->applicant->resume->id,
                            'user_id' => $record->applicant->resume->user_id,
                            'profession' => $record->applicant->resume->profession,
                            'created_at' => $record->applicant->resume->created_at,
                            'updated_at' => $record->applicant->resume->updated_at,
                        ]
                    ]
                ];
            }),
            'first_page_url' => $hiredRecords->url(1),
            'from' => $hiredRecords->firstItem(),
            'last_page' => $hiredRecords->lastPage(),
            'last_page_url' => $hiredRecords->url($hiredRecords->lastPage()),
            'links' => [
                [
                    'url' => $hiredRecords->previousPageUrl(),
                    'label' => '&laquo; Previous',
                    'active' => $hiredRecords->onFirstPage() ? false : true
                ],
                [
                    'url' => $hiredRecords->url($hiredRecords->currentPage()),
                    'label' => (string) $hiredRecords->currentPage(),
                    'active' => true
                ],
                [
                    'url' => $hiredRecords->nextPageUrl(),
                    'label' => 'Next &raquo;',
                    'active' => $hiredRecords->hasMorePages() ? true : false
                ]
            ],
            'next_page_url' => $hiredRecords->nextPageUrl(),
            'path' => url()->current(),
            'per_page' => $hiredRecords->perPage(),
            'prev_page_url' => $hiredRecords->previousPageUrl(),
            'to' => $hiredRecords->lastItem(),
            'total' => $hiredRecords->total()
        ]);
    } else {
        return response()->json(['message' => 'This account is not an employer'], 403);
    }
}


public function displayAllApplicantUnderByLoggedInEmployerRejected(Request $request)
{
    if (auth()->user()->account_type == 5) { // Employer check
        $perPage = $request->query('perPage', 10); // Default to 10 if perPage is not provided

        // Fetch hired records with eager loading of applicant and applicant's resume
        $hiredRecords = Hired::with(['applicant', 'applicant.resume']) // Eager load relationships
            ->where('employer_id', auth()->user()->id) // Filter by logged-in employer
            ->where('approval_of_admin', 3) // Filter pending applicants
            ->paginate($perPage); // Paginate with dynamic perPage

        // Customize the response format
        return response()->json([
            'current_page' => $hiredRecords->currentPage(),
            'data' => $hiredRecords->map(function ($record) {
                return [
                    'id' => $record->id,
                    'employer_id' => $record->employer_id,
                    'applicant_id' => $record->applicant_id,
                    'approval_of_admin' => $record->approval_of_admin,
                    'created_at' => $record->created_at,
                    'updated_at' => $record->updated_at,
                    'applicant' => [
                        'id' => $record->applicant->id,
                        'first_name' => $record->applicant->first_name,
                        'created_at' => $record->applicant->created_at,
                        'updated_at' => $record->applicant->updated_at,
                        'account_type' => $record->applicant->account_type,
                        'resume' => [
                            'id' => $record->applicant->resume->id,
                            'user_id' => $record->applicant->resume->user_id,
                            'profession' => $record->applicant->resume->profession,
                            'created_at' => $record->applicant->resume->created_at,
                            'updated_at' => $record->applicant->resume->updated_at,
                        ]
                    ]
                ];
            }),
            'first_page_url' => $hiredRecords->url(1),
            'from' => $hiredRecords->firstItem(),
            'last_page' => $hiredRecords->lastPage(),
            'last_page_url' => $hiredRecords->url($hiredRecords->lastPage()),
            'links' => [
                [
                    'url' => $hiredRecords->previousPageUrl(),
                    'label' => '&laquo; Previous',
                    'active' => $hiredRecords->onFirstPage() ? false : true
                ],
                [
                    'url' => $hiredRecords->url($hiredRecords->currentPage()),
                    'label' => (string) $hiredRecords->currentPage(),
                    'active' => true
                ],
                [
                    'url' => $hiredRecords->nextPageUrl(),
                    'label' => 'Next &raquo;',
                    'active' => $hiredRecords->hasMorePages() ? true : false
                ]
            ],
            'next_page_url' => $hiredRecords->nextPageUrl(),
            'path' => url()->current(),
            'per_page' => $hiredRecords->perPage(),
            'prev_page_url' => $hiredRecords->previousPageUrl(),
            'to' => $hiredRecords->lastItem(),
            'total' => $hiredRecords->total()
        ]);
    } else {
        return response()->json(['message' => 'This account is not an employer'], 403);
    }
}

}
