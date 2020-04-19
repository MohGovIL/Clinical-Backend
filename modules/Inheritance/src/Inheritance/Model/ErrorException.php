<?php

namespace Inheritance\Model;


class ErrorException
{

    public function execute($statement){

        try {

            $ress =  $statement->execute();

        } catch (\Exception $ex) {
            if($ex){
                $mysqli = mysqli_connect($GLOBALS['host'], $GLOBALS['login'], $GLOBALS['pass'], $GLOBALS['dbase']);
                $result = $mysqli->query("SELECT * FROM multiple_db WHERE namespace = 'generaldb'");
                $row = $result->fetch_assoc();
                $mysqli->close();
                if($result->num_rows){
                        $bt = debug_backtrace();
                        $caller = array_shift($bt);
                        $file = $caller['file'];
                        $line = $caller['line'];
                        $ex = addslashes($ex);
                        $sql_syntax = addslashes($statement->getResource()->queryString);
                        $error_code = substr(md5(microtime(true)), 0, 6);
                        $mysqli = mysqli_connect($row['host'], $row['username'], my_decrypt($row['password']), $row['dbname']);
                        $result = $mysqli->query("INSERT INTO error_exception (error_code, error_log, sql_syntax, file, line)
                                                  VALUES ('$error_code', '$ex', '$sql_syntax', '$file', '$line')");
                        $mysqli->close();

                        $_SESSION['sql_errors_exception_code'][] = $error_code;
                }
            }
        }

        return $ress;
    }

}