<?php

class Guide {
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''):void {
        $this->view('guide');
    }

    public function find(string $a = '', string $b = '', string $c = ''):void {
        $this->view('guide/find');
    }

    public function update(string $a = '', string $b = '', string $c = ''): void
    {
        $guide = new guideModel();
        $guide->updateGuide($_POST);
    
        // Determine the current page URL
        $currentPage = rtrim($_SERVER['REQUEST_URI'], '/');
    
        // Check if it's the dashboard page
        if ($currentPage === ROOT_DIR . '/dashboard') {
            redirect('dashboard');
        }
        // Check if it's the packages page
        elseif ($currentPage === ROOT_DIR . '/packages') {
            redirect('packages');
        }
        // If it's neither dashboard nor packages, you can add more conditions as needed
    
        // Default redirect
        redirect('dashboard');
        // $this->view('customer/profile');
    }    

    public function getPackages(string $a = '', string $b = '', string $c = ''): void {
        $packageModel = new PackageModel();
        $data["packages"] = $packageModel->getPackagesByGuide(UserMiddleware::getUser()['id']);
        $this->view('guide/packages.list', $data);
    }
    
    public function getPackage(string $packageId = '',string $b = '', string $c = ''): void {
        $guideId = UserMiddleware::getUser()['id'];
        $packageModel = new PackageModel();
        $data["package"] = $packageModel->getPackageByGuide($guideId, $packageId);
        $this->view('guide/package', $data);
    }

    public function getAvailability(string $a = '', string $b = '', string $c = ''): void {
        $GuideAvailabilityModel = new GuideAvailabilityModel();
        $data["availability"] = $GuideAvailabilityModel->getScheduleByGuideIdandDate(UserMiddleware::getUser()['id'], date('Y-m-d'));
        $this->view('guide/guideavailability', $data);
    }

}

