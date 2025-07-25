<?php

class Questionnaire_Model extends CI_Model{ 
    public $existQueIds = null;
    public function __construct(){
        parent::__construct(); 
        $this->load->model('Common_modal', 'Common_modal');
        $this->existQueIds = $this->existQuestion();
    }

    public function loadQuestions() {
        $group_id = $this->session->userdata['staff_logged_in']['group_id'];
        $userId = $this->session->userdata['staff_logged_in']['user_id'];
        
        $own_questions = $this->Admin_modal->isAccessRightGiven($group_id,272) ? 1 : 0;
        $all_questions = $this->Admin_modal->isAccessRightGiven($group_id,273) ? 1 : 0;

        $this->db->select('qt.*');
        $this->db->from('question_type qt');
        $q = $this->db->get();
        $main = $q->result();
        foreach ($main as $row) {
            $this->db->select('q.que_id,q.question,q.question_showing,q.added_date,q.answer_method,q.has_img,c.class_name,et.extype_name,s.subject_name,s.subject_code,CONCAT_WS(" ",su.fname, '.', su.lname) as added_person,su.user_id,su.access_group');
            $this->db->from('questions q');
            $this->db->where('q.qt_id',$row->qt_id);
            if (!$all_questions) { // when this doens't have the all questions permission, only it will select own person question list.
                $this->db->where('q.added_by', $userId);
            }
            $this->db->join('class c','c.class_id=q.class_id');
            $this->db->join('exam_types et','et.extype_id=q.exam_type');
            $this->db->join('subjects s','s.sub_id=q.subject');
            $this->db->join('staff_users su','su.user_id=q.added_by');
            $this->db->order_by('q.que_id','DESC');
            $que = $this->db->get();
            $row->questions = $que->result();
        }
        return $main;
    }

    public function loadClassSubjects($class_id) {
        $this->db->select('cs.subject_id,s.subject_name');
        $this->db->from('class_subjects cs');
        $this->db->where('cs.class_id',$class_id);
        $this->db->join('subjects s','s.sub_id=cs.subject_id');
        $q = $this->db->get();
        return $q->result();
    }

    public function save_questions($que_id,$que_arr,$questions,$answers,$correctanswer,$questionImgs,$answerImgs) {
        $this->db->trans_start();
            if (!empty($questions)) {
                foreach ($questions as $key => $value) {
                    $striped = strlen(strip_tags($value)) > 100 ? substr(strip_tags($value),0,100)."..." : strip_tags($value);
                    $que_arr['question'] = $value;
                    $que_arr['question_showing'] = preg_replace('/\s*\R\s*/', ' ', trim(preg_replace('/[ \t]+/', ' ', preg_replace('/[\r\n]+/', "\n", $striped))));
                    if (!empty($questionImgs)) {
                        if ($questionImgs[$key] != '') {
                            $que_arr['has_img'] = 1;
                        }else{
                            $que_arr['has_img'] = 0;
                        }
                    }
                    $this->db->insert('questions',$que_arr);
                    $que_id =  $this->db->insert_id();
                    if (!empty($questionImgs)) {
                        if ($questionImgs[$key] != '') {
                            $quePhoData = array(
                                'table' => 'questions',
                                'field' => 'que_id',
                                'field_id' => $que_id,
                                'photo_path' => $questionImgs[$key],
                                'photo_title' => 'question_image',
                                'photo_order' => 1
                            );
                            $this->db->insert('photo',$quePhoData);
                        }
                    }
                    if (!empty($answers)) {
                        foreach ($answers[$key] as $ans => $answer) {
                            if (!empty($correctanswer)) {
                                if (!isset($correctanswer[$key][$ans])) {
                                    $correct_answer = 0;
                                }else{
                                    $correct_answer = $correctanswer[$key][$ans];
                                }
                            }else{
                                $correct_answer = null;
                            }
                            $answer_arr = array(
                                'que_id' => $que_id, 
                                'answer' => $answer, 
                                'correct_answer' => $correct_answer,
                            );

                            if (!empty($answerImgs)) {
                                if ($answerImgs[$key][$ans] != '') {
                                    $answer_arr['has_img'] = 1;
                                }else{
                                    $answer_arr['has_img'] = 0;
                                }
                            }
                            $this->db->insert('question_answers',$answer_arr);
                            $ans_id = $this->db->insert_id();
                            if (!empty($answerImgs)) {
                                if ($answerImgs[$key][$ans] != '') {
                                    $ansPhoData = array(
                                        'table' => 'question_answers',
                                        'field' => 'qa_id',
                                        'field_id' => $ans_id,
                                        'photo_path' => $answerImgs[$key][$ans],
                                        'photo_title' => 'answer_image',
                                        'photo_order' => 1
                                    );
                                    $this->db->insert('photo',$ansPhoData);
                                }
                            }
                        }
                    }
                }
            }
        $this->db->trans_complete();
    }

    public function update_question($que_id,$que_arr,$questions,$answers,$correctanswer,$questionImgs,$answerImgs) {
        if ($que_id != 0) {
            $this->db->trans_start();
                $type = TRUE;
                if (!empty($questions)) {

                    foreach ($questions as $key => $value) {
                        $striped = strlen(strip_tags($value)) > 100 ? substr(strip_tags($value),0,100)."..." : strip_tags($value);
                        $que_arr['question'] = $value;
                        $que_arr['question_showing'] = preg_replace('/\s*\R\s*/', ' ', trim(preg_replace('/[ \t]+/', ' ', preg_replace('/[\r\n]+/', "\n", $striped))));
                        if (!empty($questionImgs)) {
                            if ($questionImgs[$key] != '') {
                                $que_arr['has_img'] = 1;
                            }else{
                                $que_arr['has_img'] = 0;
                            }
                        }
                        $this->db->where('que_id', $que_id);
		                $this->db->update('questions', $que_arr);
                        if (!empty($questionImgs)) {
                            # exisiting image deleting process
                            $qImg = $this->Common_modal->getImages('questions','que_id',$que_id); 
                            if (!empty($qImg)) {
                                foreach ($qImg as $row) {
                                    $this->Common_modal->delete('photo','pid',$row->pid);
                                }
                            }
                            ####################################
                            if ($questionImgs[$key] != '') {
                                $quePhoData = array(
                                    'table' => 'questions',
                                    'field' => 'que_id',
                                    'field_id' => $que_id,
                                    'photo_path' => $questionImgs[$key],
                                    'photo_title' => 'question_image',
                                    'photo_order' => 1
                                );
                                $this->db->insert('photo',$quePhoData);
                            }
                        } 
                        if (!empty($answers)) {
                            # exisiting image deleting process
                            $answer_ids = $this->Common_modal->getAllWhereStr('question_answers','que_id',$que_id);
                            $_ids = array_column($answer_ids,'qa_id');
                            $existAnsImgs = $this->Common_modal->getImagesMultiIds('question_answers','qa_id',$_ids);
                            if (!empty($existAnsImgs)) {
                                foreach ($existAnsImgs as $row) {
                                    $this->Common_modal->delete('photo','pid',$row->pid);
                                }
                            }
                            ####################################
                            $this->db->where_in('qa_id',$_ids);
                            $this->db->delete('question_answers');

                            foreach ($answers[$key] as $ans => $answer) {
                                if (!empty($correctanswer)) {
                                    if (!isset($correctanswer[$key][$ans])) {
                                        $correct_answer = 0;
                                    }else{
                                        $correct_answer = $correctanswer[$key][$ans];
                                    }
                                }else{
                                    $correct_answer = null;
                                }
                                $answer_arr = array(
                                    'que_id' => $que_id, 
                                    'answer' => $answer, 
                                    'correct_answer' => $correct_answer,
                                );
    
                                if (!empty($answerImgs)) {
                                    if ($answerImgs[$key][$ans] != '') {
                                        $answer_arr['has_img'] = 1;
                                    }else{
                                        $answer_arr['has_img'] = 0;
                                    }
                                }
                                $this->db->insert('question_answers',$answer_arr);
                                $ans_id = $this->db->insert_id();
                                if (!empty($answerImgs)) {
                                    if ($answerImgs[$key][$ans] != '') {
                                        $ansPhoData = array(
                                            'table' => 'question_answers',
                                            'field' => 'qa_id',
                                            'field_id' => $ans_id,
                                            'photo_path' => $answerImgs[$key][$ans],
                                            'photo_title' => 'answer_image',
                                            'photo_order' => 1
                                        );
                                        $this->db->insert('photo',$ansPhoData);
                                    }
                                }
                            }
                        }
                    }
                }
            $this->db->trans_complete();
        }
    }

    public function getSingleQuestion($que_id) {
        $this->db->select('q.*');
        $this->db->from('questions q');
        $this->db->where('q.que_id',$que_id);
        $this->db->limit(1);
        $q = $this->db->get();
        if ($q->num_rows() == 1) {
            $main = $q->row();
            if ($main->has_img == 1) {
                $this->db->select('qp.photo_path as que_pic');
                $this->db->from('photo qp');
                $this->db->where('qp.table="questions" AND qp.field="que_id"');
                $this->db->where('qp.field_id',$main->que_id);
                $this->db->limit(1);
                $qp = $this->db->get();
                $main->que_pic = $qp->row()->que_pic;
            }else{
                $main->que_pic = '';
            }
            $this->db->select('qa.qa_id,qa.answer,qa.correct_answer,qa.has_img');
            $this->db->from('question_answers qa');
            $this->db->where('qa.que_id',$main->que_id);
            $qa = $this->db->get();
            $main->answers = $qa->result();
            foreach ($main->answers as $ans) {
                if ($ans->has_img == 1) {
                    $this->db->select('pa.photo_path as ans_pic');
                    $this->db->from('photo pa');
                    $this->db->where('pa.table="question_answers" AND pa.field="qa_id"');
                    $this->db->where('pa.field_id',$ans->qa_id);
                    $this->db->limit(1);
                    $pa = $this->db->get();
                    $ans->ans_pic = $pa->row()->ans_pic;
                }else{
                    $ans->ans_pic = '';
                }
            }
        }else{
            $main = false;
        }
        return $main;
    }
    
    public function generateQuestions($paperId,$class_id,$sub_id,$question_from,$previousPaperQue,$questionLimit,$questionType) {
        $this->db->select('q.que_id');
        $this->db->from('questions q');
        $this->db->where('q.class_id', $class_id);
        $this->db->where('q.subject', $sub_id);
        $this->db->where_in('q.exam_type', $question_from);
        $this->db->where('q.qt_id', $questionType);
        if ($previousPaperQue == 0 && !empty($this->existQueIds)) {
            $this->db->where_not_in('q.que_id', $this->existQueIds);
        }
        $this->db->limit($questionLimit);
        $this->db->order_by('q.que_id','RANDOM');
        $q = $this->db->get();
        $resultSet = $q->result();
        
        $mainQueList = [];
        foreach ($resultSet as $row) {
            $singleQue = array(
                'paper_id' => $paperId,
                'question_id' => $row->que_id,
                'que_type' => $questionType
            );
            $mainQueList[] = $singleQue;
        }
        if (!empty($mainQueList)) {
            $this->db->insert_batch('question_paper_child', $mainQueList);
        }
        
        $addedRecordCount = $this->db->select('question_id')
                  ->get_where('question_paper_child', array('paper_id' => $paperId, 'que_type' => $questionType))
                  ->num_rows();
        return ['addedRecordCount' => $addedRecordCount];
    }

    public function existQuestion($paperId=0) {
        $this->db->select('question_id');
        $this->db->from('question_paper_child');
        if ($paperId != 0) {
            $this->db->where('paper_id', $paperId);
        }
        $q = $this->db->get();
        $result = $q->result();
        return array_unique(array_column($result, 'question_id'));
    }

    public function loadGeneratedPapers() {
        $group_id = $this->session->userdata['staff_logged_in']['group_id'];
        $userId = $this->session->userdata['staff_logged_in']['user_id'];
        
        $own_papers = $this->Admin_modal->isAccessRightGiven($group_id,280) ? 1 : 0;
        $all_papers = $this->Admin_modal->isAccessRightGiven($group_id,281) ? 1 : 0;

        $this->db->select('et.*');
        $this->db->from('exam_types et');
        $q = $this->db->get();
        $main = $q->result();
        foreach ($main as $row) {
            $this->db->select('q.*,c.class_name,s.subject_name,s.subject_code,CONCAT_WS(" ",su.fname, '.', su.lname) as added_person,su.user_id,su.access_group');
            $this->db->from('question_paper_main q');
            $this->db->where('q.term_id',$row->extype_id);
            if (!$all_papers) { // when this doens't have the all questions permission, only it will select own person paper list.
                $this->db->where('q.added_by', $userId);
            }
            $this->db->join('class c','c.class_id=q.class_id');
            $this->db->join('subjects s','s.sub_id=q.subject_id');
            $this->db->join('staff_users su','su.user_id=q.added_by');
            $this->db->order_by('q.paper_id','DESC');
            $que = $this->db->get();
            $row->papers = $que->result();
        }
        return $main;
    }

    public function getSingleQuestionPaper($paperId) {
        $this->db->select('q.*,c.class_name,s.subject_name,s.subject_code,CONCAT_WS(" ",su.fname, '.', su.lname) as added_person,su.user_id,su.access_group,et.extype_name');
        $this->db->from('question_paper_main q');
        $this->db->where('q.paper_id', $paperId);
        $this->db->join('class c','c.class_id=q.class_id');
        $this->db->join('subjects s','s.sub_id=q.subject_id');
        $this->db->join('staff_users su','su.user_id=q.added_by');
        $this->db->join('exam_types et','et.extype_id=q.term_id');
        $this->db->limit(1);
        $q= $this->db->get();
        $ret = false;
        if ($q->num_rows() > 0) {
            $ret = $q->row();
        }
        return $ret;
    }

    public function getQuestionPaperQuestions($paperId) {
        $this->db->select('qt.*');
        $this->db->from('question_type qt');
        $q = $this->db->get();
        $main = $q->result();
        foreach ($main as $row) {
            $this->db->select('qp.question_id');
            $this->db->from('question_paper_child qp');
            $this->db->where('qp.paper_id', $paperId);
            $this->db->where('qp.que_type', $row->qt_id);
            $que = $this->db->get();
            $row->questions = $que->result();
        }
        return $main;
    }

    public function filerQuestions($classId,$subjectId,$termIds) {
        $this->db->select('qt.*');
        $this->db->from('question_type qt');
        $q = $this->db->get();
        $main = $q->result();
        $questions = [];
        foreach ($main as $row) {
            $this->db->select('');
            $this->db->from('questions q');
            $this->db->where('q.qt_id', $row->qt_id);
            $this->db->where('q.class_id', $classId);
            $this->db->where('q.subject', $subjectId);
            $this->db->where_in('q.exam_type', $termIds);
            $ques = $this->db->get();
            $questions[strtolower($row->question_type)] = $ques->result();
        }
        return $questions;
    }

    public function updateExamPaper($paperId,$qArr,$questionType) {
        $this->db->trans_start();
        $type = TRUE;

        $_array = array();
        $exist_ids = array();
        foreach ($questionType as $key => $qt) {
            foreach ($qArr[strtolower($qt->question_type)] as $k => $que) {
                $_arr = array(
                    'paper_id' => $paperId, 
                    'question_id' => $que,
                    'que_type' => $qt->qt_id, 
                );
                $result = $this->Common_modal->checkExistForUpdate('qpc_id','question_paper_child',$_arr);
                if ($result) {
                    $exist_ids[] = $result->qpc_id;
                }else{
                    $_array[] = $_arr; 
                } 
            }
        }
        
        if ($type) {
            $this->db->where('paper_id', $paperId); 
            if (!(empty($exist_ids))) {
                $this->db->where_not_in('qpc_id', $exist_ids);
            }
            $this->db->delete('question_paper_child');
        }
        if (!(empty($_array))) {
            $this->db->insert_batch('question_paper_child', $_array);       
        }
        $this->db->trans_complete();
        return $paperId; 
    }

    public function viewQuestionPaper($paperId) {
        $this->db->select('q.*,c.class_name,s.subject_name,s.subject_code,CONCAT_WS(" ",su.fname, '.', su.lname) as added_person,su.user_id,su.access_group,et.extype_name,ay.year');
        $this->db->from('question_paper_main q');
        $this->db->where('q.paper_id', $paperId);
        $this->db->join('class c','c.class_id=q.class_id');
        $this->db->join('subjects s','s.sub_id=q.subject_id');
        $this->db->join('staff_users su','su.user_id=q.added_by');
        $this->db->join('exam_types et','et.extype_id=q.term_id');
        $this->db->join('acadamic_year ay','ay.ay_id=q.year');
        $this->db->limit(1);
        $q= $this->db->get();
        $ret = false;
        if ($q->num_rows() > 0) {
            $ret = $q->row_array();
            $queIds = $this->existQuestion($paperId);
            $question_types = $this->Common_modal->getAll('question_type');
            foreach ($question_types as $qt) {
                $ret[strtolower($qt->question_type).'_ques_ans'] = $this->getQuestionsAndAnswers($queIds,$qt->qt_id); // MCQ Questions
            }
        }
        return $ret;
    }

    public function getQuestionsAndAnswers($queIds,$questionType) {
        $this->db->select('q.que_id,q.question,q.answer_method,q.qt_id,q.has_img as queHasImg,pq.photo_path as questionImage');
        $this->db->from('questions q');
        $this->db->where_in('q.que_id', $queIds);
        $this->db->where('q.qt_id', $questionType);
        $this->db->join('photo pq','pq.table="questions" AND pq.field_id = q.que_id', 'left outer');
        $this->db->order_by('q.que_id', 'ASC');
        $q = $this->db->get();
        $main = $q->result();
        foreach ($main as $row) {
            $this->db->select('qa.qa_id,qa.answer,qa.has_img as ansHasImg,pa.photo_path as answerImage,qa.correct_answer');
            $this->db->from('question_answers qa');
            $this->db->where('qa.que_id', $row->que_id);
            $this->db->join('photo pa','pa.table="question_answers" AND pa.field_id=qa.qa_id', 'left outer');
            $this->db->order_by('qa.qa_id', 'ASC');
            $qa = $this->db->get();
            $row->answerHasImgs = array_unique(array_column($qa->result(), 'ansHasImg'));
            $row->answers = $qa->result();
        }
        return $main;
    }
}
?>