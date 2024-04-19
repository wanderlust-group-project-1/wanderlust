<?php

class Statistics
{
    use Controller;


    public function monthlyCompletedRentalCount(string $a = '', string $b = '', string $c = ''): void
    {

        $response = new JSONResponse;

        $rental = new RentalServiceModel;
        $data = [
            'rentalCount' => $rental->GetMonthlyCompletedRentalCount(UserMiddleware::getUser()['id']),
            'itemCount' => $rental->GetMonthlyRentedItemCount(UserMiddleware::getUser()['id'])
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
        $rentalCount = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $itemCount = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        foreach ($data['rentalCount'] as $key => $value) {
            $rentalCount[$value->Month - 1] = $value->Count;
        }

        foreach ($data['itemCount'] as $key => $value) {
            $itemCount[$value->Month - 1] = $value->ItemCount;
        }

        $data = [
            'months' => $months,
            'rentalCount' => $rentalCount,
            'itemCount' => $itemCount
        ];






        // show($data);
        $response->statusCode(200)->data($data)->send();
    }



    public function adminRental(string $a = '', string $b = '', string $c = ''): void
    {

        $response = new JSONResponse;

        $rental = new RentalServiceModel;
        $data = [
            'rentalCount' => $rental->GetAllMonthlyCompletedRentalCount(UserMiddleware::getUser()),
            'itemCount' => $rental->GetAllMonthlyRentedItemCount(UserMiddleware::getUser())
        ];

        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $rentalCount = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $itemCount = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        foreach ($data['rentalCount'] as $key => $value) {
            $rentalCount[$value->Month - 1] = $value->Count;
        }

        foreach ($data['itemCount'] as $key => $value) {
            $itemCount[$value->Month - 1] = $value->ItemCount;
        }

        $data = [
            'months' => $months,
            'rentalCount' => $rentalCount,
            'itemCount' => $itemCount
        ];






        // show($data);
        $response->statusCode(200)->data($data)->send();
    }
}
