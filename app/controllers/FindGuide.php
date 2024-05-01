<?php

class FindGuide{
    use Controller;
    public function index(string $a = '', string $b = '', string $c = ''): void {
        AuthorizationMiddleware::authorize(['customer']);
        $this->view('customer/findGuide');
    }
    
    public function search(string $a = '', string $b = '', string $c = ''): void {
        $request  = new JSONRequest;
        $response = new JSONResponse;

        $data = $request->getAll();
        
        $FindGuideModel = new FindGuideModel;
        $guides = $FindGuideModel->getSuitableGuides($data);

        $guideIds = array_column($guides, 'guide_id');
        // show($guideIds);

        // show($guides);;

        $this->view('customer/components/AllGuides', ['guides' => $guides]);

       
    }

    public function viewGuide(string $a = '', string $b = '', string $c = ''): void {
        $FindGuideModel = new FindGuideModel;
        $guide = $FindGuideModel->getGuideDetails($a);
    
        // Check if $b is not empty before calling getGuidePackages
        if (!empty($b)) {
            $packageIds = explode(',', $b); // Split $b into an array of package IDs
            foreach ($packageIds as $packageId) {
                $packages[] = $FindGuideModel->getGuidePackages($packageId);
            }
        } else {
            $packages = []; // Initialize empty array if $b is empty
        }
    
        $this->view('customer/OneGuide', ['guide' => $guide, 'packages' => $packages]);
    }
    
    public function viewGuidePackage(string $a = '', string $b = '', string $c = ''): void {
        $FindGuideModel = new FindGuideModel;
        $package = $FindGuideModel->getGuidePackages($a);
        $this->view('customer/bookPackage', ['package' => $package]);
    }
}