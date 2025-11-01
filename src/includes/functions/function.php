 <?php

 
 function getPackageDetails(){
    global $conn;

    if(!isset($_GET['packageId']) || empty($_GET['packageId'])){
        http_response_code(404);
        echo json_encode(['error' => 'PackageID is required']);
        exit;
            }

            $packageId = mysqli_real_escape_string($conn, $_GET['packageId']);

             $inclusion = $conn->prepare("SELECT * FROM inclusion WHERE packageID = ?");
                $inclusion->bind_param('s', $packageId);
                $inclusion->execute();
                $inc = $inclusion->get_result();


                $addons = $conn->prepare("SELECT * FROM addons WHERE packageID = ?");
                $addons->bind_param('s', $packageId);
                $addons->execute();
                $ad = $addons->get_result();


                $data = [
                    "inclusions" => [],
                    "addons" => []

                ];

                while($inclusions = $inc->fetch_assoc()){
                    $data['inclusions'][] = $inclusions['Description'];
                }

                while($add = $ad->fetch_assoc()){
                    $data['addons'][] = [
                        'Description' => $add['Description'],
                        'Price' => $add['Price']
                    ];
                }

                $inclusion->close();
                $addons->close();
                header('Content-Type: application/json');
                echo json_encode($data);
                exit;
 }
