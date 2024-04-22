<?php 

Class Rent{
    use Controller;


    public function index(string $a = '', string $b = '', string $c = ''):void {


        AuthorizationMiddleware::authorize(['customer']);

        $cart = new CartModel;
        $cart = $cart->first(['customer_id' => UserMiddleware::getUser()['id']]);
        // show($cart);
        $data = [];
        if($cart){
            $data['cart'] = $cart;
        }

        $this->view('customer/rent', $data);
    }


    public function search(string $a = '', string $b = '', string $c = ''):void {

        

        $request = new JSONRequest();
        $response = new JSONResponse();

        


        $this->view('customer/search');
    }

    public function items(string $a = '', string $b = '', string $c = ''):void {



        AuthorizationMiddleware::authorize(['customer']);

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

//         show($data['equipments']);




//  calculate the total fee for each , standard fee + fee * days difference (end_date - start_date)
        foreach ($data['equipments'] as $equipment) {
            $equipment->total = $equipment->standard_fee + $equipment->fee * (strtotime($data['cart']->end_date) - strtotime($data['cart']->start_date)) / (60 * 60 * 24);
        }

        // show($data['equipments']);




        // show($data['equipments']);

        $this->view('customer/components/items',$data);
    }


    public function item(string $a = '', string $b = '', string $c = ''):void {


        AuthorizationMiddleware::authorize(['customer']);


        $cart = new CartModel; 
        $cart = $cart->first(['customer_id' => UserMiddleware::getUser()['id']]);




        $equipment = new EquipmentModel;
        $data = [
            // 'equipment' => $equipment->first(['id' => $a]),
            'equipment' => $equipment->getEquipmentWithRentalService($a)
        ];
        // show($data['equipment']);

        // foreach ($data['equipment'] as $equipment) {
        //     $equipment->total = $equipment->standard_fee + $equipment->fee * (strtotime($cart->end_date) - strtotime($cart->start_date)) / (60 * 60 * 24);
        // }

        $data['equipment']->total = $data['equipment']->standard_fee + $data['equipment']->fee * (strtotime($cart->end_date) - strtotime($cart->start_date)) / (60 * 60 * 24);

        $this->view('customer/components/item', $data);
    }


    public function shop(string $a = '', string $b = '', string $c = ''):void {

        $rentalserviceId = $a;

        $rent = new RentModel;
        $data = [
            'rentalservice_id' => $rentalserviceId,
        ];

        




    }

}