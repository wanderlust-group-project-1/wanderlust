<?php

class GuideAvailability{
    use Controller;
    public function deleteSchedule(string $a = '', string $b = '', string $c = ''): void {
        $request = JSONRequest::createFromFormData();
        $response = new JSONResponse;

        $GuideAvailabilityModel = new GuideAvailabilityModel;

        $data = $GuideAvailabilityModel->deleteSchedule($a);

        if (!$data) {
            $response->success(true)
                ->message('Schedule deleted successfully')
                ->statusCode(200)
                ->send();
        } else {
            $response->success(false)
                ->data(['errors' => $GuideAvailabilityModel->errors])
                ->message('Validation failed')
                ->statusCode(422)
                ->send();
        }
    }

    public function update(string $a = '', string $b = '', string $c = ''): void {
        $request = JSONRequest::createFromFormData();
        $response = new JSONResponse;

        $GuideAvailabilityModel = new GuideAvailabilityModel;

        $data = $GuideAvailabilityModel->updateSchedule($request->getAll(), $a);

        if (!$data) {
            $response->success(true)
                ->message('Schedule updated successfully')
                ->statusCode(200)
                ->send();
        } else {
            $response->success(false)
                ->data(['errors' => $GuideAvailabilityModel->errors])
                ->message('Validation failed')
                ->statusCode(422)
                ->send();
        }
    }
}