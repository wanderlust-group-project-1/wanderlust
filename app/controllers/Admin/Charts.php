<?php

class Charts
{
    use Controller;

    // public function index(string $a = '', string $b = '', string $c = ''):void {
    //     $this->view('admin/chart');
    // }

    public function item(string $a = '', string $b = '', string $c = ''): void
    {
        $this->view('admin/chart');
    }

    public function index(string $a = '', string $b = '', string $c = ''): void
    {

        $stats = new AdminStatsModel;
        $data['rentalServices'] = $stats->getRentalServiceCount();

        $stats = new AdminStatsModel;
        $data['customers'] = $stats->getCustomerCount();

        $stats = new AdminStatsModel;
        $data['guides'] = $stats->getGuideCount();

        $stats = new AdminStatsModel;
        $data['tips'] = $stats->getTipsCount();

        $stats = new AdminStatsModel;
        $data['rentComplaints'] = $stats->getRentComplainsCount();

        $stats = new AdminStatsModel;
        $data['orders'] = $stats->getOrdersCount();

        $this->view('admin/chart', $data);
    }
}
