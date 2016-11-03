<?php
namespace phpBlog;
require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/class/dbconnect.php';

use phpBlog\dbconnect as dbconnect;
/**
 * Description of dbroutine
 *
 * @author dkrasavin
 */
class dbroutine {
    use dbconnect;
    
    //обновление 
    public function update_row_logged_user($Username,$Password,$Info){
        mysqli_query($this->conn,"SET @SESSIONTIME ='".date('Y-m-d G:i:s')."'") or die("".mysqli_error()."");
        mysqli_query($this->conn,"CALL ".$this->connectionParams['dbname'].".UPDATE_ROW_LOGGED_USER('".$Username."','".$Password."','".$Info."',STR_TO_DATE(@SESSIONTIME,'%Y-%m-%d %H:%i:%s'),@RETVAL)") or die("".mysqli_error()."");
        $result = mysqli_query($this->conn,"SELECT @RETVAL") or die("".mysqli_error()."");
        $retval = mysqli_fetch_array($result);
        mysqli_free_result($result);
        return $retval[0];
    }

    public function add_row_logged_user($Username,$Password,$Info){
        $result=mysqli_query($this->conn,"SELECT COUNT(login) FROM ".$this->dbname.".user_session_info WHERE login='".$Username."' AND password='".$Password."'") or die("".mysqli_error()."");
        $retval = mysqli_fetch_array($result);
        mysqli_free_result($result);
        if ($retval[0]==0) {
            mysqli_query($this->conn,"SET @SESSIONTIME ='".date('Y-m-d G:i:s')."';") or die("".mysqli_error()."");
            mysqli_query($this->conn,"INSERT INTO ".$this->connectionParams['dbname'].".user_session_info (login,password,info,session_id,login_time) VALUES ('".$Username."','".$Password."','".$Info."','".$Password."',STR_TO_DATE(@SESSIONTIME,'%Y-%m-%d %H:%i:%s'));") or die("".mysqli_error()."");
            return 1;
        } else
            return 0;
    }

    public function get_row_logged_user($Username){
        $result=mysqli_query($this->conn,"SELECT COUNT(login) FROM ".$this->connectionParams['dbname'].".user_session_info WHERE login='".$Username."'") or die("".mysqli_error()."");
        $retval = mysqli_fetch_array($result);
        mysqli_free_result($result);
        if ($retval[0]==0)
            return 0;
         else
            return 1;
    }

    public function get_total_sessions_amount(){
        $result=mysqli_query($this->conn,"SELECT count(session_id) FROM ".$this->connectionParams['dbname'].".session") or die("".mysqli_error()."");
        $retval = mysqli_fetch_array($result);
        mysqli_free_result($result);
        if ($retval[0]==0)
            return 0;
         else
            return $retval[0];
    }

    public function get_user_login_attempts($Username,$Password) {
        mysqli_query($this->conn,"CALL GET_USER_LOGIN_ATTEMPTS('".$Username."','".$Password."',@RETVAL)") or die("".mysqli_error()."");
        $result=mysqli_query($this->conn,"SELECT @RETVAL") or die("".mysqli_error()."");
        $retval = mysqli_fetch_array($result);
        mysqli_free_result($result);
        return $retval[0];
    }

}
