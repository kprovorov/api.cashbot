<?php

namespace App\PaymentModule\Jobs;

use App\PaymentModule\Models\Payment;
use App\PaymentModule\Services\PaymentService;
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
     *
     * @throws Exception
     */
    public function handle(PaymentService $paymentService): void
    {
        $paymentService->updateCurrencyAmount($this->payment);
    }
}
