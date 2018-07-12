<?php

namespace Os;


class AssignmentsRepository
{
    public function getAgents()
    {
        $cols = ['admin_id', 'admin_firstname'];
        $db = \OsDatabase::getInstance()->get('admin', null, $cols);
        return $db;
    }

    public function getAgentsWorkload()
    {
        $db = \OsDatabase::getInstance();
        $query = "select admin.admin_firstname as agentName, count(*) as numAssignments from assignments left join admin on admin.admin_id=assignments.agent_id group by admin.admin_id";
        $result = $db->query($query);
        return $result;
    }

    public function getAllOrdersNum($today)
    {
        $db = \OsDatabase::getInstance()->where('created_time', $today, '>=')
                                        ->getValue('assign_orders', 'count(*)');
        return $db;
    }

    public function getAssignedOrderIds($today){
        $db = \OsDatabase::getInstance()->where('created_time', $today, '>=')
                                        ->getValue('assignments', 'order_id', null);
        return $db;
    }

    public function getOrdersWithoutAssignedOrders($today)
    {
        $db = \OsDatabase::getInstance()->where('created_time', $today, '>=')
                                        ->getValue('assign_orders', 'order_id', null);
        return $db;
    }

    public function getOrdersWithAssignedOrders($assignedOrders, $today){
        $db = \OsDatabase::getInstance()->where('order_id', $assignedOrders, 'NOT IN')
                                        ->where('created_time', $today, '>=')
                                        ->getValue('assign_orders', 'order_id', null);
        return $db;
    }

    public function postAssignmentsToAgent($assignmentsInfo)
    {
        $db = \OsDatabase::getInstance()->insertMulti('assignments', $assignmentsInfo);
        return $db;
    }

    public function postOneAssignmentToAgent($assignmentInfo)
    {
        $db = \OsDatabase::getInstance()->insert('assignments', $assignmentInfo);
        return $db;
    }

    public function getInProgressAssignmentsDetails()
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
                                        ->join('admin ad', 'a.agent_id=ad.admin_id', 'LEFT')
                                        ->where('a.is_approved', 0)
                                        ->get('assignments a', null, $cols);
        return $db;
    }

    public function getAssignmentComments($oId)
    {
        $cols = [
            'comment',
            'uploaded_file'
        ];
        $db = \OsDatabase::getInstance()->where('order_id', $oId)
                                        ->get('assignmentsbug', null, $cols);
        return $db;
    }

    public function approveAssignment($assignmentId)
    {
        $db = \OsDatabase::getInstance()->where('id', $assignmentId)
                                        ->update('assignments', ['is_approved' => 1]);
        return $db;
    }

    public function unApproveAssignment($assignmentId)
    {
        $db = \OsDatabase::getInstance()->where('id', $assignmentId)
            ->update('assignments', ['is_approved' => 0]);
        return $db;
    }

    public function getApprovedAssignments()
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
                                        ->join('admin ad', 'a.agent_id=ad.admin_id', 'LEFT')
                                        ->where('a.is_approved', 1)
                                        ->get('assignments a', null, $cols);
        return $db;
    }

    public function postOneCommentByAgent($commentInfo){
        $db = \OsDatabase::getInstance()->insert('asssignmentsbug', $commentInfo);
        return $db;
    }

    public function postMultiCommentsByAgent($commentsInfo){
        $db = \OsDatabase::getInstance()->insertMulti('assignmentsbug', $commentsInfo);
        return $db;
    }

    public function updateComment($commentInfo, $commentId){
        $db = \OsDatabase::getInstance()->where('id', $commentId)->update('assignmentsbug', $commentInfo);
        return $db;
    }

    public function updateAssignments($assignmentInfo, $assignmentId)
    {
        $db = \OsDatabase::getInstance()->where('id', $assignmentId)->update('assignments', $assignmentInfo);
        return $db;
    }
}