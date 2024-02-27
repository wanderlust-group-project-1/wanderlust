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

//         show($data['equipments']);

//         Array
// (
//     [0] => stdClass Object
//         (
//             [id] => 25
//             [rentalservice_id] => 25
//             [name] => Tent - 2 Person
//             [cost] => 3000.00
//             [description] => Tent for 2 Persons
//             [type] => Tent
//             [count] => 31
//             [fee] => 1000.00
//             [standard_fee] => 0.00
//             [image] => 65b365fccf6dc.jpg
//             [rental_service_name] => ABC Rent
//         )

//     [1] => stdClass Object
//         (

            // show($data['cart']);

//             stdClass Object
// (
//     [id] => 71
//     [customer_id] => 32
//     [start_date] => 2024-02-14
//     [end_date] => 2024-02-29
// )

//         )

//  calculate the total fee for each , standard fee + fee * days difference (end_date - start_date)
        foreach ($data['equipments'] as $equipment) {
            $equipment->total = $equipment->standard_fee + $equipment->fee * (strtotime($data['cart']->end_date) - strtotime($data['cart']->start_date)) / (60 * 60 * 24);
        }

        // show($data['equipments']);




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