    public function updatePackage($id, $price, $max_group_size, $max_distance, $transport_needed, $places) {
    <?php

    class Package
    {
        use Controller;

        public function addPackage(string $a = '', string $b = '', string $c = ''): void
        {

            $request = JSONRequest::createFromFormData();
            $response = new JSONResponse;

            $packageModel = new PackageModel;

            $packageModel->createPackage($request, $response);
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
