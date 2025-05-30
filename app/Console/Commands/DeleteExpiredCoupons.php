<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use Illuminate\Console\Command;

class DeleteExpiredCoupons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coupons:delete-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all expired coupons from the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Coupon::where('end_date', '<', now()->toDateString())->delete();
        $this->info('Expired coupons deleted.');
    }
}
