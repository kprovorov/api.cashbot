<?php

namespace App\Jobs;

use App\Services\PaymentService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdatePaymentCurrencyAmounts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @param PaymentService $paymentService
     * @return void
     * @throws Exception
     */
    public function handle(PaymentService $paymentService): void
    {
        $paymentService->updateCurrencyAmounts();
    }
}
