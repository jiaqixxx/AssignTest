<?php
require('includes/application_top.php');

if(isset($_GET['action'])){
    $repo = new Os\AssignmentsRepositoryFacade(new Os\AssignmentsRepository(), $login_id);
    if($_GET['action'] == 'getAgents'){
        $agents = $repo->getAllAgents();
        if(!empty($agents)){
            echo json_encode($agents);
        }else{
            echo json_encode(['result' => 'Failed', 'message' => 'Failed to retrieve agents']);
        }
    }else if($_GET['action'] == 'getAgentsWorkload'){
        $agentsWorkloads = $repo->getAllAgentsWorkload();
        echo json_encode($agentsWorkloads);
    }else if($_GET['action'] == 'getInProgressAssignments'){
        $inProgressAssignments = $repo->getAllInProgressAssignmentsDetails();
        echo json_encode($inProgressAssignments);
    }else if($_GET['action'] == 'getComments'){
        $oId = $_GET['orderId'];
        $comments = $repo->getComments($oId);
        echo json_encode($comments);
    }else if($_GET['action'] == 'getApprovedLogs'){
        $approvedLogs = $repo->getApprovedLogs();
        echo json_encode($approvedLogs);
    }else if($_GET['action'] == 'search'){
        $id = $_GET['id'];
        $orderNum = $_GET['orderNum'];
        $customerDetails = $_GET['customerDetails'];
        $bugs = $_GET['bugs'];
        $searchedAssignments = $repo->searchAssignments($id, $orderNum, $customerDetails, $bugs);
        echo json_encode($searchedAssignments);
    }
    exit();
}

if(isset($_POST['action'])){
    if($_POST['action'] == 'assignTestingOrders'){
        $agentId = $_POST['agentId'];
        $numAssignments = $_POST['numAssignments'];
        if($numAssignments < 1){
            echo json_encode(['result' => 'Failed', 'message' => 'The number of testing assignments cannot be less than one']);
            exit();
        }
        $repo = new Os\AssignmentsRepositoryFacade(new Os\AssignmentsRepository(), $login_id, $agentId, $numAssignments);
        list($result, $assignments) = $repo->assignTestingAssignments();
        if($result){
            if($assignments){
                echo json_encode($assignments);
            }else{
                echo json_encode(['result' => 'Failed', 'message' => 'Failed to assign testing orders']);
            }
        }else{
            $message = "Total orders can be assigned today: ".$assignments;
            echo json_encode(['result' => 'Failed', 'message' => $message]);
        }
        exit();
    }else if($_POST['action'] == 'approveAssignment'){
        $assignmentId = $_POST['assignmentId'];
        $repo = new \Os\AssignmentsRepositoryFacade(new Os\AssignmentsRepository(), $login_id);
        $result = $repo->approveAssignmentByAdmin($assignmentId);
        if($result){
            echo json_encode($result);
        }else{
            echo json_encode(['result' => 'Failed', 'message' => 'Failed to approve this assignment']);
        }
        exit();
    }else if($_POST['action'] == 'unApproveAssignment'){
        $assignmentId = $_POST['assignmentId'];
        $repo = new \Os\AssignmentsRepositoryFacade(new Os\AssignmentsRepository(), $login_id);
        $result = $repo->unApproveAssignmentByAdmin($assignmentId);
        if($result){
            echo json_encode($result);
        }else{
            echo json_encode(['result' => 'Failed', 'message' => 'Failed to approve this assignment']);
        }
    }
}