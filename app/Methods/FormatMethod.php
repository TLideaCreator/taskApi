<?php
/**
 * Created by PhpStorm.
 * User: lqh
 * Date: 2018/3/23
 * Time: 上午10:04
 */

namespace App\Methods;


class FormatMethod
{
    public static function formatDate($timestamp=-1,$format="Y-m-d H:i:s")
    {
        if($timestamp == -1){
            $timestamp = time();
        }
        $e8Time = new \DateTime("@$timestamp");
        $e8Time->setTimezone(new \DateTimeZone("Asia/Shanghai"));
        return $e8Time->format($format);
    }

    public static function checkDataDelay($startTime, $endTime, $delayDay)
    {
        $startDate = new \DateTime("@$startTime");
        $startDate->setTimezone(new \DateTimeZone("Asia/Shanghai"));
        $endDate = new \DateTime("@$endTime");
        $endDate->setTimezone(new \DateTimeZone("Asia/Shanghai"));
        $startTp = strtotime($startDate->format("Y-m-d"));
        $endTp = strtotime($endDate->format("Y-m-d"));
        $day = ($endTp-$startTp)/24/3600;
        return $delayDay <= $day;
    }

    /**
     * @param $time
     * @return array
     */
    public static function getDateDWMY($time)
    {
        $date = new \DateTime("@$time");
        $day = $date->format("Z");
        $week = $date->format("W");
        $month = $date->format("n");
        $year = $date->format("Y");
        $weekYear = ($month==1&&$week>50)?($year-1):$year;
        return ["day"=>$day,"week"=>$week,"month"=>$month,"year"=>$year,"wyear"=>$weekYear];
    }

    public static function matchPhone($phone)
    {
        $regex = '/^((13[0-9])|(14[5|7])|(15([0-3]|[5-9]))|(17[0-8])|(18[0,5-9]))\\d{8}$/';
        return preg_match($regex, $phone);
    }

    public static function matchEmail($email)
    {
        $regex = '/^[A-Za-z0-9]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/';
        return preg_match($regex, $email);
    }
    public static function matchPassword($pwd)
    {
        $regex = '/^[A-Za-z0-9_-]{6,16}$/';
        return preg_match($regex, $pwd);
    }
}