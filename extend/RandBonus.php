<?php
/**
 * 随机分红包
 *
 * @package   NiPHPCMS
 * @category  extend
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/03
 */
class RandBonus
{
    private $amount;

    /**
     * 构造
     * @access public
     * @return
     */
    public function __construct()
    {
        ini_set('memory_limit', '256M');
    }

    /**
     * 获得红包
     * @access public
     * @param  int $_amount 金额
     * @param  int $_total  人数
     * @param  int $_min    每人最小金额
     * @param  int $_max    每人最大金额
     * @return array
     */
    public function getBonus($_amount, $_total, $_min = 1, $_max = 200)
    {
        $result = array();
        $_amount = $_amount * 100;
        $_min = $_min * 100;
        $_max = $_max * 100;
        if ($_amount / $_total >= $_min) {
            for ($i=1; $i <= $_total; $i++) {
                $safe_total = $_total - ($i - 1);
                $safe_total = $safe_total * $_min;
                $safe_total = $_amount - $safe_total;
                $safe_total = $safe_total / ($_total - ($i - 1));
                $safe_total = floor($safe_total);

                if ($safe_total < $_min) {
                    $safe_total = $_min;
                }
                if ($safe_total > $_max) {
                    $safe_total = $_max;
                }

                $money = mt_rand($_min, $safe_total);

                $_amount = ($_amount - $money);
                $result[] = $money / 100;
            }

            if ($_amount < $_total * 150) {
                while ($_amount > 0) {
                    foreach ($result as $key => $value) {
                        $money = $value * 100;
                        if ($_amount > 0 && $money < $_max) {
                            $money++;
                            $result[$key] = $money / 100;
                            $_amount--;
                        }
                    }
                }
            }
        }

        return array(
            'amount' => $_amount,
            'total'  => $_total,
            'sum_bonus' => array_sum($result),
            'bonus'  => $result,

        );
    }
}
