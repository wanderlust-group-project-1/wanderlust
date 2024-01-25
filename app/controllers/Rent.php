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
        
        $equipment = new EquipmentModel;
        
        $data["equipments"] = $equipment->getEquipmentsbyRentalService(25);


        $this->view('customer/components/items',$data);
    }

}