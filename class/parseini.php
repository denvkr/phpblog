<?php
namespace phpBlog;
use Symfony\Component\VarDumper\VarDumper;
/**
 * Description of parseini
 *
 * @author dvkrasavin
 */
trait parseini {
    private $SysValue;
    private $iniPath;
    //put your code here
    public function parseini(){
        $this->iniPath=filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/config/config.ini';
        $this->SysValue = parse_ini_file($this->iniPath, 1);
        $GLOBALS['SysValue'] = &$this->SysValue;
        if ($GLOBALS['SysValue']['debug']['debug'])
            VarDumper::dump(array('SysValue'=>$GLOBALS['SysValue']));
    }
}
