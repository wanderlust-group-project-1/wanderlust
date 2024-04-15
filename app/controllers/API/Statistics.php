<?php

class Statistics {
    use Controller;

   
    public function monthlyCompletedRentalCount(string $a = '', string $b = '', string $c = ''): void
    {

        $response = new JSONResponse;

        $rental = new RentalServiceModel;
        $data = [
            'chart' => $rental->GetMonthlyCompletedRentalCount(UserMiddleware::getUser()['id'])
        ];



        // [chart] => Array
        // (
        //     [0] => stdClass Object
        //         (
        //             [Month] => 2
        //             [Count] => 2
        //         )

        //     [1] => stdClass Object
        //         (
        //             [Month] => 4
        //             [Count] => 2
        //         )

        // )


        //  to  {months:[],count:[]}} / use all months, if no data for a month, set count to 0 ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]

        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $count = [0,0,0,0,0,0,0,0,0,0,0,0];

        foreach ($data['chart'] as $key => $value) {
            $count[$value->Month-1] = $value->Count;
        }

        $data = [
            'months' => $months,
            'count' => $count
        ];
      





        // show($data);
        $response->statusCode(200)->data($data)->send();


        
    }
    
}