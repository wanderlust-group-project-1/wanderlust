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
}