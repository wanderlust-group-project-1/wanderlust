<?php

class GuideBookings
{
    use Controller;
    public function index(string $a = '', string $b = '', string $c = ''): void
    {
        $this->view('guide/guidebooking');
    }
    public function bookRequest(string $a = '', string $b = '', string $c = ''): void
    {
        $request  = new JSONRequest;
        $response = new JSONResponse;

        $data = $request->getAll();

        $GuideBookingsModel = new GuideBookingsModel;
        $booking = $GuideBookingsModel->createBooking($data);


        $merchant_id = MERCHANT_ID;
        $merchant_secret = MERCHANT_SECRET;

        $hash = strtoupper(
            md5(
                $merchant_id .
                    $booking[0]->bookingID .
                    // number_format($order->totalAmount, 2, '.', '') .
                    $booking[0]->amount .
                    'LKR' .
                    strtoupper(md5($merchant_secret))
            )
        );

        $data['hash'] = $hash;
        $data['merchant_id'] = $merchant_id;
        $data['orderId'] = $booking[0]->bookingID;
        $data['amount'] = $booking[0]->amount;

        $response->success(true)->data($data)->statusCode(200)->send();

        // if ($booking) {
        //     $response->success(true)
        //         ->message('Booking created successfully')
        //         ->statusCode(200)
        //         ->send();
        //     exit();
        // } else {
        //     $response->success(false)
        //         ->data(['errors' => $GuideBookingsModel->errors])
        //         ->message('Validation failed')
        //         ->statusCode(422)
        //         ->send();
        // }
    }

    public function getDays(string $a = '', string $b = '', string $c = ''): void
    {
        $request  = new JSONRequest;
        $response = new JSONResponse;

        $data = $request->getAll();

        $GuideBookingsModel = new GuideBookingsModel;
        $schedules = $GuideBookingsModel->getDaysByMonth(UserMiddleware::getUser()['id'], $data);

        $response->success(true)
            ->data($schedules)
            ->message('Schedule fetched successfully')
            ->statusCode(200)
            ->send();
    }

    public function getBookingDetailsByDate($date): void
    {
        $response = new JSONResponse;

        // Call your method to retrieve booking details based on the date
        $GuideBookingsModel = new GuideBookingsModel();
        $bookingDetails = $GuideBookingsModel->getBookingDetailsByDate(UserMiddleware::getUser()['id'], $date);
        if (!empty($bookingDetails)) {
            $userDetails = $GuideBookingsModel->getCustomerDetailsByBookingID($bookingDetails->id);

            $response->success(true)
                ->data([
                    'bookingDetails' => $bookingDetails,
                    'userDetails' => $userDetails
                ])
                ->message('Booking details fetched successfully')
                ->statusCode(200)
                ->send();
        } else {
            // If no booking details found, send an empty response
            $response->success(false)
                ->message('No booking details found for the given date')
                ->statusCode(404)
                ->send();
        }
    }

    public function getAllBookings(): void
    {
        $response = new JSONResponse;

        $GuideBookingsModel = new GuideBookingsModel();
        $bookings = $GuideBookingsModel->getAllBookings(UserMiddleware::getUser()['id']);
        $response->success(true)
            ->data($bookings)
            ->message('Bookings fetched successfully')
            ->statusCode(200)
            ->send();
    }

    public function getGuideAllBookings(int $packageId): void{
        $response = new JSONResponse;
        $GuideBookingsModel = new GuideBookingsModel();
        $guide = $GuideBookingsModel->getGuideIdByPackageId($packageId);
        $bookings = $GuideBookingsModel->getGuideAllBookings($guide[0]->guide_id);
        // show($bookings);

        $response->success(true)
            ->data($bookings)
            ->message('Bookings fetched successfully')
            ->statusCode(200)
            ->send();

        
    }

    public function getAllMyBookings(): void
    {
        $response = new JSONResponse;

        $GuideBookingsModel = new GuideBookingsModel();
        $bookingDetails = $GuideBookingsModel->getAllMyBookings(UserMiddleware::getUser()['id']);
        $guideDetails = $GuideBookingsModel->getGuideDetailsByBookingID($bookingDetails[0]->id);
        $response->success(true)
                ->data([
                    'bookingDetails' => $bookingDetails,
                    'guideDetails' => $guideDetails
                ])
                ->message('Booking details fetched successfully')
                ->statusCode(200)
                ->send();
    }
    public function deleteBooking($date): void
    {
        $response = new JSONResponse;

        $GuideBookingsModel = new GuideBookingsModel;

        $GuideBookingsModel->deleteBooking(UserMiddleware::getUser()['id'], $date);

        $response->success(true)
            ->message('Booking deleted successfully')
            ->statusCode(200)
            ->send();
    }

    public function completeBooking($date): void
    {
        $response = new JSONResponse;

        $GuideBookingsModel = new GuideBookingsModel;

        $GuideBookingsModel->completeBooking(UserMiddleware::getUser()['id'], $date);

        $response->success(true)
            ->message('Booking completed successfully')
            ->statusCode(200)
            ->send();
    }
}
