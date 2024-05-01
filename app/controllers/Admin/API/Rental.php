<?php

class Rental {
    use Controller;


    public function updateStatus():void {
        $request = new JSONRequest;
        $response = new JSONResponse;

        $rental = new RentalServiceModel;
        // echo "success";
        // show($request->getAll());

        $rental->updateStatus($request, $response);
    }

}

?>
