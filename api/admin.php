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

	function getList($json)
	{
		include "connection.php";
		$json = json_decode($json, true);
		$tableName = $json["tableName"];
		$orderBy = $json["orderBy"];
		$sql = "SELECT * FROM $tableName ORDER BY $orderBy";
		$stmt = $conn->prepare($sql);
		$returnValue = 0;
		$stmt->execute();
		if ($stmt->rowCount() > 0) {
			$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$returnValue = json_encode($rs);
		}
		return $returnValue;
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

		if (recordExists($userId, "tbl_admin", "adm_employee_id")) {
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

	function addSupervisor($json)
	{
		include "connection.php";
		$json = json_decode($json, true);
		$fullName = $json["firstName"] . " " . $json["lastName"];
		if (recordExists($json["employeeId"], "tbl_supervisors_master", "supM_employee_id")) {
			return -1;
		}
		$sql = "INSERT INTO tbl_supervisors_master(supM_employee_id, supM_password, supM_name, supM_department_id, supM_email, supM_status) 
                VALUES(:employeeId, :password, :name, :department, :email, 1)";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":employeeId", $json["employeeId"]);
		$stmt->bindParam(":password", $json["password"]);
		$stmt->bindParam(":name", $fullName);
		$stmt->bindParam(":email", $json["email"]);
		$stmt->bindParam(":department", $json["department"]);
		$stmt->execute();
		return $stmt->rowCount() > 0 ? 1 : 0;
	}

	function addCourse($json)
	{
		include "connection.php";
		$json = json_decode($json, true);
		if (recordExists($json["course"], "tbl_course", "crs_name")) {
			return -1;
		}
		$sql = "INSERT INTO tbl_course(crs_name, crs_dept_id) VALUES(:course, :department)";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":course", $json["course"]);
		$stmt->bindParam(":department", $json["department"]);
		$stmt->execute();
		return $stmt->rowCount() > 0 ? 1 : 0;
	}

	function addScholarShipType($json)
	{
		include "connection.php";
		$json = json_decode($json, true);
		if (recordExists($json["scholarshipType"], "tbl_scholarship_type", "type_name")) {
			return -1;
		}
		$sql = "INSERT INTO tbl_scholarship_type(type_name) VALUES(:scholarshipType)";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":scholarshipType", $json["scholarshipType"]);
		$stmt->execute();
		return $stmt->rowCount() > 0 ? 1 : 0;
	}

	function addOffice($json)
	{
		include "connection.php";
		$json = json_decode($json, true);
		if (recordExists($json["officeName"], "tbl_office_master", "off_name")) {
			return -1;
		}
		$sql = "INSERT INTO tbl_office_master(off_name, off_type_id) VALUES(:officeName, 1)";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":officeName", $json["officeName"]);
		$stmt->execute();
		return $stmt->rowCount() > 0 ? 1 : 0;
	}

	function addClass($json)
	{
		include "connection.php";
		$json = json_decode($json, true);
		if (recordExists($json["className"], "tbl_office_master", "off_name")) {
			return -1;
		}

		$sql = "INSERT INTO tbl_office_master(off_name, off_descriptive_title, off_subject_code, off_section, off_room, off_type_id) 
                VALUES(:className, :description, :subjectCode, :section, :room, 2)";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":className", $json["className"]);
		$stmt->bindParam(":description", $json["description"]);
		$stmt->bindParam(":subjectCode", $json["subjectCode"]);
		$stmt->bindParam(":section", $json["section"]);
		$stmt->bindParam(":room", $json["room"]);

		$stmt->execute();
		return $stmt->rowCount() > 0 ? 1 : 0;
	}

	function addScholarSubType($json)
	{
		include "connection.php";
		$json = json_decode($json, true);

		if (recordExists($json["typeName"], "tbl_scholarship_sub_type", "stype_name")) {
			return -1;
		}

		$sql = "INSERT INTO tbl_scholarship_sub_type(stype_type_id, stype_name, stype_max_hours) 
        VALUES(:scholarshipType, :typeName, :maxHours)";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":scholarshipType", $json["scholarshipType"]);
		$stmt->bindParam(":typeName", $json["typeName"]);
		$stmt->bindParam(":maxHours", $json["maxHours"]);

		$stmt->execute();
		return $stmt->rowCount() > 0 ? 1 : 0;
	}


	function getDepartment()
	{
		include "connection.php";
		$sql = "SELECT * FROM tbl_departments ORDER BY dept_name";
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$returnValue = 0;
		if ($stmt->rowCount() > 0) {
			$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$returnValue = json_encode($rs);
		}
		return $returnValue;
	}

	function setSYActive($json)
	{
		include "connection.php";
		try {
			$conn->beginTransaction();
			$json = json_decode($json, true);
			$sql = "UPDATE tbl_sy SET sy_status = 0";
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$sql2 = "UPDATE tbl_sy SET sy_status = 1 WHERE sy_id = :schoolYear";
				$stmt2 = $conn->prepare($sql2);
				$stmt2->bindParam(":schoolYear", $json["schoolYearId"]);
				$stmt2->execute();
				$conn->commit();
			}
			return $stmt2->rowCount() > 0 ? 1 : 0;
		} catch (Exception $e) {
			$conn->rollBack();
			return $e;
		}
	}
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
	case "addSupervisor":
		echo $admin->addSupervisor($json);
		break;
	case "addCourse":
		echo $admin->addCourse($json);
		break;
	case "addScholarShipType":
		echo $admin->addScholarShipType($json);
		break;
	case "addOffice":
		echo $admin->addOffice($json);
		break;
	case "addClass":
		echo $admin->addClass($json);
		break;
	case "addScholarSubType":
		echo $admin->addScholarSubType($json);
		break;
	case "getList":
		echo $admin->getList($json);
		break;
	case "setSYActive":
		echo $admin->setSYActive($json);
		break;
}
