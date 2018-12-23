<?php
/**
 *
 *
 * @since   2018/10/31 23:27
 */


namespace App\Models;


use App\Constants\BusinessConstants;

class FeedbackQuestions extends BaseModel
{
    protected static $_TABLE = 'op_feedback_questions';

    public function countByQids($qids)
    {
        if (empty($qids)) {
            return [];
        }

        $sql = 'SELECT COUNT(*) AS num,question_id FROM ' . self::$_TABLE . ' WHERE question_id IN '
            . $this->assembleWhereInPlaceholders($qids) . '  GROUP BY question_id';
        $data = [];

        $res = $this->selectByRawSql($sql, $qids);

        foreach ($res as $v) {
            $data[$v->question_id] = $v->num;
        }

        return $data;
    }

    public function getQuestionListByOrderId($orderId)
    {
        $sql = <<<EOF
SELECT
  *
FROM
  op_questions q
  LEFT JOIN op_feedback_questions f
    ON q.`id` = f.`question_id`
WHERE f.`order_id` = ? AND q.`enable`=1 ORDER BY q.id ASC
EOF;

        return $this->selectByRawSql($sql, [$orderId]);
    }

    public function getQuestionStatById($qid)
    {
        $sql = 'SELECT choice,COUNT(*) AS num FROM ' . self::$_TABLE . ' WHERE question_id=? GROUP BY choice';
        $data = [];
        $res = $this->selectByRawSql($sql, [$qid]);
        $total = 0;
        foreach ($res as $v) {
            $data[] = [
                'choice' => $v->choice,
                'num' => $v->num
            ];
            $total += $v->num;
        }

        return [$data, $total];
    }

    public function getChoiceByOrderId($orderId)
    {
        $data = [];
        $list = $this->getListByConditions(['order_id' => $orderId], 0, BusinessConstants::MAX_RETURN_SIZE);

        foreach ($list as $v) {
            $data[$v->question_id] = $v;
        }

        return $data;
    }
}