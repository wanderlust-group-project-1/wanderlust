<?php 

Class Rent{
    use Controller;


    public function index(string $a = '', string $b = '', string $c = ''):void {
        $this->view('customer/rent');
    }


    public function search(string $a = '', string $b = '', string $c = ''):void {

        $request = new JSONRequest();
        $response = new JSONResponse();

        


        $this->view('customer/search');
    }

    public function items(string $a = '', string $b = '', string $c = ''):void {

        $request = new JSONRequest();
        
        // $equipment = new EquipmentModel;
        
        // TODO: Replace the hardcoded ID with a dynamic value
        // $data["equipments"] = $equipment->getEquipmentsbyRentalService(25);

        $cart = new CartModel; 
        $cart = $cart->first(['customer_id' => UserMiddleware::getUser()['id']]);

        if (!$cart) {
            $response = new JSONResponse();
            $response->success(false)
                ->message('Cart not found')
                ->statusCode(404)
                ->send();
            return;
        }

        $rent = new RentModel;

        $data['cart'] = $cart;
        $data['request'] = $request->getAll();

        $data['equipments'] = $rent->getItems($request->getAll());

        // show($data['equipments']);

        $this->view('customer/components/items',$data);
    }

    public function shop(string $a = '', string $b = '', string $c = ''):void {

        $rentalserviceId = $a;

        $rent = new RentModel;
        $data = [
            'rentalservice_id' => $rentalserviceId,
        ];

        




    }

}