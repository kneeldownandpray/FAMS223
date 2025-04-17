<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Skill;
use App\Models\Certification;
use App\Models\Hired;
use App\Models\VisaStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisaStatusController extends Controller
{
    public function index(Request $request)
{
    $perPage = $request->input('per_page', 20);
    $name = $request->input('name');
    $profession = $request->input('profession');
    $visaStep = $request->input('visa_step'); // e.g., 'requirements'
    
    $query = Hired::with(['applicant.resume', 'applicant.visaStatus', 'employer']);

    // Filter by name
    if ($name) {
        $query->whereHas('applicant', function ($q) use ($name) {
            $q->where('first_name', 'LIKE', "%$name%")
              ->orWhere('last_name', 'LIKE', "%$name%");
        });
    }

    // Filter by profession
    if ($profession) {
        $query->whereHas('applicant.resume', function ($q) use ($profession) {
            $q->where('profession', 'LIKE', "%$profession%");
        });
    }

    // Filter by specific visa step (e.g., only those with requirements == 1)
    if ($visaStep && in_array($visaStep, [
        'application_received',
        'interview_employer_confirmation',
        'requirements',
        'skill_assessment',
        'visa_preparation',
        'visa_lodged',
        'medical_biometrics',
        'awaiting_decision',
        'visa_outcome',
        'ready_to_fly'
    ])) {
        $query->whereHas('applicant.visaStatus', function ($q) use ($visaStep) {
            $q->where($visaStep, 1);
        });
    }

    $hiredApplicants = $query->paginate($perPage);

    $filtered = $hiredApplicants->getCollection()->filter(function ($hired) {
        return $hired->applicant && $hired->applicant->visaStatus;
    });

    $result = $filtered->map(function ($hired) {
        $user = $hired->applicant;

        return [
            'worker_id' => $user->id,
            'employer_id' => $hired->employer->id,
            'applicant_name' => $user->first_name . ' ' . $user->last_name,
            'profession' => optional($user->resume)->profession,
            'employer_name' => $hired->employer->first_name . ' ' . $hired->employer->last_name,
            'visa_status' => [
                'visa_id' => $user->visaStatus->id,
                'application_received' => $user->visaStatus->application_received,
                'interview_employer_confirmation' => $user->visaStatus->interview_employer_confirmation,
                'requirements' => $user->visaStatus->requirements,
                'skill_assessment' => $user->skill_assessment == 1
                    ? $user->visaStatus->skill_assessment
                    : null,
                'visa_preparation' => $user->visaStatus->visa_preparation,
                'visa_lodged' => $user->visaStatus->visa_lodged,
                'medical_biometrics' => $user->visaStatus->medical_biometrics,
                'awaiting_decision' => $user->visaStatus->awaiting_decision,
                'visa_outcome' => $user->visaStatus->visa_outcome,
                'ready_to_fly' => $user->visaStatus->ready_to_fly,
            ]
        ];
    });

    $hiredApplicants->setCollection($result->values());

    return response()->json($hiredApplicants);
}

    public function update(Request $request, $workerId)
{
    $request->validate([
        'field' => 'required|string',
        'value' => 'required|boolean'
    ]);

    $user = User::findOrFail($workerId);
    $visaStatus = $user->visaStatus;

    if (!$visaStatus) {
        return response()->json(['message' => 'Visa status not found'], 404);
    }

    $field = $request->input('field');

    if (!in_array($field, [
        'application_received',
        'interview_employer_confirmation',
        'requirements',
        'skill_assessment',
        'visa_preparation',
        'visa_lodged',
        'medical_biometrics',
        'awaiting_decision',
        'visa_outcome',
        'ready_to_fly'
    ])) {
        return response()->json(['message' => 'Invalid field'], 400);
    }

    $visaStatus->$field = $request->input('value');
    $visaStatus->save();

    return response()->json(['message' => 'Visa status updated successfully']);
}

public function getProgress()
{
    $user2 = Auth::user();
    $user = User::with('visaStatus')->findOrFail($user2->id);

    $visaStatus = $user->visaStatus;

    if (!$visaStatus) {
        return response()->json(['message' => 'Visa status not found'], 404);
    }

    // Define the visa status steps
    $steps = [
        'application_received',
        'interview_employer_confirmation',
        'requirements',
        'skill_assessment',
        'visa_preparation',
        'visa_lodged',
        'medical_biometrics',
        'awaiting_decision',
        'visa_outcome',
        'ready_to_fly',
    ];

    $progressCount = 0;

    foreach ($steps as $step) {
        $value = $visaStatus->$step;

        if ($value === 1) {
            // Count if it's 1
            $progressCount++;
        } elseif ($value === 3) {
            // Count if it's 3 
            $progressCount++;
          // Stop counting after encountering 3
        } else {
            // Stop counting if it's 0
            break;
        }
    }

    return response()->json([
        'user_id' => $user->id,
        'applicant_name' => $user->first_name . ' ' . $user->last_name,
        'visa_progress' => $progressCount,
        'skill_assestment' => $user->skill_assessment,
        
    ]);
}


}
