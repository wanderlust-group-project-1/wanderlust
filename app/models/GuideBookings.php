<?php

class GuideBookingsModel{
    use Model;
    protected string $table = 'guide_booking';

    protected array $allowedColumns = [
        'guide_id',
        'customer_id',
        'package_id',
        'created_at',
        'date',
        'no_of_people',
        'location',
        'transport_supply',
        'payment_id'
    ];
    public function getBooking(int $bookingId, int $guideId, int $customerId) {
        $q = new QueryBuilder();
        $q->setTable($this->table);
        $q->select('guide_booking.*')->where('booking_id', $bookingId)->where('guide_id', $guideId)->where('customer_id', $customerId);
        return $this->query($q->getQuery(), $q->getData());
    }   


    public function createBooking(array $data): mixed {
        $q = "CALL CreatePaymentForGuide(:package_id)";
        $params1 = [
            'package_id' => $data['package_id']
        ];
        $payment = $this->query($q, $params1, true);

        $q = "CALL GetGuideIdByPackageId(:package_id)";
        $params2 = [
            'package_id' => $data['package_id']
        ];
    
        // Execute the stored procedure to get the guide_id
        $guide = $this->query($q, $params2, true);
    
        // Check if guide data is retrieved successfully
        if ($guide) {
            // Prepare the final data for insertion
            $finaldata = [
                'guide_id' => $guide[0]->guide_id,
                'customer_id' => $_SESSION['USER']->id,
                'package_id' => $data['package_id'],
                'created_at' => date('Y-m-d H:i:s'),
                'date' => $data['date'],
                'no_of_people' => $data['no_of_people'],
                'location' => $data['location'],
                'transport_supply' => $data['transport_supply'],
                'payment_id' => $payment[0]->payment_id
            ];
    
            // Insert the final data into the database
            $this->insert($finaldata);
            return $payment;
        } else {
            // Handle error if guide data is not retrieved
            // For example, return false or throw an exception
            return false;
        }

    }

    public function getDaysByMonth(int $guideId, array $data): mixed {
        $q = new QueryBuilder();
        $q = "CALL RetrieveDaysByGuideIdMonthYear(:guideId, :currentMonth, :currentYear)";
        $params = [
            'guideId' => $guideId,
            'currentMonth' => $data['currentMonth'],
            'currentYear' => $data['currentYear']
        ];
        return $this->query($q, $params);
    }

    public function getBookingDetailsByDate(int $guideId, string $date): mixed {
        $q = new QueryBuilder();
        $q->setTable($this->table);
        $q->select('guide_booking.*')->where('guide_id', $guideId)->where('date', $date);
        // show($q);
        return $this->query($q->getQuery(), $q->getData())[0];
    }

    public function getCustomerDetailsByBookingID(int $bookingId): mixed {
        $q = new QueryBuilder();
        $q->setTable('customers');
        $q->select('customers.*')->join('guide_booking', 'customers.id', 'guide_booking.customer_id')->where('guide_booking.id', $bookingId);
        return $this->query($q->getQuery(), $q->getData())[0];
    }

    public function getAllBookings(int $userId): mixed {

        $userId = $_SESSION['USER']->id;
        $q = new QueryBuilder();
        $q->setTable($this->table);
        $q->select('guide_booking.*')->where('guide_id', $userId);
        return $this->query($q->getQuery(), $q->getData());
    }

    public function getGuideIdByPackageId(int $packageId): mixed {
        $q = "CALL GetGuideIdByPackageId(:package_id)";
        $params = [
            'package_id' => $packageId
        ];
        return $this->query($q, $params, true);
    }

    public function getGuideAllBookings(int $guideId): mixed {
        $q = new QueryBuilder();
        $q->setTable($this->table);
        $q->select('guide_booking.*')->where('guide_id', $guideId);
        return $this->query($q->getQuery(), $q->getData());
    }

    public function deleteBooking(int $guideId, string $date): mixed {
      
        //return $this->delete(['guide_id' => $guideId, 'date' => $date]);
        $q = new QueryBuilder();
        $q->setTable($this->table);
        $q->delete()->where('guide_id', $guideId)->where('date', $date);
        return $this->query($q->getQuery(), $q->getData());

        
    }
    
    public function completeBooking(int $guideId, string $date): mixed {
        $q = new QueryBuilder();
        $q->setTable($this->table);
        $q->update(['status' => 'completed'])->where('guide_id', $guideId)->where('date', $date);
        return $this->query($q->getQuery(), $q->getData());
    }
}