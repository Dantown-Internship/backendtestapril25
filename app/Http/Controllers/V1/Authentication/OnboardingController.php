<?php

namespace App\Http\Controllers\V1\Authentication;

use App\Actions\Company\CreateCompanyAction;
use App\Actions\User\CreateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Authentication\OnboardingRequest;
use App\Http\Resources\V1\Authentication\OnboardingResource;
use App\Jobs\BackgroundProcessing\AuditLog\AuditLogActivityBackgroundProcessingJob;
use App\Jobs\BackgroundProcessing\Onboarding\SetupCompanyExpenseCategoriesBackgroundProcessingJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OnboardingController extends Controller
{
    public function __construct(
        private CreateCompanyAction $createCompanyAction,
        private CreateUserAction $createUserAction,
    ) {}
    public function __invoke(OnboardingRequest $request)
    {
        $createdUser = DB::transaction(function () use ($request) {
            $company = $this->createCompanyAction->execute(
                $request->company
            );

            $createUserRecordOptions = array_merge($request->user, [
                'company_id' => $company->id,
                'role' => 'Admin',
                'password' => Hash::make($request->user['password'])
            ]);

            unset($createUserRecordOptions['password_confirmation']);

            $user = $this->createUserAction->execute(
                $createUserRecordOptions
            );

            return $user;
        });

        dispatch(
            new AuditLogActivityBackgroundProcessingJob([
                'user_id' => $createdUser->id,
                'action' => "{$createdUser->name} created an account",
                'changes' => extractObjectPropertiesToKeyPairValues([
                    'user' => $request->user,
                    'company' => $request->company,
                ])
            ])
        );

        dispatch(
            new SetupCompanyExpenseCategoriesBackgroundProcessingJob([
                'company_id' => $createdUser->company_id
            ])
        );

        $responsePayload = new OnboardingResource($createdUser);

        return generateSuccessApiMessage('User onboarding was successful', 201, $responsePayload);
    }
}
