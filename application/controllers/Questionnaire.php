<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Questionnaire extends Admin_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model("Common_modal");
        $this->load->model("Admin_modal"); 
        $this->load->model("Questionnaire_Model"); 
        $this->load->library("Aayusmain");
    }

    public function questions() {
        try {
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $manage_main = $this->Admin_modal->isAccessRightGiven($group_id,95)?0:1;
            if ($manage_main) {
                throw new Exception("אין לך הרשאה לנהל שאלות.");
            }

            $data['question_list'] = $this->Admin_modal->isAccessRightGiven($group_id,96)?1:0;
            $data['add'] = $this->Admin_modal->isAccessRightGiven($group_id,97)?1:0;
            $data['view'] = $this->Admin_modal->isAccessRightGiven($group_id,98)?1:0;
            $data['edit'] = $this->Admin_modal->isAccessRightGiven($group_id,99)?1:0;
            $data['delete'] = $this->Admin_modal->isAccessRightGiven($group_id,100)?1:0;

            $data['question_types'] = $this->Common_modal->get_all_selected_fields('*','question_type',['status' => 1]);
            $data['questions'] = $this->Questionnaire_Model->loadQuestions();

            $this->load->view('questions',$data);

        } catch (Exception $x) {
            redirect(base_url());
        }
    }

    public function addQuestion() {
        try {
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $add = $this->Admin_modal->isAccessRightGiven($group_id,97)?0:1;
            $edit = $this->Admin_modal->isAccessRightGiven($group_id,99)?0:1;

            if (isset($_POST['que_id'])) {
                if ($edit) {
                    throw new Exception("אין לך הרשאה לערוך שאלה.");
                }
                $data['type']='עריכת';
                $data['question_detail'] = $this->Questionnaire_Model->getSingleQuestion($this->input->post('que_id'));
            }else{
                if ($add) {
                    throw new Exception("אין לך הרשאה להוסיף שאלה.");
                }
                $data['type']='הוספת';
            }
            $data['class'] = $this->Common_modal->getAll('class');
            $data['terms'] = $this->Common_modal->getAll('exam_types');
            $data['question_type'] = $this->Common_modal->get_all_selected_fields('*','question_type',['status' => 1]);

            $this->load->view('add_questions',$data);

        } catch (Exception $x) {
            redirect(base_url());
        }
    }

    public function loadClassSubjects() {
        $class_id = $this->input->post('class_id');
        // Handle both single value and array
        $class_ids = is_array($class_id) ? $class_id : [$class_id];
        $result = $this->Questionnaire_Model->getCommonSubjects($class_ids);
        echo json_encode($result);
    }

    public function uploadQuestionaireImage() {
        try {
            if(isset($_FILES['questionaireImg'])){
                if (!empty($_FILES)) {
                    $PhotoFileName = $_FILES['questionaireImg']['name'];
                    $file_tmp = $_FILES['questionaireImg']['tmp_name'];
                    $exten = basename($_FILES['questionaireImg']['type']); 
                    $folder = $this->folder."/photos/questionaire/";
                    if(!is_dir($folder)){
                        mkdir($folder, 0755, true);
                    }
                    $PhotoFileNameMD5 = md5(date('YmdHis').$PhotoFileName);
                    $targetFilePath = $folder . $PhotoFileNameMD5.'.'.$exten; 
                    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                    $extensions = array("jpeg","jpg","png");

                    if(in_array($fileType,$extensions)=== false){
                        $this->output->set_header("HTTP/1.0 400 Bad Request");
                        throw new Exception("סיומת הקובץ לא מורשית, אנא בחר קובץ JPEG או PNG.");
                    }

                    if(move_uploaded_file($_FILES["questionaireImg"]["tmp_name"], $targetFilePath)){
                        $msg = array('status' => 'success', 'message'=>'התמונה הועלתה בהצלחה.','uploadedImage' => $PhotoFileNameMD5.'.'.$exten);
                    }else{
                        $this->output->set_header("HTTP/1.0 400 Bad Request");
                        throw new Exception("הקובץ ריק.");
                    }
                }else{
                    throw new Exception("הקובץ ריק.");
                }
            }else{
                throw new Exception("תמונה לא הוגדרה.");
            }
        } catch (Exception $x) {
            $msg = array('status' => 'success', 'message'=> $x->getMessage());
        }
        echo json_encode($msg);
    }

    public function removeQuestionaireImage() {
        try {
            $image_name = $this->input->post('image');
            $folder = $this->folder."/photos/questionaire/";
            if (file_exists( $folder.$image_name)) {
                unlink( $folder . $image_name );
                $picset = $this->Common_modal->getAllWhere('photo','photo_path',$image_name);
                if (!empty($picset)) {
                    if ($picset->table == 'questions' && $picset->field == 'que_id') {
                        $val['has_img'] = 0;
                        $this->Common_modal->update('que_id',$picset->field_id,'questions',$val);
                    }else if ($picset->table == 'question_answers' && $picset->field == 'qa_id'){
                        $val['has_img'] = 0;
                        $this->Common_modal->update('qa_id',$picset->field_id,'question_answers',$val);
                    }
                    $this->Common_modal->delete('photo','photo_path',$image_name);
                }
                $msg = array('status' => 'success', 'message'=>'התמונה הוסרה בהצלחה.');
            }else{
                throw new Exception("תמונה זו אינה קיימת.");
            }
        } catch (Exception $x) {
            $msg = array('status' => 'error', 'message'=> $x->getMessage());
        }
        echo json_encode($msg);
    }

    public function saveQuestions() {
        try {
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $user_id = $this->session->userdata['staff_logged_in']['user_id'];
            $add = $this->Admin_modal->isAccessRightGiven($group_id,97)?0:1;
            $edit = $this->Admin_modal->isAccessRightGiven($group_id,99)?0:1;
            
            $que_id = $this->input->post('que_id');
            $class_ids = $this->input->post('class_id'); // Now EXPECTING an array
            // Ensure class_ids is an array
            if (!is_array($class_ids)) {
                 $class_ids = [$class_ids];
            }

            $sub_id = $this->input->post('sub_id');
            $term_id = $this->input->post('term_id');
            $qt_id = $this->input->post('qt_id');
            $date = date('Y-m-d H:i:s');

            // VALIDATION: Check if subject is valid for all selected classes
            $common_subjects = $this->Questionnaire_Model->getCommonSubjects($class_ids);
            $valid_subject = false;
            foreach($common_subjects as $subj) {
                if ($subj->subject_id == $sub_id) {
                    $valid_subject = true;
                    break;
                }
            }
            if (!$valid_subject) {
                throw new Exception("המקצוע שנבחר אינו זמין עבור כל המוסדות שנבחרו.");
            }

            switch ($qt_id) {
                case 1:
                    $answer_method = $this->input->post('mcqanswermethods');
                    break;
                case 2:
                    $answer_method = $this->input->post('structuredanswermethod');
                    break;
                case 3:
                    $answer_method = 'textbox';
                    break;
                default:
                    $answer_method = '';
                    break;
            }

            $que_arr = array(
                'class_id' => $class_ids[0], // Set primary class_id to first selection to satisfy DB constraint
                'class_id_array' => $class_ids, // Pass full array for model to handle link table
                'subject' => $sub_id, 
                'exam_type' => $term_id, 
                'qt_id' => $qt_id, 
                'answer_method' => $answer_method,
                'added_by' => $user_id, 
                'added_date' => $date, 
            );

            if (isset($_POST['questions'])){
                $questions = $this->input->post('questions');
            }else{
                $questions = array();
            }

            if (isset($_POST['answers'])){
                $answers = $this->input->post('answers');
            }else{
                $answers = array();
            }

            if ($qt_id == 1) {
                if (isset($_POST['correctanswer'])){
                    $correctanswer = $this->input->post('correctanswer');
                }else{
                    $correctanswer = array();
                }
            }else{
                $correctanswer = array();
            }

            if ($qt_id == 1 || $qt_id == 2) {
                if (isset($_POST['questionImgs'])){
                    $questionImgs = $this->input->post('questionImgs');
                }else{
                    $questionImgs = array();
                }
                if ($answer_method == 'single' || $answer_method == 'multiple' || $answer_method == 'smallbox') {
                    if (isset($_POST['answerImgs'])){
                        $answerImgs = $this->input->post('answerImgs');
                    }else{
                        $answerImgs = array();
                    }
                }else{
                    $answerImgs = array();
                }
            }else{
                $questionImgs = array();
                $answerImgs = array();
            }

            if ($que_id != 0) {
                if ($edit) {
                    throw new Exception("אין לך הרשאה לערוך שאלות.");
                }
                $type = 'update';
            }else{
                if ($add) {
                    throw new Exception("אין לך הרשאה לשמור שאלות.");
                }
                $type = 'save';
            }

            if ($que_id == 0) {
                # SAVE FUNCTION SHOULD BE HERE.......
                $this->Questionnaire_Model->save_questions($que_id,$que_arr,$questions,$answers,$correctanswer,$questionImgs,$answerImgs);
            }else{
                $this->Questionnaire_Model->update_question($que_id,$que_arr,$questions,$answers,$correctanswer,$questionImgs,$answerImgs);
            }
            $action_hebrew = ($type == 'save') ? 'נשמרה' : 'עודכנה';
            $msg = array('status' => 'success', 'message' => 'שאלה '.$action_hebrew.' בהצלחה.');
        } catch (Exception $x) {
            $msg = array('status' => 'error', 'message' => $x->getMessage());
        }
        echo json_encode($msg);
    }

    public function viewQuestion($qId) {
        $data['question_detail'] = $this->Questionnaire_Model->getSingleQuestion($qId);
        $this->load->view('view_question',$data);
    }

    public function deleteQuestion() {
        try {
            $que_id = $this->input->post('que_id');
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $delete_ = $this->Admin_modal->isAccessRightGiven($group_id,100)?1:0;
            if ($delete_) {
                $_delete = $this->Common_modal->delete('questions','que_id',$que_id);
                if ($_delete) { 
                    $qImg = $this->Common_modal->getImages('questions','que_id',$que_id); 
                    if ($qImg) {
                        foreach ($qImg as $row) {
                            $this->Common_modal->delete('photo','pid',$row->pid);
                            $folder = $this->folder."/photos/questionaire/";
                            unlink( $folder . $row->photo_path);
                        }
                    }
                    $q_ans = $this->Common_modal->getAllWhereStr('question_answers','que_id',$que_id);
                    if ($q_ans) {
                        foreach ($q_ans as $row) {
                            $aImg = $this->Common_modal->getImages('question_answers','qa_id',$row->qa_id); 
                            if ($aImg) {
                                foreach ($aImg as $row) {
                                    $this->Common_modal->delete('photo','pid',$row->pid);
                                    $folder = $this->folder."/photos/questionaire/";
                                    unlink( $folder . $row->photo_path);
                                }
                            }
                        }
                        $this->Common_modal->delete('question_answers','que_id',$que_id);
                        $this->Common_modal->delete('question_classes','que_id',$que_id);
                    }
                    $msg = array("status" => "success","message" => "השאלה נמחקה בהצלחה.");
                }else{
                    throw new Exception("לא ניתן למחוק שאלה זו.");
                }
            }else {
                throw new Exception("אין לך הרשאה למחוק שאלה.");
            }
        } catch (Exception $x) {
            $msg = array("status" => "error","message" => $x->getMessage());
        }
        echo json_encode($msg);
    }

    // Paper Generation
    public function generateQuestionPaper() {
        try {
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $manage_main = $this->Admin_modal->isAccessRightGiven($group_id,144)?0:1;
            if ($manage_main) {
                throw new Exception("אין לך הרשאה לנהל הפקת דפים.");
            }

            $data['list'] = $this->Admin_modal->isAccessRightGiven($group_id,145) ? 1 : 0;
            $data['add'] = $this->Admin_modal->isAccessRightGiven($group_id,146) ? 1 : 0;
            $data['view'] = $this->Admin_modal->isAccessRightGiven($group_id,147) ? 1 : 0;
            $data['delete'] = $this->Admin_modal->isAccessRightGiven($group_id,148) ? 1 : 0;
            $data['edit'] = $this->Admin_modal->isAccessRightGiven($group_id,149) ? 1 : 0;
            $data['change_status'] = $this->Admin_modal->isAccessRightGiven($group_id,155) ? 1 : 0;
            
            $data['exam_types'] = $this->Common_modal->getAll('exam_types');
            $data['class'] = $this->Common_modal->getAll('class');
            $data['question_type'] = $this->Common_modal->get_all_selected_fields('*','question_type',['status' => 1]);
            $data['get_all_papers'] = $this->Questionnaire_Model->loadGeneratedPapers();

            $this->load->view('paper_list',$data);

        } catch (Exception $x) {
            redirect(base_url());
        }
    }

    public function generateExamPaper() {
        try {
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $has_permission = $this->Admin_modal->isAccessRightGiven($group_id,146)?0:1;
            if ($has_permission) {
                throw new Exception("אין לך הרשאה להפיק דף שאלות.");
            }

            $schoolName = $this->input->post('school_name');
            $duration = $this->input->post('duration');
            $marks_count = $this->input->post('marks_count');
            $term_id = $this->input->post('term_id');
            $class_id = $this->input->post('class_id');
            $sub_id = $this->input->post('sub_id');
            $attempts = $this->input->post('attempts');

            $added_by = $this->session->userdata['staff_logged_in']['user_id'];
            $created_at = date('Y-m-d');
            
            $mcqLimit = $this->input->post('mcq');
            $structuredLimit = $this->input->post('structured');
            $essayLimit = $this->input->post('essay');

            $mcqScore = $this->input->post('mcq_score');
            $structuredScore = $this->input->post('structured_score');
            $essayScore = $this->input->post('essay_score');

            if (isset($_POST['questions_from'])) {
                $question_from = $this->input->post('questions_from');
            } else {
                $question_from = array();
            }
            $previousPaperQue = $this->input->post('previousPaperQue'); // 0 : No, 1 : Yes

            $mcqMainTitle = trim($this->input->post('mcq_main_title'));
            $structuredMainTitle = trim($this->input->post('structured_main_title'));
            $essayMainTitle = trim($this->input->post('essay_main_title'));

            $PhotoFileNameMD5 = '';
            $filetype = '';

            $arr = array(
                'school_name' => $schoolName, 
                'paper_duration' => $duration,
                'total_marks_count' => $marks_count,
                'term_id' => $term_id,
                'class_id' => $class_id,
                'subject_id' => $sub_id,
                'no_of_mcqs' => $mcqLimit,
                'score_per_mcq' => $mcqScore,
                'no_of_structured' => $structuredLimit,
                'score_per_structure' => $structuredScore,
                'no_of_essays' => $essayLimit,
                'score_per_essay' => $essayScore,
                'selected_ques_from' => implode(',', $question_from),
                'need_previous_added' => $previousPaperQue,
                'mcq_main_title' => $mcqMainTitle != '' ? $mcqMainTitle : null,
                'structured_main_title' => $structuredMainTitle != '' ? $structuredMainTitle : null,
                'essay_main_title' => $essayMainTitle != '' ? $essayMainTitle : null,
                'no_of_attempts' => $attempts,
                'added_by' => $added_by
            );

            if (isset($_FILES['fileUpload'])) {
                if (!empty($_FILES['fileUpload']["name"])) {
                    $PhotoFileName = $_FILES['fileUpload']['name'];
                    $PhotoFileNameMD5 = md5(date('YmdHis').$PhotoFileName);
                    $filetype = pathinfo($PhotoFileName, PATHINFO_EXTENSION);
                    $extensions= array("jpeg","jpg","png", 'svg');
                    if(!in_array($filetype,$extensions)){
                        throw new Exception("סיומת לוגו בית הספר לא מורשית, אנא בחר קובץ JPEG או PNG.");
                    }
                    $folder = $this->folder."/photos/exam_papers/";
                    if(!is_dir($folder)){
                        mkdir($folder, 0777, true);
                    }
                    $img_org = $folder.$PhotoFileNameMD5.'.'.$filetype;
                    if (!@move_uploaded_file ($_FILES['fileUpload']['tmp_name'],$img_org)) throw new Exception('לא ניתן להעלות את הקובץ המקורי...');
                }
            }

            if ($PhotoFileNameMD5!='') {
                $arr['school_logo'] = "photos/exam_papers/".$PhotoFileNameMD5.'.'.$filetype;
            }
            $paperId = 0;
            if (($mcqLimit != '' && $mcqLimit != null && $mcqLimit != 0) || ($structuredLimit != '' && $structuredLimit != null && $structuredLimit != 0) || ($essayLimit != '' && $essayLimit != null && $essayLimit != 0)) {
                $paperId = $this->Common_modal->insert('question_paper_main', $arr);
            }

            $queMessage = [];
            // create mcq questions.
            if ($mcqLimit != '' && $mcqLimit != null && $mcqLimit != 0) {
                $createMcqQuestions = $this->Questionnaire_Model->generateQuestions($paperId,$class_id,$sub_id,$question_from,$previousPaperQue,$mcqLimit,1); // 1 : MCQ
                if ($createMcqQuestions['addedRecordCount'] == 0) {
                    $queMessage[] = 'לא ניתן ליצור שאלות אמריקאיות מכיוון שלא נמצא מידע ברשימת השאלות, או שהשאלה שנבחרה כבר בשימוש.';
                } else if ($createMcqQuestions['addedRecordCount'] != $mcqLimit) {
                    $queMessage[] = "הצלחנו להוסיף רק ".$createMcqQuestions['addedRecordCount']." שאלות אמריקאיות. הסיבה היא או חוסר במידע ברשימת השאלות האמריקאיות, או שרוב השאלות הרלוונטיות כבר היו בשימוש במבחנים קודמים. נא להוסיף שאלות חדשות. ערוך את המבחן וכלול בו את השאלות החדשות שהוספת.";
                }
            }

            // create structured questions.
            if ($structuredLimit != '' && $structuredLimit != null && $structuredLimit != 0) {
                $createStructuredQuestions = $this->Questionnaire_Model->generateQuestions($paperId,$class_id,$sub_id,$question_from,$previousPaperQue,$structuredLimit,2); // 2 : Structured
                if ($createStructuredQuestions['addedRecordCount'] == 0) {
                    $queMessage[] = 'לא ניתן ליצור שאלות פתוחות מכיוון שלא נמצא מידע ברשימת השאלות, או שהשאלה שנבחרה כבר בשימוש.';
                } else if ($createStructuredQuestions['addedRecordCount'] != $structuredLimit) {
                    $queMessage[] = "הצלחנו להוסיף רק ".$createStructuredQuestions['addedRecordCount']." שאלות פתוחות. הסיבה היא או חוסר במידע ברשימת השאלות הפתוחות, או שרוב השאלות הרלוונטיות כבר היו בשימוש במבחנים קודמים. נא להוסיף שאלות חדשות. ערוך את המבחן וכלול בו את השאלות החדשות שהוספת.";
                }
            }

            // create essay questions.
            if ($essayLimit != '' && $essayLimit != null && $essayLimit != 0) {
                $createEssayQuestions = $this->Questionnaire_Model->generateQuestions($paperId,$class_id,$sub_id,$question_from,$previousPaperQue,$essayLimit,3); // 3 : Essay
                if ($createEssayQuestions['addedRecordCount'] == 0) {
                    $queMessage[] = 'לא ניתן ליצור שאלות חיבור מכיוון שלא נמצא מידע ברשימת השאלות, או שהשאלה שנבחרה כבר בשימוש.';
                } else if ($createEssayQuestions['addedRecordCount'] != $essayLimit) {
                    $queMessage[] = "הצלחנו להוסיף רק ".$createEssayQuestions['addedRecordCount']." שאלות חיבור. הסיבה היא או חוסר במידע ברשימת שאלות החיבור, או שרוב השאלות הרלוונטיות כבר היו בשימוש במבחנים קודמים. נא להוסיף שאלות חדשות. ערוך את המבחן וכלול בו את השאלות החדשות שהוספת.";
                }
            }
            # Last added question.
            $lastAddedMainQue = $this->Questionnaire_Model->getSingleQuestionPaper($paperId);
            
            $msg = array("status" => "success","message" => 'הדף הופק בהצלחה.', 'queMessage' => $queMessage, 'last_added' => $lastAddedMainQue);
        } catch (Exception $x) {
            $msg = array("status" => "error","message" => $x->getMessage());
        }
        echo json_encode($msg);
    }

    public function editExamPaper($paperId) {
        try {
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $has_permission = $this->Admin_modal->isAccessRightGiven($group_id,149)?0:1;
            if ($has_permission) {
                throw new Exception("אין לך הרשאה לערוך דף שאלות שהופק.");
            }
            $paperId = base64_decode($paperId);
            
            $data['exam_types'] = $this->Common_modal->getAll('exam_types');
            $data['class'] = $this->Common_modal->getAll('class');
            $data['question_type'] = $this->Common_modal->get_all_selected_fields('*','question_type',['status' => 1]);
            $data['getQuestionPaper'] = $this->Questionnaire_Model->getSingleQuestionPaper($paperId);
            $data['getQuestionPaperQuestions'] = $this->Questionnaire_Model->getQuestionPaperQuestions($paperId);

            $this->load->view('edit_exam_paper', $data);
        } catch (Exception $x) {
            redirect(base_url('Questionnaire/generateQuestionPaper/274'));
        }
    }

    public function removeSchoolLogo() {
        try {
            $paperId = $this->input->post('paper_id');
            $fields = array('school_logo');
            $get_image = $this->Common_modal->getSingleField('question_paper_main',$fields,'paper_id',$paperId);
            if ($get_image->school_logo != '' && $get_image->school_logo != null) {
                $folder = $this->folder.'/';
                $imagename = $get_image->school_logo;
                unlink( $folder . $imagename );
                $data['school_logo'] = null;
                $this->Common_modal->update('paper_id',$paperId,'question_paper_main',$data);
                $msg = array('status' => 'success', 'message' => 'לוגו המוסד הוסר מהמבחן בהצלחה.');
            }
        } catch (Exception $ex) {
            $msg = array('status' => 'error', 'message' => $ex->getMessage());
        }
        echo json_encode($msg);
    }

    public function filterQuestions() {
        $classId = $this->input->post('classId');
        $subjectId = $this->input->post('subjectId');
        $termIds = $this->input->post('termIds');
        $response = $this->Questionnaire_Model->filerQuestions($classId,$subjectId,$termIds);
        echo json_encode($response);
    }

    public function updateExamPaper() {
        try {
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $manage_main = $this->Admin_modal->isAccessRightGiven($group_id,149)?0:1;
            if ($manage_main) {
                throw new Exception("אין לך הרשאה לעדכן הפקת דפים.");
            }

            $paperId = $this->input->post('paperId');
            $question_type = $this->Common_modal->getAll('question_type');

            $mcqMainTitle = trim($this->input->post('mcq_main_title'));
            $structuredMainTitle = trim($this->input->post('structured_main_title'));
            $essayMainTitle = trim($this->input->post('essay_main_title'));

            $attempts = $this->input->post('attempts');

            $PhotoFileNameMD5 = '';
            $filetype = '';
            $data_arr = array(
                'mcq_main_title' => $mcqMainTitle != '' ? $mcqMainTitle : null,
                'structured_main_title' => $structuredMainTitle != '' ? $structuredMainTitle : null,
                'essay_main_title' => $essayMainTitle != '' ? $essayMainTitle : null,
                'no_of_attempts' => $attempts != '' ? $attempts : null
            );
            if (isset($_FILES['fileUpload'])) {
                if (!empty($_FILES['fileUpload']["name"])) {
                    $PhotoFileName = $_FILES['fileUpload']['name'];
                    $PhotoFileNameMD5 = md5(date('YmdHis').$PhotoFileName);
                    $filetype = pathinfo($PhotoFileName, PATHINFO_EXTENSION);
                    $extensions= array("jpeg","jpg","png", 'svg');
                    if(!in_array($filetype,$extensions)){
                        throw new Exception("סיומת לוגו בית הספר לא מורשית, אנא בחר קובץ JPEG או PNG.");
                    }
                    $folder = $this->folder."/photos/exam_papers/";
                    if(!is_dir($folder)){
                        mkdir($folder, 0777, true);
                    }
                    $img_org = $folder.$PhotoFileNameMD5.'.'.$filetype;
                    if (!@move_uploaded_file ($_FILES['fileUpload']['tmp_name'],$img_org)) throw new Exception('לא ניתן להעלות קובץ מקורי...');
                }
            }

            if ($PhotoFileNameMD5!='') {
                $data_arr['school_logo'] = "photos/exam_papers/".$PhotoFileNameMD5.'.'.$filetype;
                $result = $this->Common_modal->getAllWhere('question_paper_main','paper_id',$paperId);
                if ($result && $result->school_logo != null && $result->school_logo != '') {
                    $folder = $this->folder."/";
                    $imagename = $result->school_logo;
                    unlink( $folder . $imagename );
                    $message = array("status" => "success","message" => 'נמחק בהצלחה');
                }
            }
            $this->Common_modal->update('paper_id',$paperId,'question_paper_main',$data_arr);

            $arr = [];
            foreach ($question_type as $key => $val) {
                $simpleName = strtolower($val->question_type_english);
                if (isset($_POST[$simpleName])) {
                    $arr[$simpleName] = array_filter($this->input->post($simpleName));
                } else {
                    $arr[$simpleName] = array();
                }
            }
            $response = $this->Questionnaire_Model->updateExamPaper($paperId,$arr,$question_type);
            if ($response) {
                $msg = array('status' => 'success', 'message' => "דף המבחן עודכן בהצלחה.");
            }
        } catch (Exception $x) {
            $msg = array('status' => 'error', 'message' => $x->getMessage());
        }
        echo json_encode($msg);
    }

    public function viewPaper($paperId) {
        try {
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $manage = $this->Admin_modal->isAccessRightGiven($group_id,147) ? 0 : 1;
            $view_questions = $this->Admin_modal->isAccessRightGiven($group_id,152) ? 0 : 1;
            if ($manage || $view_questions) {
                throw new Exception("אין לך הרשאה לצפות בדף המבחן.");
            }
            $paperId = base64_decode($paperId);
            $data['paper_detail'] = $this->Questionnaire_Model->viewQuestionPaper($paperId);

            $data['download_paper'] = $this->Admin_modal->isAccessRightGiven($group_id,153) ? 1 : 0;
            $data['download_scheme'] = $this->Admin_modal->isAccessRightGiven($group_id,154) ? 1 : 0;
            $this->load->view('view_exam_paper', $data);
        } catch (Exception $x) {
            redirect(base_url());
        }
    }

    public function deleteExamPaper() {
        try {
            $paperId = $this->input->post('paperId');
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $delete_ = $this->Admin_modal->isAccessRightGiven($group_id,148) ? 0 : 1;
            
            if ($delete_) {
                throw new Exception("אין לך הרשאה למחוק דף מבחן.");
            }
            $getPaper = $this->Common_modal->getAllWhere('question_paper_main','paper_id',$paperId);
            if ($getPaper && $getPaper->school_logo != null && $getPaper->school_logo != '') {
                $folder = $this->folder."/";
                $imagename = $getPaper->school_logo;
                unlink( $folder . $imagename );
            }
            $isDeleted = $this->Common_modal->delete('question_paper_main','paper_id',$paperId);
            if ($isDeleted) {
                $this->Questionnaire_Model->update_all_students_points();
            }
            $msg = array("status" => "success","message" => 'דף המבחן נמחק בהצלחה.');
        } catch (Exception $x) {
            $msg = array("status" => "error","message" => $x->getMessage());
        }
        echo json_encode($msg);
    }

    public function downloadExamPaper($paperId, $type) {
        try {
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $has_download = $this->Admin_modal->isAccessRightGiven($group_id,153) ? 0 : 1;
            if ($has_download) {
                throw new Exception("אין לך הרשאה להוריד דף מבחן.");
            }
            $paperId = base64_decode($paperId);
            $paper_detail = $this->Questionnaire_Model->viewQuestionPaper($paperId);

            $filename = str_replace(' ', '_', strtolower($paper_detail['extype_name'])).'_'.str_replace(' ', '_', strtolower($paper_detail['class_name'])).'_'.str_replace(' ', '_', strtolower($paper_detail['subject_name']));
            $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P', 'default_font' => 'dejavusans']);
            $mpdf->SetHTMLFooter('<table width="100%">
                                    <tr>
                                        <td width="33%" align="center">{PAGENO}</td>
                                    </tr>
                                </table>');
            
            $paper_temp = file_get_contents(FCPATH.'assets/template/exam_paper.html');

            $schoolLogo = 'assets/img/hat.png';
            if ($paper_detail['school_logo'] != null && $paper_detail['school_logo'] != '') {
                $schoolLogo = $paper_detail['school_logo'];
            }

            # MCQ QUESTION SET
            # ================================================================================================================================================================================
            $mcqQuestionSet = '';
            if (!empty($paper_detail['mcq_ques_ans'])) {
                $mcqQuestionSet.=   '<div style="width: 100%; margin: 0px 30px 10px 0px">
                                        <div style="width: 100%">'.$paper_detail['mcq_main_title'].'</div>
                                    </div>';
                foreach ($paper_detail['mcq_ques_ans'] as $mcqIndex => $mcqs) {
                    $mcqQueImage = '';
                    if ($mcqs->queHasImg == 1) {
                        $mcqQueImage = '<img src="'.base_url().'photos/questionaire/'.$mcqs->questionImage.'" alt="Question Image" height="120" width="120"/>';
                    }
                    $marginTopStyle = 'margin-top:15px';
                    if ($mcqIndex == 0) {
                        $marginTopStyle = '';
                    }
                    $mcqQuestionSet .= '<div>
                                            <table style="'.$marginTopStyle.'">
                                                <tr>
                                                    <td style="width: 50%">'.($mcqIndex+1).') '.$mcqs->question.'</td>
                                                    <td style="width: 50%; float: right">
                                                    '.$mcqQueImage.'
                                                    </td>
                                                </tr>
                                            </table>';
                        $mcqQuestionSet .= '<table style="margin-left: 30px">';
                        if (in_array(1, $mcqs->answerHasImgs)) {
                            foreach ($mcqs->answers as $ansIndex => $ans) {
                                $mcqAnsImage = '';
                                if ($ans->answerImage != '' && $ans->answerImage != null) {
                                    $mcqAnsImage = '<img src="'.base_url().'photos/questionaire/'.$ans->answerImage.'" alt="Answer Image" height="120" width="120"/>';
                                }
                                $answer = $ans->answer;
                                if ($type == 'download_scheme' && $ans->correct_answer == 1) {
                                    $answer = '<b><i>'.$ans->answer.'</i></b>';
                                }
                                $mcqQuestionSet .= '<tr>
                                                        <td style="width: 15%; font-size:18px;">'.($ansIndex+1).'. '.$answer.'</td>
                                                        <td style="width: 15%">'.$mcqAnsImage.'</td>
                                                    </tr>';
                            }
                        } else {
                            $mcqQuestionSet .= '<tr>';
                            foreach ($mcqs->answers as $ansIndex => $ans) {
                                $answer = $ans->answer;
                                if ($type == 'download_scheme' && $ans->correct_answer == 1) {
                                    $answer = '<b><i>'.$ans->answer.'</i></b>';
                                }
                                $mcqQuestionSet .= '<td style="width:15%; float:right">'.($ansIndex+1).'. '.$answer.'</td>';
                            }
                            $mcqQuestionSet .= '</tr>';
                        }
                        $mcqQuestionSet .= '</table></div>';
                }
            }

            # STRUCTURE QUESTION SET
            # ================================================================================================================================================================================
            $strQuestionSet = '';
            if (!empty($paper_detail['structured_ques_ans'])) {
                $strQuestionSet.=   '<div style="width: 100%; margin: 20px 30px 10px 0px">
                                        <div style="width: 100%">'.$paper_detail['structured_main_title'].'</div>
                                    </div>';
                foreach ($paper_detail['structured_ques_ans'] as $strIndex => $strucs) {
                    $strQueImage = '';
                    if ($strucs->queHasImg == 1) {
                        $strQueImage = '<img src="'.base_url().'photos/questionaire/'.$strucs->questionImage.'" alt="Question Image" height="120" width="120"/>';
                    }
                    $marginTopStyle = 'margin-top:15px';
                    if ($strIndex == 0) {
                        $marginTopStyle = '';
                    }
                    $text = preg_replace('/<p>/', '', $strucs->question,1);
                    $text =strrev ($text);
                    $text = preg_replace('/>p\/</', '', $text,1);
                    $text = strrev ($text);
                    $strQuestionSet .= '<div>
                                            <table style="'.$marginTopStyle.'">
                                                <tr>
                                                    <td style="width: 70%;">'.($strIndex+1).') '.$text.'</td>
                                                    <td style="width: 30%; float: right">
                                                    '.$strQueImage.'
                                                    </td>
                                                </tr>
                                            </table>';
                        if ($type == 'download_scheme') {
                            $strQuestionSet .= '<div style="margin:15px 0px 15px 30px;width:100%; border: 1px dashed #000; padding: 10px"><div style="font-weight:700; font-style: italic;"><u>תשובות</u></div>';
                            foreach ($strucs->answers as $strAns) {
                                $strQuestionSet .= '<div style="margin-top:10px">'.$strAns->answer.'</div>';
                            }
                        } else {
                            $strQuestionSet .= '<div style="margin-left: 30px;">';
                            for ($i = 1; $i <= count($strucs->answers); $i++) {
                                $strQuestionSet .= '<hr style="margin-top:30px;" />';
                            }
                        }
                        
                    $strQuestionSet .= '</div></div>';
                }
            }

            # ESSAY QUESTION SET
            # ================================================================================================================================================================================
            $essQuestionSet = '';
            if (!empty($paper_detail['essay_ques_ans'])) {
                $essQuestionSet.=   '<div style="width: 100%; margin: 20px 30px 10px 0px">
                                        <div style="width: 100%">'.$paper_detail['essay_main_title'].'</div>
                                    </div>';
                $essQuestionSet .= '<div style="margin-top: 20px;">';
                foreach ($paper_detail['essay_ques_ans'] as $essIndex => $essay) {
                    // $text = str_replace(['<p>', '</p>'], '', $essay->question);
                    $text = preg_replace('/<p>/', '', $essay->question,1);
                    $text =strrev ($text);
                    $text = preg_replace('/>p\/</', '', $text,1);
                    $text = strrev ($text);
                    $essQuestionSet .= '<div>'.($essIndex+1).') '.$text.'</div>';

                    if ($type == 'download_scheme') {
                        $essQuestionSet .= '<div style="margin:15px 0px 15px 30px;width:100%; border: 1px dashed #000; padding: 10px">
                                                <div style="font-weight:700; font-style: italic;"><u>תשובות</u></div>';
                        foreach ($essay->answers as $essAns) {
                            $essQuestionSet .= '<div style="margin-top:10px"><b><i>'.$essAns->answer.'</i></b></div>';
                        }
                        $essQuestionSet .= '</div>';
                    }
                }
                $essQuestionSet .= '</div>';
            }


            $paper_code = '';
            if ($paper_detail['term_id'] == 2) {
                $paper_code = 'קוד מבחן: M-' . str_pad($paper_detail['paper_id'], 5, '0', STR_PAD_LEFT);
            }
            $paper_arr = array(
                '[school_logo]' => $schoolLogo,
                '[school_name]' => $paper_detail['school_name'],
                '[subject_name]' => $paper_detail['subject_name'],
                '[exam_type]' => $paper_detail['extype_name'],
                '[class_name]' => $paper_detail['class_name'],
                '[year]' => $paper_detail['year'],
                '[total_marks]' => number_format($paper_detail['total_marks_count'],0),
                '[total_hours]' => str_pad(number_format($paper_detail['paper_duration'],0), 2, '0', STR_PAD_LEFT),
                '[paper_code]' => $paper_code,
                '[mcq_question_set]' => $mcqQuestionSet,
                '[structure_question_set]' => $strQuestionSet,
                '[essay_question_set]' => $essQuestionSet,
            );

            $paper_temp = strtr($paper_temp, $paper_arr);
            $mpdf->WriteHTML($paper_temp);
            $mpdf->Output($filename.'.pdf','D');

            $msg = array("status" => "success","message" => 'דף המבחן ירד בהצלחה.');
        } catch (Exception $ex) {
            $msg = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($msg);
    }

    public function changePaperStatus() {
        try {
            $paper_id= $this->input->post('paper_id');

            $result = $this->Common_modal->getAllWhere('question_paper_main','paper_id',$paper_id);

            if ($result) {
                if ($result->status==0) {
                    $data['status']=1;
                }else{
                    $group_id = $this->session->userdata['staff_logged_in']['group_id'];
                    $ChangeStatus= $this->Admin_modal->isAccessRightGiven($group_id,155) ? 1 : 0;
                    if ($ChangeStatus) {
                        $data['status']=0;
                    }else{
                        throw new Exception("אין לך הרשאה לשנות סטטוס.");
                    }
                }
                $this->Common_modal->update('paper_id',$paper_id,'question_paper_main',$data);
                $message = array("status" => "success","message" => "הסטטוס עודכן בהצלחה.");
            }else{
                throw new Exception("משהו השתבש. נא לנסות שנית.");
            }
        } catch (Exception $ex) {
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }
}
