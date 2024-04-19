<?php 

class RentModel {

    use Model;

    protected string $table = 'rent';
    protected array $allowedColumns = [
        'customer_id',
        'equipment_id',
        'rental_service_id',
        'start_date',
        'end_date',
        'status',
        'total',
        'payment_method',
        'payment_status',
    ];

    public function createRent(JSONRequest $request, JSONResponse $response) {
        $data = $request->getAll();

        if ($this->validateRent($data)) {
          
            // $data['image'] = upload($files['image'], 'images/equipment');

            // show(UserMiddleware::getUser());
            $data['customer_id'] = UserMiddleware::getUser()['id'];
            // show($data['customer_id']);

            $data = array_filter($data, function ($key) {
                return in_array($key, $this->allowedColumns);
            }, ARRAY_FILTER_USE_KEY);

             
             return $this->insert($data);

            
        } else {
           
            return false;
        }
    }

    public function validateRent(array $data) {
        

        return empty($this->errors);
    }


    public function getItems(array $data) {

        $q = new QueryBuilder();

        $q->setTable('equipment');
        $q->select('equipment.*, rental_services.name As rental_service_name')
            ->join('rental_services', 'equipment.rentalservice_id', 'rental_services.id')
            ->join('locations', 'rental_services.location_id', 'locations.id')

            // if   $data['search']
           ->where('equipment.name', "%{$data['search']}%" , 'LIKE');


        //    order by location langitude and latitude
        //$data['latitude'] and $data['longitude']
        // ->append("ORDER BY ABS(locations.latitude - ?) + ABS(locations.longitude - ?) ASC")
        // ->addData([$data['latitude'], $data['longitude']]);

        if (isset($data['latitude']) && isset($data['longitude'])) {
            $q->append("ORDER BY ABS(locations.latitude - ?) + ABS(locations.longitude - ?) ASC")
                ->addData([$data['latitude'], $data['longitude']]);
        }

           

            // show($q->getQuery());


           return $this->query($q->getQuery(),$q->getData());

        

            // ->join('rent', 'equipment.id', 'rent.equipment_id')


    }   


    public function getRentalsByCustomer($data) {

        $q = 'CALL getRentalsByCustomer(:customer_id,:status)';

        // show ($data);
        return $this->query($q, $data);
    }

    

    public function getRentalsByRentalService($data) {

        $q = 'CALL GetFilteredPaidOrders(:rentalservice_id,:status)';

        // show ($data);
        return $this->query($q, $data);
    }


    public function getRental(int $id): mixed {
       
        $q = 'CALL getRentalDetailsById(:id)';
        return $this->query($q, ['id' => $id])[0];


    }

    public function getItemListbyRentId(int $id): mixed {
       
        $q = 'CALL GetItemListbyRentID(:id)';
        return $this->query($q, ['id' => $id]);

    }



}