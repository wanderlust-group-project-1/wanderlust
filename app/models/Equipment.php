<?php

class EquipmentModel {
    use Model;

    protected string $table = 'equipment';
    protected array $allowedColumns = [
        'rentalservice_id',
        'name',
        'cost',
        'description',
        'type',
        'count',
        'fee',
        'image',
        'standard_fee',
    ];

    public function createEquipment(JSONRequest $request, JSONResponse $response) {
        $data = $request->getAll();
        $files = $_FILES; // Assuming the files are sent as part of the request

        // show($data);
        if ($this->validateEquipment($data, $files)) {
            // Additional logic for creating equipment
            // For example, uploading documents, registering with a user, etc.
            // show($files);

            $data['image'] = upload($files['image'], 'images/equipment');

            // show(UserMiddleware::getUser());
            $data['rentalservice_id'] = UserMiddleware::getUser()['id'];
            // show($data['rentalservice_id']);

            $data = array_filter($data, function ($key) {
                return in_array($key, $this->allowedColumns);
            }, ARRAY_FILTER_USE_KEY);

            $id =  $this->insert($data);

            $item = new ItemModel();
            $item->createItems(['equipment_id' => $id, 'count' => $data['count']]);

            $response->success(true)

                ->message('Equipment created successfully')
                ->statusCode(201)
                ->send();
        } else {
            $response->success(false)
                ->data(['errors' => $this->errors])
                ->message('Validation failed')
                ->statusCode(422)
                ->send();
        }
    }

    public function validateEquipment(array $data, array $files) {
        // Validation logic for equipment attributes
        // Similar to the validateRentalService method

        return empty($this->errors);
    }

    public function updateEquipment(array $data, array $files, int $id) {


    //   if image in file
     if (isset($files['image']) && $files['image']['name'] != '') {
        $data['image'] = upload($files['image'], 'images/equipment');

    } else {
        unset($data['image']);
    }

    // show($data);
    // filter data
    $data = array_filter($data, function ($key) {
        return in_array($key, $this->allowedColumns);
    }, ARRAY_FILTER_USE_KEY);

    // show($data);


    return $this->update($id,$data);
    }

    public function deleteEquipment(int $id) {

       


        return $this->delete($id);
    }


    public function GetCurrentAcceptedRents(int $id) {
        $q = 'CALL GetCurrentAcceptedRents(:id)';
        return $this->query($q, ['id' => $id]);
    }

    public function increaseCount(int $id, int $count) {
        $q = 'CALL IncreaseEquipmentCount(:id, :count)';
        return $this->query($q, ['id' => $id, 'count' => $count]);


    }

    public function getEquipment(int $id): mixed {
        // Retrieval logic for equipment attributes
        // Similar to the getRentalService method

        $q = new QueryBuilder;
        $q->setTable('equipment');
        // Additional logic for joining tables if needed
        $q->where('equipment.id', $id);

        return $this->query($q->getQuery(), $q->getData());
    }


    public function getEquipmentsbyRentalService(int $id, int $status = 1): mixed {
        // Retrieval logic for equipment attributes
        // Similar to the getRentalService method

        $q = new QueryBuilder;
        $q->setTable('equipment');
        // Additional logic for joining tables if needed
        if ($status == 1) {
            $q->select('equipment.*')->where('equipment.rentalservice_id', $id)->where('equipment.count',  0,'>');
        } else {
            $q->select('equipment.*')->where('equipment.rentalservice_id', $id)->where('equipment.count',  0,'=');
        }
        // $q->select('equipment.*')->where('equipment.rentalservice_id', $id)->where('equipment.count',  0,'>');
        // show($q->getQuery());
        return $this->query($q->getQuery(), $q->getData());

    }

    // Additional methods for EquipmentModel if needed

    public function getEquipmentbyRentalService(int $id, int $equipment_id): mixed {
        // Retrieval logic for equipment attributes
        // Similar to the getRentalService method

        $q = new QueryBuilder;
        $q->setTable('equipment');
        // Additional logic for joining tables if needed
        $q->select('equipment.*');
        $q->where('equipment.rentalservice_id', $id)->where('equipment.id', $equipment_id);
        // show($q->getQuery());
        return $this->query($q->getQuery(), $q->getData());
    }


    

    public function getEquipmentWithRentalService(int $id): mixed {
        // Retrieval logic for equipment attributes
        // Similar to the getRentalService method

        $q = new QueryBuilder;
        $q->setTable('equipment');
        // Additional logic for joining tables if needed
        $q->select('equipment.* , rental_services.name as rentalservice_name, rental_services.image as rentalservice_image');
        $q->join('rental_services', 'equipment.rentalservice_id', 'rental_services.id');

        $q->where('equipment.id', $id);
        // show($q->getQuery());
        // show($this->query($q->getQuery(), $q->getData()));
        return $this->query($q->getQuery(), $q->getData())[0];
    }

    // Customer Functions

    function getEquipments($data){

        $q = new QueryBuilder;
        

    }


    function rentalServiceEquipments($data){

        $q = 'CALL GetAvailableEquipmentByRental(:rentalservice_id, :start_date, :end_date)';

        return $this->query($q, $data);

        


    }


    function getItemsByEquipment($data){
            
            // $q = new QueryBuilder;
            // $q->setTable('item');
            // $q->select('item.*', 'equipment.name as equipment_name', 'equipment.image as equipment_image');
            // $q->join('equipment', 'item.equipment_id', 'equipment.id');
            // $q->where('item.equipment_id', $data['equipment_id']);
            // // show($q->getQuery());
            // return $this->query($q->getQuery(), $q->getData());

            $q = 'CALL GetItemsByEquipment(:equipment_id)';
            return $this->query($q, $data);

    }


}





