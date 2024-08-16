<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

use App\Models\Income\IcmAgreement;

class ClosingTasks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $system_date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($system_date)
    {
        $this->system_date = $system_date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        # Cancelar liquidaciones
        $affected = DB::update("UPDATE icm_liquidations SET is_deleted = 1 WHERE liquidation_date < ? AND state='P'", [$this->system_date]);

        $system_date  = getSystemDate();
        $agreements   = IcmAgreement::where(['state' => 'A'])->whereDate('date_to', '<', $this->system_date)->get();

        foreach ($agreements as $key => $agreement) {
            $agreement->state = 'I';
            $agreement->update();
        }

    }
}
