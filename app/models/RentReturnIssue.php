<?php


class RentReturnIssueModel {
    use Model;



    // CREATE TABLE rent_return_issues (
    //     id INT AUTO_INCREMENT PRIMARY KEY,
    //     rent_id INT NOT NULL,
    //     complain JSON NOT NULL,
    //     charge DECIMAL(10, 2) NOT NULL
    // );


    // var data = {
    //     order_id: orderId,
    //     issues: [],
    //     issue_descriptions: [],
    //     charges: []
    // };
    


    protected string $table = 'rent_return_issues';

    protected array $allowedColumns = [
        'rent_id',
        'complains',
        'charge'
    ];

    public function createReturnIssue($data) {
        $data = array_filter($data, function ($key) {
            return in_array($key, $this->allowedColumns);
        }, ARRAY_FILTER_USE_KEY);

        $this->insert($data);


    
       






    }


}