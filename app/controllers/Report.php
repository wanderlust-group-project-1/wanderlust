<?php 


  use Dompdf\Dompdf; 


class Report {
    use Controller;


   
    public function index(string $a = '', string $b = '', string $c = ''): void
    {

      
        $request = new JSONRequest;

        $d = $request->getAll();

        $d = [
          'id' => UserMiddleware::getUser()['id'],
          'from' => '2021-01-01',
           'to' => '2024-12-31'];



        $rental = new RentalServiceModel;
        $data = [
            'info' => $rental->getRentalService($d['id'])[0],
            'income' => $rental->GetMonthlyIncome($d['id'], $d['from'], $d['to'])


        ];
        


        // generate id for the report
        $id = uniqid();
        $servicefee = 10/100;
        $name = $data['info']->name;
        $address = $data['info']->address;
        $companyName = "Wanderlust";
        $reportTitle = "Monthly Income Report for {$name}";
        $reportDate = date("Y-m-d");
        
        // HTML content for the PDF
        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Income Report</title>
            <style>
                body { font-family: 'Helvetica', sans-serif; font-size: 14px; }
                h1, h2 { text-align: center; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
            </style>
        </head>
        <body>
            <h1>{$companyName}</h1>
            <h2>{$reportTitle} <br> {$reportDate}</h2>
            <p><strong>Name:</strong> {$name} <br>
            <strong>Address:</strong> {$address}</p>
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Income</th>
                    </tr>
                </thead>
                <tbody>
        HTML;
        
        foreach ($data['income'] as $monthlyIncome) {
            // $html .= "<tr><td>$month</td><td>\$$income</td></tr>";
            // $html .= "<tr><td>{$monthlyIncome->Month}</td><td>{$monthlyIncome->MonthlyIncome}</td></tr>";
            // Currency format
            $html .= "<tr><td>{$monthlyIncome->Month}</td><td>Rs.{$monthlyIncome->MonthlyIncome}</td></tr>";
    
        }

        // total income
        $totalIncome = 0;
        foreach ($data['income'] as $monthlyIncome) {
            $totalIncome += $monthlyIncome->MonthlyIncome;

        }
        // Currency format
        // $totalIncome = number_format($totalIncome, 2);
        // $html .= "<tr><td><strong>Total</strong></td><td><strong>Rs.{number_format($totalIncome, 2);}</strong></td></tr>";
        $html .= "<tr><td><strong>Total</strong></td><td><strong>Rs." . number_format($totalIncome, 2) . "</strong></td></tr>";


        // Service Fee
        // to int
        
        $serviceFee = (int)$totalIncome * $servicefee;
        // $serviceFee = number_format($serviceFee, 2);
        // $html .= "<tr><td><strong>Service Fee</strong></td><td><strong>Rs.{number_format($serviceFee, 2)}</strong></td></tr>";
        $html .= "<tr><td><strong>Service Fee</strong></td><td><strong>Rs." . number_format($serviceFee, 2) . "</strong></td></tr>";

        // Net Income
        $netIncome = (int)$totalIncome - (int)$serviceFee;
        // $netIncome = number_format($netIncome, 2);
        // $html .= "<tr><td><strong>Net Income</strong></td><td><strong>Rs.{number_format($netIncome, 2);}</strong></td></tr>";
        $html .= "<tr><td><strong>Net Income</strong><br>
            " . $d['from'] . " - " . $d['to'] . "
        
        </td><td><strong>Rs." . number_format($netIncome, 2) . "</strong></td></tr>";




        
        $html .= <<<HTML
                </tbody>
            </table>

            <!-- Issue Date and by  this is generated by the system -->
            <p> This report was generated by the system on {$reportDate}</p>
            <!-- verify link -->
            <p>Verify at: <a href="{$_SERVER['HTTP_HOST']}/reports/income_report_{$id}.pdf">{$_SERVER['HTTP_HOST']}/reports/income_report_{$id}.pdf</a></p>



           

        </body>
        </html>
        HTML;
        
        // Create an instance of Dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
        
        // Render the HTML as PDF
        $dompdf->render();

        // Save the generated PDF to a file on the server
        $output = $dompdf->output();
        file_put_contents("reports/income_report_{$id}.pdf", $output);


      

        

        
        // Output the generated PDF to Browser
        $dompdf->stream("income_report.pdf", array("Attachment" => false));
        // echo $html;
        
    }
    
}