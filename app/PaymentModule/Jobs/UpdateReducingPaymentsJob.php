<?php

namespace App\PaymentModule\Jobs;

use App\PaymentModule\Services\PaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateReducingPaymentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @param  PaymentService  $paymentService
     * @return void
     */
    public function handle(PaymentService $paymentService): void
    {
        $paymentService->updateReducingPayments();
    }
}
