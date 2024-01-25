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
        
        // TODO: Replace the hardcoded ID with a dynamic value
        $data["equipments"] = $equipment->getEquipmentsbyRentalService($dynamicId);


        $this->view('customer/components/items',$data);
    }

}