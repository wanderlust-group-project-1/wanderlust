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
    ];
    public function getBooking(int $bookingId, int $guideId, int $customerId) {
        $q = new QueryBuilder();
        $q->setTable($this->table);
        $q->select('guide_booking.*')->where('booking_id', $bookingId)->where('guide_id', $guideId)->where('customer_id', $customerId);
        return $this->query($q->getQuery(), $q->getData());
    }   

    public function createBooking(array $data): bool {
        $q = "CALL GetGuideIdByPackageId(:package_id)";
        $params = [
            'package_id' => $data['package_id']
        ];
    
        // Execute the stored procedure to get the guide_id
        $guide = $this->query($q, $params, true);
    
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
                'transport_supply' => $data['transport_supply']
            ];
    
            // Insert the final data into the database
            return $this->insert($finaldata);
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
    
}