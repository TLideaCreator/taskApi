<?php
/**
 * Created by PhpStorm.
 * User: lqh
 * Date: 2018/3/7
 * Time: 上午9:33
 */

namespace App\Constants;


class QueueName
{
    //发送短信
    const SMS_SEND_JOB = "smsSendJob";
    //比赛分数发送
    const MSM_SEND_JOB = "msmSendJob";
    //比分结算
    const CONTEST_SETTLE_FINAL_JOB = "contestSettleFinalJob";
    const CONTEST_SETTLE_KNOCKOFF_JOB = "contestSettleKnockoffJob";
    const CONTEST_SETTLE_RACE_JOB = "contestSettleRaceJob";
    //裁判邀请推送
    const PUSH_REF_INVITE_JOB = "pushRefInviteJob";
    const PUSH_REF_REPLY_JOB = "pushRefReplyJob";
    /**
     * @return array
     */
    public static function getQueueList()
    {
        return [
            self::SMS_SEND_JOB=>60,
            self::MSM_SEND_JOB=>60,
            self::PUSH_REF_INVITE_JOB=>60,
            self::PUSH_REF_REPLY_JOB=>60,
            self::CONTEST_SETTLE_FINAL_JOB=>60,
            self::CONTEST_SETTLE_KNOCKOFF_JOB=>60,
            self::CONTEST_SETTLE_RACE_JOB=>60,
        ];
    }
}
