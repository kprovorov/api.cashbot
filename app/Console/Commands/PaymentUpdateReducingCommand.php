<?php

namespace App\Console\Commands;

use App\Jobs\UpdateReducingPaymentsJob;
use Illuminate\Console\Command;

class PaymentUpdateReducingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:update-reducing-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update reducing payments';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        dispatch(new UpdateReducingPaymentsJob());
    }
}
