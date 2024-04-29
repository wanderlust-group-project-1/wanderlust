<?php

class Package
{
    use Controller;

    public function addPackage(string $a = '', string $b = '', string $c = ''): void
    {

        $request = new JSONRequest;
        $response = new JSONResponse;

        $data = $request->getAll();
        $data['guide_id'] = UserMiddleware::getUser()['id'];

        $packageModel = new PackageModel;
        $packageModel->createPackage($data);

        $response->success(true)
            ->message('Package created successfully')
            ->statusCode(200)
            ->send();
    }


    public function deletePackage(string $a = '', string $b = '', string $c = ''): void
    {
        $request = JSONRequest::createFromFormData();
        $response = new JSONResponse;

        $packageModel = new PackageModel;

        $data = $packageModel->deletePackage($a);

        if (!$data) {
            $response->success(true)
                ->message('Package deleted successfully')
                ->statusCode(200)
                ->send();
        } else {
            $response->success(false)
                ->data(['errors' => $packageModel->errors])
                ->message('Validation failed')
                ->statusCode(422)
                ->send();
        }
    }

    public function update(string $a = '', string $b = '', string $c = ''): void
    {
        $request = JSONRequest::createFromFormData();
        $response = new JSONResponse;

        $packageModel = new PackageModel;
;
        $data = $packageModel->updatePackage($request->getAll(), $a);

        if (!$data) {
            $response->success(true)
                ->message('Package updated successfully')
                ->statusCode(200)
                ->send();
        } else {
            $response->success(false)
                ->data(['errors' => $packageModel->errors])
                ->message('Validation failed')
                ->statusCode(422)
                ->send();
        }
    }
}
