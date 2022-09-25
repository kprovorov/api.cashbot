<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Services\PaymentService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdatePaymentCurrencyAmountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Payment $payment)
    {
    }

    /**
     * Execute the job.
     *
     * @param  PaymentService  $paymentService
     * @return void
     *
     * @throws Exception
     */
    public function handle(PaymentService $paymentService): void
    {
        $paymentService->updateCurrencyAmount($this->payment);
    }
}
