<?php

class Package {
    use Controller;

    public function addPackage(string $a = '', string $b = '', string $c = ''): void {
        
        $request = JSONRequest::createFromFormData();
        $response = new JSONResponse;

        $packageModel = new PackageModel;

        $packageModel->createPackage($request, $response);
    }
}