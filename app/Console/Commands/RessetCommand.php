<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RessetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:room';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $da= date( "Y-m-d" , now()->timestamp );
        $date  = Carbon::parse($da)->timestamp;

        $a=   Bill::query()->where('day_in',$date)->get();

        foreach ($a as $b){
            $room=   Room::query()->find($b->room_id);
            if($room->status==4){
                Room::query()->where('id',$b->room_id)->update(
                    ['status' => 1]
                );
                Bill::query()->where('id',$b->id)->update(['status'=>3]);
            }
            Bill::query()->where('id',$b->id)->update(['status'=>3]);
        }
        $this->info('Demo:Cron Cummand Run successfully!');
    }
}
