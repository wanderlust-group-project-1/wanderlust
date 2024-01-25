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
        'payment_id'
    ];

    public function createRent(JSONRequest $request, JSONResponse $response) {
        $data = $request->getAll();

        if ($this->validateRent($data)) {
            // Additional logic for creating rent
            // For example, uploading documents, registering with a user, etc.

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
        // Validation logic for rent attributes
        // Similar to the validateRentalService method

        return empty($this->errors);
    }



}