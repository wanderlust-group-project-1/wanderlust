<?php

class FindGuide{
    use Controller;
    public function index(string $a = '', string $b = '', string $c = ''): void {
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
        // show($guide);

        $this->view('customer/OneGuide', ['guide' => $guide]);
    }
}