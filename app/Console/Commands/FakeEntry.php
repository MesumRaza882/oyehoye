<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{Product, User, UserToken};
use Carbon\Carbon;
use App\Helpers\Helper;


class FakeEntry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fake:entry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'In this Command we generate fake item solded entries by limit of specific items,
        when product not solded by admin or product quantity not ended.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        set_time_limit(0);
        // \Log::info('Fake Entery Log and video notifications 1');

        // change minute edit if not solded 
        $items = Product::where('product_status','published')->where('soldstatus', 0)->where('soldItem', '>', 0)->get();
        $itemsNotifications = Product::/*where('soldstatus', 0)->where('soldItem', '>', 0)->*/where('for_notification', 1)->latest()->get(['id','name','for_notification']);

        foreach ($itemsNotifications as $item) {
          
            \Log::info('Notification product send start');

            $item->for_notification = null;
            $item->save();

            if(strtolower($item->category_id) != "88"){
                $arr = [
                    'title' => 'Wao Collection New Item Added 😊',
                    'body' => $item->name,
                ];    
            }else{
                $arr = [
                    'title' => 'Client Feedbacks 😍',
                    'body' => $item->name,
                ];
            }
            $output = Helper::sendWebNotification($arr, []);

            // $FcmTokens1 = User::whereNotNull('remember_token')
            //     ->where('id', '>=', 1)
            //     ->where('id', '<', 1001)
            //     ->latest()
            //     ->get()
            //     ->pluck('remember_token');
            // if (!empty($FcmTokens1)) {
            //     $arr = [
            //         'title' => 'Wao Collection New Product Added',
            //         'body' => $item->name,
            //     ];
            //     // $regIdChunk=array_chunk($FcmTokens1,900);
            //     // foreach($regIdChunk as $FcmToken){
            //     $output = Helper::sendWebNotification($arr, $FcmTokens1);
            //     // }
            // }

            // // fsm Ntification send..........
            // $FcmTokens2 = User::whereNotNull('remember_token')->where('id', '>', 1000)->where('id', '<', 2000)->get()->pluck('remember_token');
            // if (!empty($FcmTokens2)) {
            //     $arr = [
            //         'title' => 'Wao Collection New Item Added',
            //         'body' => $item->name,
            //     ];
            //     $output = Helper::sendWebNotification($arr, $FcmTokens2);
            // }

            // // fsm Ntification send..........
            // $FcmTokens3 = User::whereNotNull('remember_token')
            //     ->where('id', '>', 2000)->where('id', '<', 3000)->get()->pluck('remember_token');
            // if (!empty($FcmTokens3)) {
            //     $arr = [
            //         'title' => 'Wao Collection New Item Added',
            //         'body' => $item->name,
            //     ];
            //     // $regIdChunk=array_chunk($FcmTokens3,900);
            //     // foreach($regIdChunk as $FcmToken){
            //     $output = Helper::sendWebNotification($arr, $FcmTokens3);
            //     // }
            // }
            // // end...........

            // $FcmTokens4 = User::whereNotNull('remember_token')
            //     ->where('id', '>', 3000)->where('id', '<', 4000)->get()->pluck('remember_token');
            // if (!empty($FcmTokens4)) {
            //     $arr = [
            //         'title' => 'Wao Collection New Item Added',
            //         'body' => $item->name,
            //     ];
            //     // $regIdChunk=array_chunk($FcmTokens4,900);
            //     // foreach($regIdChunk as $FcmToken){
            //     $output = Helper::sendWebNotification($arr, $FcmTokens4);
            //     // }
            // }


            // $tokens = UserToken::where('admin_id', 1)
            // ->latest()
            // ->get()
            // ->pluck('device_token')
            // ->toArray();
            // $chunks = array_chunk($tokens, 500);
            // foreach ($chunks as $index => $chunk) {
            //     $tokens = array_values($chunk);
            //     if (!empty($tokens)) {

            //         if(strtolower($item->category_id) != "88"){
            //             $arr = [
            //                 'title' => 'Wao Collection New Item Added 😊',
            //                 'body' => $item->name,
            //             ];    
            //         }else{
            //             $arr = [
            //                 'title' => 'Client Feedbacks 😍',
            //                 'body' => $item->name,
            //             ];
            //         }
            //         // $regIdChunk=array_chunk($FcmTokens3,900);
            //         // foreach($regIdChunk as $FcmToken){
            //         $output = Helper::sendWebNotification($arr, $tokens);
            //         // }
            //     }
            // }
        }


        $fcmTokens = \App\Models\UserToken::where('is_fcm_subscribe', 0)->latest()->limit(1000)->get();
        foreach($fcmTokens as $token){
            $output = Helper::subscribeToFcm([$token->device_token], 'add', 'products');
            $token->is_fcm_subscribe = 1;
            $token->save();
        }
        
        // foreach ($items as $item) {
        //     $minutes = $item->updated_at->diffInMinutes(Carbon::now());

        //     if ($item->increase_perMin == '0' || $item->increase_perMin == 0) {
               
        //     }else{
        //         $exact_minutes = intdiv($minutes, $item->increase_perMin);
        //         $new_minutes = ($item->soldAdm) + $exact_minutes;
        //         if ($new_minutes > $item->stop_fake_after_quantity) {
        //             // if new minutes are greater then our limit 
        //             $item->soldAdm = $item->stop_fake_after_quantity;
        //             $item->save();
        //         } else {
        //             // return $exact_minutes minutes added';
        //             $item->soldAdm = $new_minutes;
        //             $item->save();
        //         }
        //     }

        // }
    }
}
