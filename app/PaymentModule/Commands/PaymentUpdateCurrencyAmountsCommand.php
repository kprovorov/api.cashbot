<?php

namespace App\PaymentModule\Commands;

use App\PaymentModule\Jobs\UpdatePaymentCurrencyAmountsJob;
use Illuminate\Console\Command;

class PaymentUpdateCurrencyAmountsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:update-currency-amounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update amount for payments where currency is different from account currency';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        dispatch(new UpdatePaymentCurrencyAmountsJob());
    }
}
