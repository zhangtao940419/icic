<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $emailAddress;
    private $emailMessage;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($emailAddress,$emailMessage)
    {
        //
        $this->emailAddress = $emailAddress;
        $this->emailMessage = $emailMessage;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        Mail::send('email', ['emailMessage' => $this->emailMessage], function($message)
        {
            $message->to($this->emailAddress, 'BTPUser')->subject('CoinBAB驗證郵件');
        });
        if(Mail::failures())
        Log::channel('email')->info('邮件发送失败信息:',['toaddress'=>$this->emailAddress,'code'=>$this->emailMessage,'author'=>'zt','time'=>date('Y-m-d H:i:s',time())]);
//        Log::useFiles(storage_path() . '/logs/emailerror.log')->info('邮件发送失败信息:',['address'=>$this->emailAddress,'code'=>$this->emailMessage]);
    }
}
