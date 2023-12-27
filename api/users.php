<?php 
    include "headers.php";

    class User{
        function login($json){
            // {"userId":"pitok","password":"pitok"}
            include "connection.php";
            $json = json_decode($json, true);
            $sql = "SELECT * FROM tbl_supervisors_master WHERE supM_employee_id = :userId AND supM_password = :password";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":userId", $json["userId"]);
            $stmt->bindParam(":password", $json["password"]);
            $returnValue = 0;
            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $rs = $stmt->fetch(PDO::FETCH_ASSOC);
                $returnValue = json_encode($rs);
            }else{
                $jsonEncoded = json_encode($json);
                $returnValue = scholarLogin($jsonEncoded);
            }
            return $returnValue;
        }

        function getStudentInformation($json){
            // {"studId":"02-2223-08840"}
            include "connection.php";
            $json = json_decode($json, true);
            $sql = "SELECT * FROM tblstudents WHERE stud_schoolId = :studId";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':studId',$json['studId']);
            $returnValue = 0;
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $rs = $stmt->fetch(PDO::FETCH_ASSOC);
                $returnValue = json_encode($rs);
            }

            return $returnValue;
        }
    }//user

    function scholarLogin($json){
        // {"username":"02-2223-08840","password":"Macario123"}
        include "connection.php";
        $json = json_decode($json, true);
        $sql = "SELECT * FROM tbl_scholars WHERE stud_school_id = :userId AND stud_password = :password";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":userId", $json["userId"]);
        $stmt->bindParam(":password", $json["password"]);
        $stmt->execute();
        $returnValue = 0;
        if($stmt->rowCount() > 0) {
            $rs = $stmt->fetch(PDO::FETCH_ASSOC);
            $returnValue = json_encode($rs);
        }
        return $returnValue;
    }

    $json = isset($_POST["json"]) ? $_POST["json"] : "0";
    $operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";

    $user = new User();

    switch($operation){
        case "login":
            echo $user->login($json);
            break;
        case "getStudentInformation":
            echo $user->getStudentInformation($json);
            break;
    }
?>