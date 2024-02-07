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
}