<?php

namespace App\Console\Commands;


use App\Constants\QueueName;
use Illuminate\Console\Command;

class QueueCheck extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check queue';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $queueSetArray = QueueName::getQueueList();
        $queueArray = array_keys($queueSetArray);
        foreach ($queueArray as $queue) {
            $results=null;
            exec("sudo ps aux|grep {$queue}",$results);
            $checkResult = false;
            foreach ($results as $result) {
                $lines = explode("--queue=",$result);
                if(count($lines)>1){
                    $lineChild = explode(" ",$lines[1]);
                    if ($lineChild[0] == $queue){
                        $checkResult = true;
                    }
                }
            }
            if(!$checkResult){
                popen("php artisan queue:listen --queue='{$queue}' --tries=3 --timeout={$queueSetArray[$queue]} &",'r');
                echo $queue." not is running \n";
            }else{
                echo $queue." is running \n";
            }
        }
    }
}