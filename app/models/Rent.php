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
            // if   $data['search']
           ->where('equipment.name', "%{$data['search']}%" , 'LIKE');


           return $this->query($q->getQuery(),$q->getData());

        

            // ->join('rent', 'equipment.id', 'rent.equipment_id')


    }   



}