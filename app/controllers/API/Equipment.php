<?php

class Equipment {
    use Controller;

    public function addEquipment(string $a = '', string $b = '', string $c = ''): void {
        
        $request = JSONRequest::createFromFormData();
        $response = new JSONResponse;

        $equipmentModel = new EquipmentModel;

        $equipmentModel->createEquipment($request, $response);


    }
    public function update(string $a = '', string $b = '', string $c = ''): void {
        $request = JSONRequest::createFromFormData();
        $response = new JSONResponse;

        $equipmentModel = new EquipmentModel;

        $data = $equipmentModel->updateEquipment($request->getAll(), $_FILES,$a);

        if (!$data) {
            $response->success(true)
                ->message('Equipment updated successfully')
                ->statusCode(200)
                ->send();
        } else {
            $response->success(false)
                ->data(['errors' => $equipmentModel->errors])
                ->message('Validation failed')
                ->statusCode(422)
                ->send();
        }



    }

    public function delete(string $a = '', string $b = '', string $c = ''): void {
        $request = JSONRequest::createFromFormData();
        $response = new JSONResponse;

        $equipmentModel = new EquipmentModel;

        if($equipmentModel->GetCurrentAcceptedRents($a)){
            $response->success(false)
                ->message('Equipment is currently rented out')
                ->statusCode(422)
                ->send();

                return;
        }
            

        $data = $equipmentModel->deleteEquipment($a);
      
        if (!$data) {
            $response->success(true)
                ->message('Equipment deleted successfully')
                ->statusCode(200)
                ->send();
        } else {
            $response->success(false)
                ->data(['errors' => $equipmentModel->errors])
                ->message('Validation failed')
                ->statusCode(422)
                ->send();
        }
    }

    public function increasecount(string $a = '', string $b = '', string $c = ''): void {
        $request = new JSONRequest;
        $response = new JSONResponse;

        $equipmentModel = new EquipmentModel;

        // show($request->getAll());

        $data = $equipmentModel->increaseCount($a, $request->get('count'));

        if (!$data){
            $response->success(true)
                ->message('Equipment count increased successfully')
                ->statusCode(200)
                ->send();
        } else {
            $response->success(false)
                ->data(['errors' => $equipmentModel->errors])
                ->message('Validation failed')
                ->statusCode(422)
                ->send();
        }


    }

    public function disableEquipment(string $a = '', string $b = '', string $c = ''): void {
        $request = new JSONRequest;
        $response = new JSONResponse;

       $item = new ItemModel;

        $data = $item->makeUnavailableByEquipment($a);

        if (!$data){
            $response->success(true)
                ->message('Equipment disabled successfully')
                ->statusCode(200)
                ->send();
        } else {
            $response->success(false)
                ->data(['errors' => $item->errors])
                ->message('Failed to disable equipment')
                ->statusCode(422)
                ->send();
        }
    }
}