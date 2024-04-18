<?php 

class RentalService {
    use Controller;


    public function uploadImage(string $a = '', string $b = '', string $c = ''):void {

        $request = JSONRequest::createFromFormData();
        $response = new JSONResponse();

        $rental = new RentalServiceModel;

        $data = [
            'image' => $_FILES['image'],

        ];
        $id = UserMiddleware::getUser()['id'];

        $image = $rental->uploadImage($data, $id);



        // $response->statusCode(200)->data(['image' => $image['image']])->send();
        // response with image url success or fail
        if($image){
            $response->statusCode(200)->data(['image' => $image['image']])->send();
        }else{
            $response->statusCode(400)->data(['error' => 'Image upload failed'])->send();
        }
    }


}