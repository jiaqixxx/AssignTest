<?php

namespace Os;


class AssignmentsRepositoryFacade
{
    private $assignmentRepo;
    private $adminArray = [1,25];
    private $adminId = '';
    private $defaultNumAssignments = 5;
    private $agentId;

    public function __construct(AssignmentsRepository $assignmentRepo, $adminId, $agentId='', $numAssignments='')
    {
        $this->assignmentRepo = $assignmentRepo;
        $this->adminId = $adminId;
        if($numAssignments != ''){
            $this->defaultNumAssignments = $numAssignments;
        }
        $this->agentId = $agentId;
    }

    public function getAllAgents(){
        if(in_array($this->adminId, $this->adminArray)){
            return $this->assignmentRepo->getAgents();
        }
    }

    public function getAllAgentsWorkload(){
        if(in_array($this->adminId, $this->adminArray)){
            $workloads = $this->assignmentRepo->getAgentsWorkload();
            return $workloads;
        }
    }

    public function checkEnoughOrdersToAssign()
    {
        $today = date('Y-m-d H:i:s', mktime(0,0,0));
        $ordersNum = $this->assignmentRepo->getAllOrdersNum($today);
        if($ordersNum == 0){
            $result = [false, $ordersNum];
            return $result;
        }
        $assignedOrders = $this->assignmentRepo->getAssignedOrderIds($today);
        $ordersNumLeft = $ordersNum - count($assignedOrders);
        if($ordersNumLeft < $this->defaultNumAssignments){
            $result = [false, $ordersNumLeft];
            return $result;
        }else{
            $result = [true, $assignedOrders];
            return $result;
        }
    }

    public function randomAssignments(){
        list($firstResult, $secondResult) = $this->checkEnoughOrdersToAssign();
        if(!$firstResult){
            $result = [false, $secondResult];
            return $result;
        }
        $today = date('Y-m-d H:i:s', mktime(0,0,0));
        $assignedOrders = $secondResult;
        if(!empty($assignedOrders)){
            $assignOrders = $this->assignmentRepo->getOrdersWithAssignedOrders($assignedOrders, $today);
        }else{
            $assignOrders = $this->assignmentRepo->getOrdersWithoutAssignedOrders($today);
        }
        $randOrders = array_rand($assignOrders, $this->defaultNumAssignments);
        if(count($randOrders) > 1){
            foreach($randOrders as $key => $value){
                $randOrders[$key] = $assignOrders[$value];
            }
        }else{
            $randOrders = $assignOrders[$randOrders];
        }
        $result = [true, $randOrders];
        return $result;
    }

    public function assignTestingAssignments()
    {
        list($firstResult, $assignments) = $this->randomAssignments();
        if(!$firstResult){
            return [false, $assignments];
        }
        $data = [];
        if(count($assignments) > 1){
            foreach($assignments as $index=>$assignment){
                $data[$index] = [
                    'order_id' => $assignment,
                    'agent_id' => $this->agentId,
                    'assigned_by' => 'Admin'
                ];
            }
            return [true, $this->assignmentRepo->postAssignmentsToAgent($data)];
        }else{
            $data = [
                'order_id' => $assignments,
                'agent_id' => $this->agentId,
                'assigned_by' => 'Admin'
            ];
            return [true, $this->assignmentRepo->postOneAssignmentToAgent($data)];
        }
    }

    public function getAllInProgressAssignmentsDetails()
    {
        $inProgressAssignments = $this->assignmentRepo->getInProgressAssignmentsDetails();
        foreach($inProgressAssignments as $index=>$assignment){
            $items = json_decode($assignment['order_items']);
            $inProgressAssignments[$index]['order_items'] = $items;
            $customerDetail = json_decode($assignment['customer_details']);
            $inProgressAssignments[$index]['customer_details'] = $customerDetail;
        }
        return $inProgressAssignments;
    }

    public function getComments($oId)
    {
       return $this->assignmentRepo->getAssignmentComments($oId);
    }

    public function approveAssignmentByAdmin($assignmentId)
    {
        if(in_array($this->adminId, $this->adminArray)){
            return $this->assignmentRepo->approveAssignment($assignmentId);
        }
    }

    public function unApproveAssignmentByAdmin($assignmentId)
    {
        if(in_array($this->adminId, $this->adminArray)){
            return $this->assignmentRepo->unApproveAssignment($assignmentId);
        }
    }

    public function getApprovedLogs()
    {
        if(in_array($this->adminId, $this->adminArray)){
            $approvedAssignments =  $this->assignmentRepo->getApprovedAssignments();
            foreach($approvedAssignments as $index=>$assignment){
                $items = json_decode($assignment['order_items']);
                $approvedAssignments[$index]['order_items'] = $items;
                $customerDetail = json_decode($assignment['customer_details']);
                $approvedAssignments[$index]['customer_details'] = $customerDetail;
            }
            return $approvedAssignments;
        }
    }

    public function searchAssignments($id='', $orderNum='', $customerDetails='', $bugs='')
    {
        $cols = [
            'a.*',
            'ad.admin_firstname',
            'ao.order_items',
            'ao.environment',
            'ao.product_look_up',
            'ao.customer_details'
        ];
        $db = \OsDatabase::getInstance()->join('assign_orders ao', 'a.order_id=ao.order_id', 'LEFT')
                                        ->join('admin ad', 'a.agent_id=ad.admin_id', 'LEFT');
        if($id != ''){
            $db->where('a.id', $id);
        }
        if($orderNum != ''){
            $db->where('a.order_id', $orderNum);
        }
        if($customerDetails != ''){
            $db->where('ao.customer_details', '%'.$customerDetails.'%', 'like');
        }
        if($bugs != ''){
            if($bugs == 'Yes'){
                $db->where('a.has_comments', 1);
            }else if($bugs == 'No'){
                $db->where('a.has_comments', 0);
            }
        }
        $searchedAssignments = $db->get('assignments a', null, $cols);
        foreach($searchedAssignments as $index=>$assignment){
            $items = json_decode($assignment['order_items']);
            $searchedAssignments[$index]['order_items'] = $items;
            $customerDetail = json_decode($assignment['customer_details']);
            $searchedAssignments[$index]['customer_details'] = $customerDetail;
        }
        return $searchedAssignments;
    }
}
