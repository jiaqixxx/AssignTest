<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;
use Validator;
use App\Assignment;

class CommentController extends Controller
{
    public function getComments($assignmentId)
    {
        $comments = Comment::select('comment')
            ->where('assignment_id', '=', $assignmentId)
            ->get();
        return $comments;
    }

    public function saveComments(Request $request)
    {
        $rules = [
            'assignmentId' => 'string|required',
            'content' => 'string|required'
        ];
        $validator = Validator::make($request->all(),$rules);

        if($validator->fails()){
            return json_encode(['result' => 'Failed']);
        }
        $assignmentId = $request->input('assignmentId');
        $content = $request->input('content');
        if(Comment::where('assignment_id', '=', $assignmentId)->exists()){
            $result = Comment::where('assignment_id', '=', $assignmentId)
                ->update(['comment' => $content]);
            if($result){
                echo $result;
            }else{
                echo json_encode(['result' => 'Failed']);
            }
        }else{

            //TODO
            $comment = new Comment([
                'assignment_id' => $assignmentId,
                'comment' => $content
            ]);
            $result = $comment->save();
            if($result){
                $updateAssignment = Assignment::where('id', '=', $assignmentId)
                    ->update(['has_comments' => 1]);
                if($updateAssignment){
                    echo $updateAssignment;
                }else{
                    echo json_encode(['result' => 'Failed']);
                }
            }else{
                echo json_encode(['result' => 'Failed']);
            }
        }
    }

    // TODO
    public function saveImages(Request $request)
    {
        $image = $request->file('image');
        $filename = $request->input('assignmentId').'_'.time().'.'.$image->getClientOriginalExtension();
        $image->storeAs('public/images', $filename);
        return 'storage/images/'.$filename;
    }
}
