<?php 

class Item {
    use Controller;

    public function getItems(string $a = '', string $b = '', string $c = ''):void {

        $equipment = new EquipmentModel();

        $data["items"] = $equipment->getItemsByEquipment(['equipment_id' => $a]);
        // show($data["items"]);
        $this->view('rental/components/items', $data);
        

    }

    public function makeunavailabletemporarily(string $a = '', string $b = '', string $c = ''):void {
        $response = new JSONResponse;
        $item = new ItemModel();
        $data =  $item->makeunavailabletemporarily($a);

        if ($data) {
            $response->success(true)
                ->message('Item made unavailable temporarily')
                ->data($data)
                ->statusCode(200)
                ->send();
        } else {
            $response->success(false)
                ->data(['errors' => $item->errors])
                ->message('Validation failed')
                ->statusCode(422)
                ->send();
        }


    }

    public function makeavailable(string $a = '', string $b = '', string $c = ''):void {
        $response = new JSONResponse;
        $item = new ItemModel();
        $data =  $item->makeavailable($a);

        if ($data) {
            $response->success(true)
                ->message('Item made available')
                ->data($data)
                ->statusCode(200)
                ->send();
        } else {
            $response->success(false)
                ->data(['errors' => $item->errors])
                ->message('Validation failed')
                ->statusCode(422)
                ->send();
        }

    }

    public function makeunavailablepermanently(string $a = '', string $b = '', string $c = ''):void {
        $response = new JSONResponse;
        $item = new ItemModel();
        $data =  $item->makeunavailablepermanently($a);

        if ($data) {
            $response->success(true)
                ->message('Item made unavailable permanently')
                ->data($data)
                ->statusCode(200)
                ->send();
        } else {
            $response->success(false)
                ->data(['errors' => $item->errors])
                ->message('Validation failed')
                ->statusCode(422)
                ->send();
        }

    }

}

?>