<?php

namespace App\PaymentModule\Jobs;

use App\PaymentModule\Models\Payment;
use App\PaymentModule\Services\PaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateReducingPaymentJob implements ShouldQueue
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
     */
    public function handle(PaymentService $paymentService): void
    {
        $paymentService->updateReducingPayment($this->payment);
    }
}
