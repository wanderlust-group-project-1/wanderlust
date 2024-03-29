<?php

class RentalService {
    use Controller;


    public function update(string $a = '', string $b = '', string $c = ''):void {

        $rentalservice = new rentalserviceModel();
        $rentalservice->updateRentalservice($_POST);

        redirect('dashboard');
        // $this->view('customer/profile');
    }

    public function getEquipments(string $a = '', string $b = '', string $c = ''):void {

        $equipment = new EquipmentModel();

        $data["equipments"] = $equipment->getEquipmentsbyRentalService(UserMiddleware::getUser()['id']);
        // show($data["equipments"]);
        $this->view('rental/components/equipmentlist', $data);
    }

    public function getEquipment(string $a = '', string $b = '', string $c = ''):void {

        $equipment = new EquipmentModel();

        $data["equipment"] = $equipment->getEquipmentbyRentalService(UserMiddleware::getUser()['id'], $a);
        // show($data["equipment"]);
        $this->view('rental/components/equipment', $data);
    }


    public function getItems(string $a = '', string $b = '', string $c = ''):void {

        $equipment = new EquipmentModel();

        $data["items"] = $equipment->getItemsByEquipment(['equipment_id' => $a]);
        // show($data["items"]);
        $this->view('rental/components/items', $data);
        


 
    }



    // Customer
    public function index(string $a = '', string $b = '', string $c = ''):void {

        // show($a);
        $rental = new RentalServiceModel;
        $data = [
            'rental' => $rental->getRentalService($a)[0],
        ];

        // show($data);



        $this->view('customer/rentalservice', $data);
    }

    public function availableEquipments(string $a = '', string $b = '', string $c = ''):void {

        $equipment = new EquipmentModel;

        // get cart end start time 
        $cart = new CartModel;
        $cart = $cart->first(['customer_id' => UserMiddleware::getUser()['id']]);
        // show($cart);
        $data = [
            'start_date' => $cart->start_date,
            'end_date' => $cart->end_date,
            'rentalservice_id' => $a,
        ];

        $data["equipments"] = $equipment->rentalServiceEquipments($data);

        // show($data);



        
        $this->view('customer/components/items',$data);
    }
}