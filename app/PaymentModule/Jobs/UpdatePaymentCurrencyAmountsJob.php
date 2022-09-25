<?php

namespace App\PaymentModule\Jobs;

use App\PaymentModule\Services\PaymentService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdatePaymentCurrencyAmountsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     *
     * @throws Exception
     */
    public function handle(PaymentService $paymentService): void
    {
        $paymentService->updateCurrencyAmounts();
    }
}
