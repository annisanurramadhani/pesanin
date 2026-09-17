<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MidtransPayoutService
{

    public function process($withdrawal)
    {

        if(
            !config('services.midtrans.payout_enabled')
        ){

            return $this->sandboxSimulation(
                $withdrawal
            );

        }


        return $this->sendToMidtrans(
            $withdrawal
        );

    }



    private function sandboxSimulation($withdrawal)
    {

        return [

            'success'=>true,

            'status'=>'paid',

            'payout_id'=>
            'SANDBOX-'.time(),

            'response'=>[

                'message'=>
                'Sandbox payout simulation'

            ]

        ];

    }



    private function sendToMidtrans($withdrawal)
    {


        $response = Http::withHeaders([

            'Authorization'=>
            'Basic '
            .
            base64_encode(
                config('services.midtrans.payout_key')
                . ':'
            ),

            'Content-Type'=>
            'application/json'

        ])
        ->post(

            config('services.midtrans.payout_url'),

            [

                'payouts'=>[

                    [

                        'beneficiary_name'=>
                        $withdrawal
                        ->bankAccount
                        ->account_name,


                        'beneficiary_account'=>
                        $withdrawal
                        ->bankAccount
                        ->account_number,


                        'beneficiary_bank'=>
                        strtolower(
                            $withdrawal
                            ->bankAccount
                            ->bank_name
                        ),


                        'amount'=>
                        $withdrawal
                        ->amount,


                        'notes'=>
                        'Withdrawal PesanIn'

                    ]

                ]

            ]

        );


        return [

            'success'=>
            $response->successful(),


            'status'=>
            $response->successful()
            ?
            'processing'
            :
            'failed',


            'payout_id'=>
            $response['payouts'][0]['payout_id']
            ??
            null,


            'response'=>
            $response->json()

        ];

    }

}