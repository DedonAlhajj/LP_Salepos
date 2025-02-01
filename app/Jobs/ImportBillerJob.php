<?php

namespace App\Jobs;

use App\Actions\SendMailAction;
use App\Mail\BillerCreate;
use App\Models\Biller;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportBillerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $sendMailAction;
    protected $tenantId;

    public function __construct(array $data, $tenantId)
    {
        $this->data = $data;
        $this->tenantId = $tenantId;
    }

    public function handle(SendMailAction $sendMailAction)
    {
        try {
            $biller = Biller::firstOrNew(['company_name' => $this->data['company_name']]);

            $biller->fill($this->data);
            $biller->save();

            // إرسال الإيميل بعد نجاح العملية
            $sendMailAction->execute($this->data, BillerCreate::class);

        } catch (\Exception $e) {
            Log::error("❌ فشل استيراد الفاتورة: " . $e->getMessage());
        }
    }
}
