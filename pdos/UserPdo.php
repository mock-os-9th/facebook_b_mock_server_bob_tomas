<?php

function nameCheck($name)
{
    if ( trim( $name ) === '' )
    {
        return array(false,206, "공백 제거 필요");
    }
    if(preg_match("/\s/u", $name) == true)
    {
        return array(false,206, "공백 제거 필요");
    }
    if(!is_string($name))
    {
        return array(false,200,"이름은 문자열로 입력해주세요");
    }
    if(preg_match("/^[가-힣]+$/", $name)==false && preg_match("/^[a-z|A-Z]+$/",$name)==false)
    {
        return array(false,201,"이름은 한글 또는 영어로 입력해주세요");
    }
    return array(true);
}


function emailCheck($email)
{
    if ( trim( $email ) === '' )
    {
        return array(false,206, "공백 제거 필요");
    }
    if(preg_match("/\s/u", $email) == true)
    {
        return array(false,206, "공백 제거 필요");
    }
    if(!is_string($email))
    {
        return array(false,202,"이메일 문자열로 입력해주세요");
    }
    if(preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email)==false)
    {
        return array(false,203," 이메일 형식으로 입력해주세요");
    }
    return array(true);
}

function passwordCheck($password)
{
    if ( trim( $password ) === '' )
    {
        return array(false,206, "공백 제거 필요");
    }
    if(!is_string($password))
    {
        return array(false,208,"비밀번호 문자열로 입력해주세요");
    }
    $pw = $password;
    $num = preg_match('/[0-9]/u', $pw);
    $eng = preg_match('/[a-z]/u', $pw);
    $spe = preg_match("/[_\!\@\#\%\^\&\*]/u",$pw);
    if(strlen($pw) < 8 || strlen($pw) > 20)
    {
        return array(false,205,"비밀번호는 최소 8자리 ~ 최대 20자리 이내로 입력 필요");

    }

    if(preg_match("/\s/u", $pw) == true)
    {
        return array(false,206, "공백 제거 필요");
    }

    if( $num == 0 || $eng == 0 || $spe == 0)
    {
        return array(false, 295,"영문, 숫자, 특수문자를 혼합 필요");
    }

    return array(true);
}

function phoneCheck($phone)
{
    if ( trim( $phone ) === '' )
    {
        return array(false,206, "공백 제거 필요");
    }
    if(preg_match("/\s/u", $phone) == true)
    {
        return array(false,206, "공백 제거 필요");
    }
    if(!is_string($phone))
    {
        return array(false,209,"전화번호 문자열로 입력해주세요");
    }
    if(preg_match("/[^0-9]/i", $phone)==true)
    {
        return array(false,210,"전화번호는 숫자로된 문자열로만 입력해주세요");
    }
    return array(true);
}

function sexCheck($sex)
{
    if ( trim( $sex ) === '' )
    {
        return array(false,206, "공백 제거 필요");
    }
    if(preg_match("/\s/u", $sex) == true)
    {
        return array(false,206, "공백 제거 필요");
    }
    if(!is_integer($sex) || $sex<0 || $sex>2)
    {
        return array(false,232,"성별은 0,1,2로 입력해주세요");
    }
    return array(true);
}


function birthDateCheck($birthDate)
{
    if ( trim( $birthDate ) === '' )
    {
        return array(false,206, "공백 제거 필요");
    }
    if(preg_match("/\s/u", $birthDate) == true)
    {
        return array(false,206, "공백 제거 필요");
    }
    if(!is_string($birthDate))
    {
        return array(false,215,"생년월일은 문자열로 입력해주세요");
    }
    if(!is_date($birthDate))
    {
        return array(false,216,"날짜는 YYYY-MM-DD 형식으로 입력해주세요");
    }
    return array(true);
}

function is_date($birthDate)
{
    $date_check = preg_match("/^[0-9]{4}-(0[1-9]|[1-9]|1[0-2])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/", $birthDate);
    if($date_check==true)
    {
        return true;
    }
    else
    {
        return false;
    }
}

function duplicatedEmail($email){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM users WHERE email = ?) AS exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$email]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function duplicatedPhone($phone){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM users WHERE phone = ?) AS exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$phone]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function createUserWithEmail($lastName,$firstName,$birth,$sex,$email,$password)
{
    $pdo = pdoSqlConnect();
    $query1 = "INSERT INTO users (lastName,firstName,birth,sex,email,password) VALUES (?,?,?,?,?,?);";

    $st1 = $pdo->prepare($query1);
    $st1->execute([$lastName,$firstName,$birth,$sex,$email,$password]);

    $st1 = null;
    $pdo = null;
}

function createUserWithPhone($lastName,$firstName,$birth,$phone,$sex,$password)
{
    $pdo = pdoSqlConnect();
    $query1 = "INSERT INTO users (lastName,firstName,birth, phone,sex,password) VALUES (?,?,?,?,?,?);";

    $st1 = $pdo->prepare($query1);
    $st1->execute([$lastName,$firstName,$birth,$phone,$sex,$password]);

    $st1 = null;
    $pdo = null;
}

function isValidUserWithEmail($email,$password)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM users WHERE email = ? and password = ? ) AS exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$email,$password]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function isValidUserWithPhone($phone,$password)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM users WHERE phone = ? and password = ?) AS exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$phone,$password]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function getUserIdfromEmail($email)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM users WHERE email = ?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$email]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return $res[0]["email"];
}

function getUserIdfromPhone($phone)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM users WHERE phone = ?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$phone]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return $res[0]["phone"];
}

function isPhoneOrEmail($sign)
{
    if(preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $sign)==false)
    {
        return "phone";
    }else{
        return "email";
    }
}