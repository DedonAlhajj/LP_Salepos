<?php

namespace App\Http\Controllers\Tenant;

use App\DTOs\SmsTemplateDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\SmsTemplateRequest;
use App\Models\SmsTemplate;
use App\Services\Tenant\SmsTemplateService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SmsTemplateController extends Controller
{

    protected SmsTemplateService $smsTemplateService;

    public function __construct(SmsTemplateService $smsTemplateService)
    {
        $this->smsTemplateService = $smsTemplateService;
    }

    public function index():View
    {
        $templates = $this->smsTemplateService->getSmsTemplateAll();

        return view('Tenant.sms_templates.index',compact('templates'));
    }

    public function store(SmsTemplateRequest $request): RedirectResponse
    {
        try {
            // تحويل البيانات إلى DTO
            $dto = SmsTemplateDTO::fromRequest($request);

            // تنفيذ العملية عبر الـ Service
            $this->smsTemplateService->createSmsTemplate($dto);

            return redirect()->route('smstemplates.index')->with('message', 'SMS Template created successfully.');
        } catch (\Exception $e) {
            return redirect()->route('smstemplates.index')->with('not_permitted', 'Unexpected error occurred.');
        }
    }

    public function update(SmsTemplateRequest $request, string $id): RedirectResponse
    {
        try {
            // تحويل البيانات إلى DTO
            $dto = SmsTemplateDTO::fromRequest($request);

            // تنفيذ العملية عبر الـ Service
            $this->smsTemplateService->updateSmsTemplate($dto);

            return redirect()->route('smstemplates.index')->with('message', 'SMS Template updated successfully.');
        } catch (\Throwable $e) {
            return redirect()->route('smstemplates.index')->with('not_permitted', 'Unexpected error occurred.');
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        try {
            // Call the SmsTemplate service to delete the specified SmsTemplate record.
            $this->smsTemplateService->deleteSmsTemplate($id);

            // Redirect back with a success message upon successful deletion.
            return redirect()->back()->with('message', 'SmsTemplate deleted successfully');
        } catch (ModelNotFoundException $e) {
            // Handle the case where the SmsTemplate record is not found.
            return redirect()->back()->with(['not_permitted' => 'SmsTemplate not found!']);
        } catch (\Exception $e) {
            // Handle any general exceptions and return an appropriate error message.
            return redirect()->back()->with(['not_permitted' => 'Failed to delete SmsTemplate. ' . $e->getMessage()]);
        }
    }

}
