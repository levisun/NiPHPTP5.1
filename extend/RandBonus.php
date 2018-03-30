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
    public function getBonus($_amount, $_total, $_min = null, $_max = null)
    {
        $_total  = (int) $_total;
        $average = (int) ($_amount * 100);
        $average = floor($average / $_total);

        if (is_null($_min)) {
            $_min = 100;
        } else {
            $_min = (int) ($_min * 100);
        }

        if (is_null($_max)) {
            $_max = intval($average * 3);
        } else {
            $_max = (int) ($_max * 100);
        }


        if ($_amount < $_total) {
            // 非法参数
            // 金额少于分配人数
            return false;
        } elseif ($_max < $average) {
            // 最大金额小于平均数
            // 返回最小金额与最大金额区间随机数
            for ($i=0; $i < $_total; $i++) {
                $result[$i] = rand($_min, $_max);
            }
        } elseif ($_min > $average || $_amount == $_total || $_total >= $_amount * 0.95) {
            // 最小金额大于平均数
            // 金额等于人数
            // 金额小于等于人数的十倍数
            // 返回平均值
            for ($i=0; $i < $_total; $i++) {
                $result[$i] = $average;
            }
        } else {
            // 金额转换为分
            // 减去容错值
            $_amount = ($_amount * 100) - ($_total * 10);
            $_amount = intval($_amount);

            for ($i = 0; $i < $_total; $i++) {
                // 因为小红包的数量通常是要比大红包的数量要多的，因为这里的概率要调换过来。
                // 当随机数>平均值，则产生小红包
                // 当随机数<平均值，则产生大红包
                if (rand($_min, $_max) > $average) {
                    // 在平均线上减钱
                    $temp = $_min + $this->xRandom($_min, $average);
                    $temp = $temp > $_max ? $_max : $temp;
                    $temp = $temp < $_min ? $_min : $temp;
                    $result[$i] = $temp;
                    $_amount -= $temp;
                } else {
                    // 在平均线上加钱
                    $temp = $_max - $this->xRandom($average, $_max);
                    $temp = $temp > $_max ? $_max : $temp;
                    $temp = $temp < $_min ? $_min : $temp;
                    $result[$i] = $temp;
                    $_amount -= $temp;
                }
            }

            // 如果还有余钱，则尝试加到小红包里，如果加不进去，则尝试下一个。
            while ($_amount > 0) {
                for ($i = 0; $i < $_total; $i++) {
                    if ($_amount > 0 && $result[$i] < $_max) {
                        $result[$i]++;
                        $_amount--;
                    }
                }
            }

            // 如果钱是负数了，还得从已生成的小红包中抽取回来
            while ($_amount < 0) {
                for ($i = 0; $i < $_total; $i++) {
                    if ($_amount < 0 && $result[$i] > $_min) {
                        $result[$i]--;
                        $_amount++;
                    }
                }
            }
        }

        foreach ($result as $key => $value) {
            $result[$key] = $value / 100;
        }

        return $result;
    }

    /**
     * 生产min和max之间的随机数，但是概率不是平均的，从min到max方向概率逐渐加大。
     * 先平方，然后产生一个平方值范围内的随机数，再开方，这样就产生了一种“膨胀”再“收缩”的效果。
     * @access public
     * @param  int    $_max
     * @param  int    $_min
     * @return int
     */
    private function xRandom($_max, $_min)
    {
        $sqr = intval($this->sqr($_max - $_min));
        $rand_num = rand(0, $sqr - 1);
        return intval(sqrt($rand_num));
    }

    /**
     * 求一个数的平方
     * @access private
     * @param  int     $_n
     * @return int
     */
    private function sqr($_n)
    {
        return $_n * $_n;
    }
}
