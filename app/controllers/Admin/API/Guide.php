<?php

class Guide {
    use Controller;


    public function updateStatus():void {
        $request = new JSONRequest;
        $response = new JSONResponse;

        $rental = new GuideModel;
        // echo "success";
        // show($request->getAll());

        $rental->updateStatus($request, $response);
    }

    
}

?>
