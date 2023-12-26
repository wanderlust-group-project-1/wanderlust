<?php

class Equipment {
    use Controller;

    public function addEquipment(string $a = '', string $b = '', string $c = ''): void {
        
        $request = JSONRequest::createFromFormData();
        $response = new JSONResponse;

        $equipmentModel = new EquipmentModel;

        $equipmentModel->createEquipment($request, $response);


    }
}