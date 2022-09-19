<?php

namespace App\Console\Commands;

use App\Jobs\UpdatePaymentCurrencyAmounts;
use Illuminate\Console\Command;

class PaymentUpdateCurrencyAmounts extends Command
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
     *
     * @return void
     */
    public function handle(): void
    {
        dispatch(new UpdatePaymentCurrencyAmounts());
    }
}
