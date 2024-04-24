<?php

class GuideBookings{
    use Controller;
    public function index(string $a = '', string $b = '', string $c = ''): void {
        $this->view('guide/guidebooking');
    }
    public function bookRequest(string $a = '', string $b = '', string $c = ''): void {
        $request  = new JSONRequest;
        $response = new JSONResponse;

        $data = $request->getAll();

        $GuideBookingsModel = new GuideBookingsModel;
        $booking = $GuideBookingsModel->createBooking($data);

        if ($booking) {
            $response->success(true)
                ->message('Booking created successfully')
                ->statusCode(200)
                ->send();
            exit();
        } else {
            $response->success(false)
                ->data(['errors' => $GuideBookingsModel->errors])
                ->message('Validation failed')
                ->statusCode(422)
                ->send();
        }
    }
    public function getDays(string $a = '', string $b = '', string $c = ''): void {
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
    
    public function getBookingDetailsByDate($date): void {
        $response = new JSONResponse;
    
        // Call your method to retrieve booking details based on the date
        $GuideBookingsModel = new GuideBookingsModel();
        $bookingDetails = $GuideBookingsModel->getBookingDetailsByDate(UserMiddleware::getUser()['id'],$date);
    
        $response->success(true)
            ->data($bookingDetails)
            ->message('Booking details fetched successfully')
            ->statusCode(200)
            ->send();
    
    }

    public function deleteBooking($date): void {
        $response = new JSONResponse;

        $GuideBookingsModel = new GuideBookingsModel;

        $GuideBookingsModel->deleteBooking(UserMiddleware::getUser()['id'],$date);

        $response->success(true)
            ->message('Booking deleted successfully')
            ->statusCode(200)
            ->send();

    }

}