<?php

namespace App\Services\Tenant;


use App\DTOs\SmsTemplateDTO;
use App\Models\SmsTemplate;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SmsTemplateService
{

    public function getSmsTemplateAll():Collection
    {
        return SmsTemplate::all();
    }

    public function createSmsTemplate(SmsTemplateDTO $dto): SmsTemplate
    {
        return DB::transaction(function () use ($dto) {
            try {
                if ($dto->is_default) {
                    DB::table('sms_templates')->where('is_default', true)->update(['is_default' => false]);
                }

                if ($dto->is_default_ecommerce) {
                    DB::table('sms_templates')->where('is_default_ecommerce', true)->update(['is_default_ecommerce' => false]);
                }

                return SmsTemplate::create($dto->toArray());
            } catch (\Throwable $e) {
                Log::error('Failed to create SMS Template', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        });
    }

    public function updateSmsTemplate(SmsTemplateDTO $dto): SmsTemplate
    {
        return DB::transaction(function () use ($dto) {
            try {
                // البحث عن القالب أو إرجاع خطأ تلقائيًا إذا لم يتم العثور عليه
                $template = SmsTemplate::findOrFail($dto->smstemplate_id);

                if ($dto->is_default) {
                    DB::table('sms_templates')
                        ->where('id', '!=', $template->id)
                        ->where('is_default', true)
                        ->update(['is_default' => false]);
                }

                if ($dto->is_default_ecommerce) {
                    DB::table('sms_templates')
                        ->where('id', '!=', $template->id)
                        ->where('is_default_ecommerce', true)
                        ->update(['is_default_ecommerce' => false]);
                }

                $template->update($dto->toArray());

                return $template;
            } catch (\Throwable $e) {
                Log::error('Failed to update SMS Template', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        });
    }

    public function deleteSmsTemplate(int $id): bool
    {
        try {
            // Attempt to find and delete a SmsTemplate by its ID
            SmsTemplate::findOrFail($id)->delete();
            return true;
        } catch (ModelNotFoundException $e) {
            // Log error and rethrow if SmsTemplate is not found
            Log::error('SmsTemplate not found: ' . $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            // Log and rethrow for general errors
            Log::error('Error deleting SmsTemplate: ' . $e->getMessage());
            throw $e;
        }
    }




}

