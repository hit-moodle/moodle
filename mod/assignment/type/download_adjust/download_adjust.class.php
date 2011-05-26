<?php

//this is used to adjust the download from the type of uploadsingle/upload/online.

require_once($CFG->dirroot.'/mod/assignment/lib.php');

class download_adjust extends assignment_base {

    function download_adjust($cmid='staticonly', $assignment=NULL, $cm=NULL, $course=NULL) {
        parent::assignment_base($cmid, $assignment, $cm, $course);
    }

    function adjust($a_user, $a_userid, $into_folder = FALSE){
        global $CFG,$DB;
        require_once($CFG->libdir.'/filelib.php');
	$grades_feedback_string = NULL;	//init
        //find out the grades and feedback
        $grades_feedback = array();
        $itemid = $this->cm->id + 2;		//plus 2 to get the real itemid which is no null
        $grades_feedback = $DB->get_record("grade_grades", array("userid"=>$a_userid, "itemid"=>$itemid), 'userid, rawgrade, rawgrademax, feedback, usermodified, timemodified');//get the grades and feedback 
        $teac_user = $DB->get_record("user", array("id"=>$grades_feedback->usermodified), 'firstname, lastname');
        //if the grade exists, create _username_feedback.html
        if($grades_feedback->rawgrade){
        //store the information into strings
            $feedback_homework = get_string('course');	
            $feedback_teacher = get_string('teachers');	
            $feedback_student = get_string('username');
            $feedback_grades = get_string('grades');
            $feedback_feedback = get_string('feedback');
            $feedback_time = get_string('modified');
	    $feedback_time_value = date("Y-m-d H:i:s", $grades_feedback->timemodified);
            $grades_feedback_string = $feedback_homework.':'.$this->assignment->name.'</p>'.$feedback_teacher.':'.fullname($teac_user).'</p>'.$feedback_student.':'.fullname($a_user).'</p>'.$feedback_grades.':'.$grades_feedback->rawgrade.'/'.$grades_feedback->rawgrademax.'</p>'.$feedback_feedback.':'."$grades_feedback->feedback".'</p>'."$feedback_time".':'."$feedback_time_value".'</p>';
            //the string should be stored in html
            $grades_feedback_string = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf8"></head>'.$grades_feedback_string.'</html>';
            //create the file, put grade&feedback into _grades_feedback.html
            $fs = get_file_storage();
            $fileinfo = array(
                               'contextid' => $this->context->id, // ID of context			
                               'component' => 'mod_assignment',     // usually = table name
                               'filearea' => 'submission',     // usually = table name
                               'itemid' => 0,               // usually = ID of row in table
                               'filepath' => '/',           // any path beginning and ending in /
                               'filename' => '_'.fullname($a_user).'_'.'feedback.html'
                              );
            $file_feedback_old = $fs->get_file($fileinfo["contextid"], $fileinfo["component"], $fileinfo["filearea"], $fileinfo["itemid"], $fileinfo["filepath"], $fileinfo["filename"]);
            //if the file exists, delete it
            if($file_feedback_old){
                $file_feedback_old->delete();
            }
            //create the new file
            $fs->create_file_from_string($fileinfo, $grades_feedback_string);
            $file_feedback = $fs->get_file($fileinfo["contextid"], $fileinfo["component"], $fileinfo["filearea"], $fileinfo["itemid"], $fileinfo["filepath"], $fileinfo["filename"]);
            if($into_folder == TRUE)
                $fileinfoname = fullname($a_user)."/".$fileinfo["filename"];	//put the file into a folder
            else
                $fileinfoname = $fileinfo["filename"];
        }
	$return_fileinfo[0] = $fileinfoname;
	$return_fileinfo[1] = $file_feedback;
	return $return_fileinfo;
    }
		
}
