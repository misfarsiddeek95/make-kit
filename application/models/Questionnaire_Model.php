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
        
        $own_questions = $this->Admin_modal->isAccessRightGiven($group_id,150) ? 1 : 0;
        $all_questions = $this->Admin_modal->isAccessRightGiven($group_id,151) ? 1 : 0;

        $this->db->select('qt.*');
        $this->db->from('question_type qt');
        $this->db->where('qt.status', 1);
        $q = $this->db->get();
        $main = $q->result();
        foreach ($main as $row) {
            $this->db->select('q.que_id,q.question,q.question_showing,q.added_date,q.answer_method,q.has_img,et.extype_name,s.subject_name,s.subject_code,CONCAT_WS(" ",su.fname, '.', su.lname) as added_person,su.user_id,su.access_group, GROUP_CONCAT(c.class_name SEPARATOR ", ") as class_name');
            $this->db->from('questions q');
            $this->db->where('q.qt_id',$row->qt_id);
            if (!$all_questions) { // when this doens't have the all questions permission, only it will select own person question list.
                $this->db->where('q.added_by', $userId);
            }
            $this->db->join('question_classes qc','qc.que_id=q.que_id', 'left');
            $this->db->join('class c','c.class_id=qc.class_id', 'left');
            
            $this->db->join('exam_types et','et.extype_id=q.exam_type');
            $this->db->join('subjects s','s.sub_id=q.subject');
            $this->db->join('staff_users su','su.user_id=q.added_by');
            
            $this->db->group_by('q.que_id');
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

    public function getCommonSubjects($class_ids) {
        if (empty($class_ids)) return [];
        if (!is_array($class_ids)) $class_ids = [$class_ids];

        $common_subjects = null;

        foreach ($class_ids as $class_id) {
            $this->db->select('cs.subject_id, s.subject_name');
            $this->db->from('class_subjects cs');
            $this->db->where('cs.class_id', $class_id);
            $this->db->join('subjects s', 's.sub_id = cs.subject_id');
            $q = $this->db->get();
            $subjects = $q->result_array();

            $subject_ids = array_column($subjects, 'subject_id');
            
            // Create map for easy retrieval of name
            $subject_map = [];
            foreach($subjects as $s) {
                $subject_map[$s['subject_id']] = $s;
            }

            if ($common_subjects === null) {
                $common_subjects = $subject_ids;
                $final_map = $subject_map;
            } else {
                $common_subjects = array_intersect($common_subjects, $subject_ids);
                // Keep the map intersection
                $final_map = array_intersect_key($final_map, array_flip($common_subjects));
            }
        }

        $result = [];
        if ($common_subjects) {
            foreach($common_subjects as $sid) {
                if(isset($final_map[$sid])) {
                    $result[] = (object) $final_map[$sid]; // Return as object to match loadClassSubjects
                }
            }
        }
        return $result;
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

                    // FIX: Extract class_id_array and unset it from que_arr before insert
                    $class_id_array = isset($que_arr['class_id_array']) ? $que_arr['class_id_array'] : [];
                    unset($que_arr['class_id_array']);

                    $this->db->insert('questions',$que_arr);
                    $que_id =  $this->db->insert_id();

                    if (!empty($class_id_array)) { // Insert into link table
                        $batch_data = [];
                        foreach($class_id_array as $c_id) {
                            $batch_data[] = [
                                'que_id' => $que_id,
                                'class_id' => $c_id
                            ];
                        }
                        if(!empty($batch_data)) {
                             $this->db->insert_batch('question_classes', $batch_data);
                        }
                    }
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
                        // Remove class_id_array before update questions table as it is not a column there
                        $class_ids_update = isset($que_arr['class_id_array']) ? $que_arr['class_id_array'] : [];
                        unset($que_arr['class_id_array']);
                        
		                $this->db->update('questions', $que_arr);
                        
                        // Update question_classes table (Selective Update)
                        $batch_data = [];
                        $exist_ids = [];
                        
                        if (!empty($class_ids_update) && is_array($class_ids_update)) {
                             foreach($class_ids_update as $c_id) {
                                 $check_arr = [
                                     'que_id' => $que_id,
                                     'class_id' => $c_id
                                 ];
                                 
                                 // Check if this link already exists
                                 $result = $this->Common_modal->checkExistForUpdate('id', 'question_classes', $check_arr);
                                 
                                 if ($result) {
                                     $exist_ids[] = $result->id; // Keep this relationship
                                 } else {
                                     $batch_data[] = $check_arr; // Add new relationship
                                 }
                             }
                        }
                        
                        // Delete removed links
                        $this->db->where('que_id', $que_id);
                        if (!empty($exist_ids)) {
                             $this->db->where_not_in('id', $exist_ids);
                        }
                        $this->db->delete('question_classes');

                        // Insert new links
                        if(!empty($batch_data)) {
                             $this->db->insert_batch('question_classes', $batch_data);
                        }
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

            // Get associated classes
            $this->db->select('class_id');
            $this->db->from('question_classes');
            $this->db->where('que_id', $main->que_id);
            $qc_query = $this->db->get();
            $main->class_ids = array_column($qc_query->result_array(), 'class_id');

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
        $this->db->join('question_classes qc', 'qc.que_id = q.que_id'); // Join with link table
        $this->db->where('qc.class_id', $class_id); // Filter by linked class_id
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
        $this->db->where('qt.status', 1);
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
            $questions[strtolower($row->question_type_english)] = $ques->result();
        }
        return $questions;
    }

    public function updateExamPaper($paperId,$qArr,$questionType) {
        $this->db->trans_start();
        $type = TRUE;

        $_array = array();
        $exist_ids = array();
        foreach ($questionType as $key => $qt) {
            foreach ($qArr[strtolower($qt->question_type_english)] as $k => $que) {
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
            $ret = $q->row_array();
            $queIds = $this->existQuestion($paperId);
            $question_types = $this->Common_modal->getAll('question_type');
            foreach ($question_types as $qt) {
                if (!empty($queIds)) {
                    $ret[strtolower($qt->question_type_english) . '_ques_ans'] = $this->getQuestionsAndAnswers($queIds, $qt->qt_id);
                } else {
                    $ret[strtolower($qt->question_type_english) . '_ques_ans'] = [];
                }
            }
        }
        return $ret;
    }

    public function getQuestionsAndAnswers($queIds,$questionType) {
        if (empty($queIds)) {
            return [];  // or return false, whatever fits your logic
        }
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

    public function get_total_scores_all_students() {
        $this->db->select('sp.student_id, SUM(sp.points) as total_points');
        $this->db->from('student_points sp');
        $this->db->where('sp.attempt_id = (
            SELECT MAX(attempt_id)
            FROM student_points
            WHERE student_id = sp.student_id
              AND paper_id = sp.paper_id
        )', null, false);
        $this->db->group_by('sp.student_id');
    
        $query = $this->db->get();
        return $query->result(); // returns [{student_id, total_points}, ...]
    }    

    public function update_all_students_points() {
        $sql = "
            UPDATE external_users eu
            LEFT JOIN (
                SELECT sp.student_id, SUM(sp.points) AS total_points
                FROM student_points sp
                INNER JOIN question_paper_main q ON sp.paper_id = q.paper_id
                WHERE q.status = 1  -- only consider active papers
                AND sp.attempt_id = (
                    SELECT MAX(sp2.attempt_id)
                    FROM student_points sp2
                    WHERE sp2.student_id = sp.student_id
                        AND sp2.paper_id = sp.paper_id
                )
                GROUP BY sp.student_id
            ) AS scores ON eu.id = scores.student_id
            SET eu.points_earned = IFNULL(scores.total_points, 0)
        ";

        return $this->db->query($sql);
    }

}
?>