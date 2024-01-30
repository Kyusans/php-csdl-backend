<?php
include "headers.php";

class Admin
{
    function login($json)
    {
        // {"username":"admin","password":"admin"}
        include "connection.php";
        $json = json_decode($json, true);
        $sql = "SELECT * FROM tbl_admin WHERE adm_employee_id = :userId AND BINARY adm_password = :password";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":userId", $json["userId"]);
        $stmt->bindParam(":password", $json["password"]);
        $returnValue = 0;
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $rs = $stmt->fetch(PDO::FETCH_ASSOC);
            $returnValue = json_encode($rs);
        }
        return $returnValue;
    }

    function addScholar($json)
    {
        include "connection.php";
        $json = json_decode($json, true);
        $password = $json["lastName"] . "123";
        $sql = "INSERT INTO tbl_scholars(stud_school_Id, stud_last_name, stud_first_name, stud_course_id, stud_year_level, stud_scholarship_type_id, stud_password, stud_contact_number) 
            VALUES(:schoolId, :lastName, :firstName, :courseId, :yearLevel, :scholarShipId, :password, :contact)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":schoolId", $json["schoolId"]);
        $stmt->bindParam(":lastName", $json["lastName"]);
        $stmt->bindParam(":firstName", $json["firstName"]);
        $stmt->bindParam(":courseId", $json["courseId"]);
        $stmt->bindParam(":yearLevel", $json["yearLevel"]);
        $stmt->bindParam(":scholarShipId", $json["scholarShipId"]);
        $stmt->bindValue(":password", $password);
        $stmt->bindParam(":contact", $json["contact"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function getCourse()
    {
        include "connection.php";
        $sql = "SELECT * FROM tbl_course ORDER BY crs_name";
        $stmt = $conn->prepare($sql);
        $returnValue = 0;
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $returnValue = json_encode($rs);
        }

        return $returnValue;
    }
    function getScholarshipType()
    {
        include "connection.php";
        $sql = "SELECT * FROM tbl_scholarship_type ORDER BY type_name";
        $stmt = $conn->prepare($sql);
        $returnValue = 0;
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $returnValue = json_encode($rs);
        }

        return $returnValue;
    }

    function addAdmin($json)
    {
        include "connection.php";
        $json = json_decode($json, true);
        $image = "emptyImage.jpg";
        $fullName = $json["firstName"] . " " . $json["lastName"];
        $userId = $json["userId"];

        if (recordExists($userId, "tbl_admin", "adm_employee_id") ) {
            return -1;
        }

        $sql = "INSERT INTO tbl_admin(adm_name, adm_employee_id, adm_password, adm_email, adm_image, adm_user_level) 
                VALUES(:fullName, :userId, :password, :email, :image, 100)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":fullName", $fullName);
        $stmt->bindParam(":password", $json["password"]);
        $stmt->bindParam(":email", $json["email"]);
        $stmt->bindParam(":image", $image);
        $stmt->bindParam(":userId", $userId);
        $stmt->execute();

        return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function addDepartment($json)
    {
        include "connection.php";
        $json = json_decode($json, true);
        if (recordExists($json["department"], "tbl_departments", "dept_name")) {
            return -1;
        }
        $sql = "INSERT INTO tbl_departments(dept_name) VALUES(:name)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":name", $json["department"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function addSchoolYear($json)
    {
        include "connection.php";
        $json = json_decode($json, true);
        if (recordExists($json["schoolYear"], "tbl_sy", "sy_name")) {
            return -1;
        }
        $sql = "INSERT INTO tbl_sy(sy_name) VALUES(:schoolYear)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":schoolYear", $json["schoolYear"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function getDepartment(){
        include "connection.php";
        $sql = "SELECT * FROM tbl_departments ORDER BY dept_name";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $returnValue = 0;
        if($stmt->rowCount() > 0){
            $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $returnValue = json_encode($rs);
        }
        return $returnValue;
    }

    // function updateAdmin($json){
    //     include "connection.php";
    //     $json = json_decode($json, true);
    //     $sql = "UPDATE tbl_admin SET adm_password = :password, adm_employee_id = :userId WHERE adm_id = :userId";
    //     $stmt = $conn->prepare($sql);
    //     $stmt->bindParam(":password", $json["password"]);
    //     $stmt->bindParam(":userId", $json["userId"]);
    //     $stmt->execute();
    //     return $stmt->rowCount() > 0 ? 1 : 0;
    // }

    // function getAdminInfo($json){
    //     // {"userId": 1}
    //     include "connection.php";
    //     $json = json_decode($json, true);
    //     $sql = "SELECT * FROM tbl_admin WHERE adm_id  = :userId";
    //     $stmt = $conn->prepare($sql);
    //     $stmt->bindParam(":userId", $json["userId"]);
    //     $returnValue = 0;
    //     $stmt->execute();
    //     if ($stmt->rowCount() > 0) {
    //         $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    //         $returnValue = json_encode($rs);
    //     }
    //     return $returnValue;
    // }


} //admin 

function recordExists($value, $table, $column)
{
    include "connection.php";
    $sql = "SELECT COUNT(*) FROM $table WHERE $column = :value";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":value", $value);
    $stmt->execute();
    $count = $stmt->fetchColumn();
    return $count > 0;
}

$json = isset($_POST["json"]) ? $_POST["json"] : "0";
$operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";

$admin = new Admin();

switch ($operation) {
    case "login":
        echo $admin->login($json);
        break;
    case "addScholar":
        echo $admin->addScholar($json);
        break;
    case "getCourse":
        echo $admin->getCourse();
        break;
    case "getScholarshipType":
        echo $admin->getScholarshipType();
        break;
    case "addAdmin":
        echo $admin->addAdmin($json);
        break;
    case "addDepartment":
        echo $admin->addDepartment($json);
        break;
    case "addSchoolYear":
        echo $admin->addSchoolYear($json);
        break;
    case "getDepartment":
        echo $admin->getDepartment();
        break;
}
