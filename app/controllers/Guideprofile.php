<?php

class Guideprofile {
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''): void {
        $user = $_SESSION['USER'];

        if ($user->role == 'guide') {
            $guideProfileModel = new GuideprofileModel();
            $guideProfile = $guideProfileModel->getGuideProfileByUserId($user->id); // Assuming you have a method to fetch guide profile by user ID
            $this->view('guide/guideprofile', ['user' => $user, 'guideProfile' => $guideProfile]);
        }
    }

    public function update(string $a = '', string $b = '', string $c = ''): void {
        $guideProfileModel = new GuideprofileModel();
        $guideProfileModel->updateGuideProfile($_POST);
        redirect('guideprofile');
    }
}