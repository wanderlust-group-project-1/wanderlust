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
    ];

    public function createEquipment(JSONRequest $request, JSONResponse $response) {
        $data = $request->getAll();
        $files = $_FILES; // Assuming the files are sent as part of the request

        if ($this->validateEquipment($data, $files)) {
            // Additional logic for creating equipment
            // For example, uploading documents, registering with a user, etc.

            $data['image'] = upload($files['image'], 'images/equipment');

            // show(UserMiddleware::getUser());
            $data['rentalservice_id'] = UserMiddleware::getUser()['id'];
            // show($data['rentalservice_id']);

            $data = array_filter($data, function ($key) {
                return in_array($key, $this->allowedColumns);
            }, ARRAY_FILTER_USE_KEY);

            $this->insert($data);

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

    public function updateEquipment(array $data) {
        // Update logic for equipment attributes
        // Similar to the updateRentalservice method
        // Make sure to filter $data based on $this->allowedColumns

        return $this->update($data['id'], $data, 'id');
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


    public function getEquipmentbyRentalService(int $id): mixed {
        // Retrieval logic for equipment attributes
        // Similar to the getRentalService method

        $q = new QueryBuilder;
        $q->setTable('equipment');
        // Additional logic for joining tables if needed
        $q->select('equipment.*')->where('equipment.rentalservice_id', $id);
        // show($q->getQuery());
        return $this->query($q->getQuery(), $q->getData());
    }

    // Additional methods for EquipmentModel if needed

}
