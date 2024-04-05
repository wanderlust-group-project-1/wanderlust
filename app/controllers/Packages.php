<?php

class Packages
{
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''): void
    {

        $user = $_SESSION['USER'];

        if ($user->role == 'guide') {
            $this->view('guide/packages');
        }
    }
<<<<<<< HEAD

    public function create(string $a = '', string $b = '', string $c = ''): void
    {
        $guideId = 1;

        $packageModel = new PackageModel();
        $data = [
            'guide_id' => $guideId,
            'price' => $_POST['price'],
            'max_group_size' => $_POST['max_group_size'],
            'max_distance' => $_POST['max_distance'],
            'transport_needed' => $_POST['transport_needed'],
            'places' => $_POST['places']
        ];

        $packageModel->createPackage($data);
        redirect('packages');
    }

    public function edit(int $packageId): void
    {
        $packageModel = new PackageModel();
        $package = $packageModel->getPackage($packageId);

        $this->view('packages/edit', ['packages' => $package]);
    }

    public function update(int $packageId): void
    {
        $packageModel = new PackageModel();
        $data = [
            'price' => $_POST['price'],
            'max_group_size' => $_POST['max_group_size'],
            'max_distance' => $_POST['max_distance'],
            'transport_needed' => $_POST['transport_needed'],
            'places' => $_POST['places']
        ];

        $packageModel->updatePackage($packageId, $data);
        redirect('packages');
    }

    public function delete(int $packageId): void
    {
        $packageModel = new PackageModel();
        $packageModel->deletePackage($packageId);
        redirect('packages');
    }

=======
>>>>>>> c9fac3582af613de0b8a6bb9d6ee4f181f14d0fb
}
