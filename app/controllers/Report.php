<?php


use Dompdf\Dompdf;


class Report
{
    use Controller;



    public function orderReportByRental(string $a = '', string $b = '', string $c = ''): void
    {


        $rent = new RentModel;
        $order = $rent->getRental($a);
        $items = $rent->getItemListbyRentId($a);





        ob_start();
        $companyName = "Wanderlust";
        $reportTitle = "Order Description";
        $reportDate = date("Y-m-d");


        // show($order);

        $this->view('reports/order', ['order' => $order, 'items' => $items , 'companyName' => $companyName, 'reportTitle' => $reportTitle, 'reportDate' => $reportDate]);

        // show($order);
        $html = ob_get_clean();

        // echo $html;
        // die();


        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream("order-details.pdf", array("Attachment" => false));
    }
}
