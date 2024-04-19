<?php

class GuideAvailability{
    use Controller;
    public function index(string $a = '', string $b = '', string $c = ''): void {
        $this->view('guide/guideAvailability');
    }

    public function update(string $a = '', string $b = '', string $c = ''): void {

        $request  = new JSONRequest;
        $response = new JSONResponse;

        $id = UserMiddleware::getUser()['id'];

        // $request->getAll();
        // show($request->getAll());
        $data = $request->getAll();
        $data['guide_id'] = $id;

        $GuideAvailabilityModel = new GuideAvailabilityModel;
        $sch =  $GuideAvailabilityModel->getScheduleByGuideIdandDate(UserMiddleware::getUser()['id'], $data['date']);

        if($sch){
            $GuideAvailabilityModel->updateSchedule($data);
        }else{
            $GuideAvailabilityModel->createSchedule($data);
        }


        $response->success(true)
            ->message('Schedule updated successfully')
            ->statusCode(200)
            ->send();
        
    }
}